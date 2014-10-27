<?php
namespace myersd\core;

use myersd\core\container;

class Config_Variable_Not_Found_Exception extends \Exception { }

class config {
	protected $c;
	protected $data = [];

	public function __construct(container &$container) {
		$this->c = $container;
	}

	public function item($filename,$field=NULL,$default=NULL) {
		if (!isset($this->data[$filename])) {
			$env = $this->c->app->environment_variable();			
			$env_value = $this->c->input->server($env);

			/* default empty */
			$base_config = $env_config = [];

			if ($config_filename = stream_resolve_include_path('config/'.$filename.'.php')) {
				include $config_filename;

				if (!isset($config)) {
					throw new Config_Variable_Not_Found_Exception('Config variable not found in config/'.$filename.'.php',809);
				}

				$base_config = $config;
			}

			if ($env_value) {
				if ($config_filename = stream_resolve_include_path('config/'.$env_value.'/'.$filename.'.php')) {
					include $config_filename;

					if (!isset($config)) {
						throw new Config_Variable_Not_Found_Exception('Config variable not found in config/'.$env_value.'/'.$filename.'.php',810);
					}

					$env_config = $config;
				}

			}

			$this->data[$filename] = array_replace_recursive($base_config,$env_config);
		}

		if ($field) {
			return (!isset($this->data[$filename][$field])) ? $default : $this->data[$filename][$field];
		} else {
			return $this->data[$filename];
		}
	} /* end item */

} /* end config class */