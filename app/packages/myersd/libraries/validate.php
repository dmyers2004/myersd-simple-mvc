<?php

namespace myersd\libraries;

use myersd\core\container;

class Validation_Forged_Exception extends \Exception {}
class Validation_Not_Found_Exception extends \Exception {}
class Validation_File_Not_Found_Exception extends \Exception {}

class validate {
	protected $c;
	protected $attached = [];
	protected $_field_data = [];
	protected $_error_array = [];
	protected $_error_prefix	= '';
	protected $_error_suffix	= '';
	protected $error_string = '';
	protected $json_options;
	protected $die_on_failure;
	protected $success;
	protected $error;
	protected $config;
	protected $internal = ['string']; /* internal already known libraries */
	protected $errors_detailed = []; /* used for debugging */

	public function __construct(container &$container) {
		$this->c = $container;

		$this->config = $this->c->config->item('validate');

		$this->json_options = $this->c->config->item('validate','json_options',JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
		$this->die_on_failure = $this->c->config->item('validate','die_on_failure',TRUE);
		$this->_error_prefix = $this->c->config->item('validate','_error_prefix','<p>');
		$this->_error_suffix = $this->c->config->item('validate','_error_suffix','</p>');

		$combined_config_functions = $this->c->config->item('validate','functions',[]);

		foreach ($this->internal as $i) {
			$filename = __DIR__.'/validate/'.$i.'.php';

			if (!file_exists($filename)) {
				throw new Validation_File_Not_Found_Exception('Could Not Find Validate File "'.$file.'"',807);
			}

			include $filename;

			$combined_config_functions = array_merge($combined_config_functions,$config);
		}

		/* quickly setup our functions from the config */
		foreach ($combined_config_functions as $name=>$function) {
			$this->attach($name,$function);
		}

		/* setup the defaults */
		$this->clear();
	}

	public function errors_detailed() {
		return $this->errors_detailed;
	}

	public function add_error($text) {
		$this->_error_array[] = $text;

		return $this;
	}

	public function error_array() {
		return $this->_error_array;
	}

	public function error_string($prefix='',$suffix='') {
		$str = '';

		// No errors, validation passes!
		if (count($this->_error_array) > 0) {
			$prefix = ($prefix === '') ? $prefix : $this->_error_prefix;
			$suffix = ($suffix === '') ? $suffix : $this->_error_suffix;

			// Generate the error string

			foreach ($this->_error_array as $val) {
				if ($val !== '') {
					$str .= $prefix.$val.$suffix.chr(10);
				}
			}
		}

		return $str;
	}

	public function set_message($text='') {
		$this->error_string = $text;

		return $this;
	}

	public function set_error_delimiters($prefix='<p>',$suffix='</p>') {
		$this->_error_prefix = $prefix;
		$this->_error_suffix = $suffix;

		return $this;
	}

	/* get last error */
	public function error($field,$prefix='',$suffix='') {
		$html = end($this->_error_array);

		return $prefix.$html.$suffix;
	}

	public function errors_json($options=NULL) {
		$options = ($options)  ? $options : $this->json_options;

		return json_encode(['err'=>TRUE,'errors'=>$this->error_string('','<br>'),'errors_array'=>$this->error_array()],$options);
	}

	public function clear() {
		$this->_error_array = [];
		$this->errors_detailed = [];
		$this->die_on_failure = FALSE;
		$this->success = FALSE;

		return $this;
	}

	public function attach($name,$func) {
		$this->attached['validate_'.$name] = $func;

		return $this;
	}

	public function die_on_fail($boolean=TRUE) {
		$this->die_on_failure = $boolean;

		return $this;
	}

	public function test($rules,&$field) {
		/* by default fail on failure */
		$this->single($rules,$field,TRUE);

		return $this;
	}

	public function filter($rules,&$field) {
		/* by default who cares on failure use this to filter input only (they all return true) */
		$this->single($rules,$field,FALSE);

		return $this;
	}

	public function single($rules,&$field,$human_label=NULL) {
		/* store rule groups in the validate config */
		$rules = (!isset($this->config[$rules])) ? $rules : $this->config[$rules];

		/* if human_label is true then die on fali */
		if ($human_label === TRUE) {
			$this->die_on_fail(TRUE);
			$human_label = NULL;
		}

		/* do we even have a rules to validate against? */
		if (!empty($rules)) {
			$rules = explode('|',$rules);

			foreach ($rules as $rule) {
				/* do we even have a rules to validate against? */
				if (empty($rule)) {
					$this->success = TRUE;
					break;
				}

				/*
				Strip the parameter (if exists) from the rule
				Rules can contain a parameter(s): max_length[5]
				*/
				$param = NULL;

				if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match)) {
					$rule = $match[1];
					$param = $match[2];
				}

				$this->success = FALSE;
				$this->error_string = '%s is not valid.';

				$attached = $this->attached;

				/* is it a attached (closure) validation function? */
				if (isset($attached['validate_'.$rule])) {
					if ($param !== NULL) {
						$this->success = $attached['validate_'.$rule]($this,$field,$param);
					} else {
						$this->success = $attached['validate_'.$rule]($this,$field);
					}

				/* is it a PHP method? */
				} elseif (function_exists($rule)) {
					/* Try PHP Functions */
					if ($param !== NULL) {
						$success = call_user_func($rule,$field,$param);
					} else {
						$success = call_user_func($rule,$field);
					}

					if (is_bool($success)) {
						$this->success = $success;
					} else {
						$field = $success;
						$this->success = TRUE;
					}
				/* rule not found */
				} else {
					throw new Validation_Not_Found_Exception('Could Not Validate Against "'.$rule.'"',808);
				}

				/* FAIL! */
				if ($this->success === FALSE) {
					/* ok let's clean out the field since it "failed" */
					$field = NULL;

					/* if the label is not provided use the rule name */
					$human_label = (empty($human_label)) ? ucwords(str_replace('_','',$rule)) : $human_label;

					/* replace %s with human label */
					$this->add_error(sprintf($this->error_string, $human_label, $param));

					/* for debugging */
					$this->errors_detailed[] = ['rule'=>$rule,'param'=>$param,'human_label'=>$human_label,'value'=>$field];

					/* they have the die on fail on then die now */
					if ($this->die_on_failure) {
						throw new Validation_Forged_Exception('Validation Forgery Detected',809);
					}

					break;
				}
			}
		}

		return $this->success;
	} /* end single */

	public function multiple($rules,&$fields) {
		$this->_field_data = &$fields;

		foreach ($rules as $fieldname=>$rule) {
			/* success fail doesn't matter until we run all the tests on all of the fields */
			$this->single($rule['rules'],$this->_field_data[$fieldname],$rule['label']);
		}

		$fields = &$this->_field_data;

		return (bool)(count($this->_error_array) == 0);
	} /* end multiple */

	public function post($rules='',$index='') {
		$field = $this->c->input->post($index);

		/* filter post and die on fail */
		$this->validate->single($rules,$field);

		return $this; /* allow chaining */
	}

} /* end validate class */