<?php
namespace myersd\libraries;

class view extends \myersd\core\base {
	public function data($name=NULL,$value='#FOOBAR#') {
		$return = $this;

		if ($name === NULL) {
			$return = $this->data();
		} elseif ($value == '#FOOBAR#') {
			$return = $this->data($name);
		} else {
			$this->data[$name] = $value;
		}

		return $return;
	}

	public function load($_mvc_view_name=NULL,$_mvc_view_data=[],$_mvc_set_output=TRUE) {
		$_mvc_output = '';
		$_mvc_var_name = NULL;

		if (is_string($_mvc_set_output)) {
			$_mvc_var_name = $_mvc_set_output;
			$_mvc_set_output = FALSE;
		}

		$_mvc_view_data = array_merge_recursive($this->data,$_mvc_view_data);

		if ($_mvc_view_file = stream_resolve_include_path('views/'.$_mvc_view_name.'.php')) {
			/* extract out view data and make it in scope */
			extract($_mvc_view_data);

			/* start output cache */
			ob_start();

			/* load in view (which now has access to the in scope view data */
			include $_mvc_view_file;

			/* capture cache and return */
			$_mvc_output = ob_get_clean();
		} else {
			/* simply error and exit */
			throw new \Exception('View File "views/'.$_mvc_view_name.'.php" Not Found',810);
		}

		if ($_mvc_var_name != NULL) {
			$this->data[$_mvc_var_name] = $_mvc_output;
		}

		if ($_mvc_set_output) {
			$this->c->output->set_output($_mvc_output);
		}

		return $_mvc_output;
	}

} /* end view class */