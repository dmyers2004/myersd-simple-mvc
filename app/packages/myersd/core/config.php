<?php
namespace myersd\core;

use myersd\core\container;

class Config_Variable_Not_Found_Exception extends \Exception { }

class config {
	protected $c;
	protected $data = [];

	public function __construct(container &$container) {
		$this->c = $container;

		$defaults = [
			'environment'=>ENV,
			'default_controller'=>'main',
			'default_method'=>'index',
			'error_reporting'=>E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT,
			'display_errors'=>0,
			'timezone'=>'UTC',
			'request_method_format'=>'%c%a%mAction', // %c called method, %a ajax, %m request method
			'request_methods'=>['get'=>'','post'=>'Post','put'=>'Put','delete'=>'Delete','cli'=>'Cli'],
			'server'=>$_SERVER,
			'post'=>$_POST,
			'get'=>$_GET,
			'cookie'=>$_COOKIE,
			'env'=>$_ENV,
			'files'=>$_FILES,
			'request'=>$_REQUEST,
			'session'=>[],
			'put'=>[],
		];

		/* merge loaded over defaults */
		$this->data['bootstrap'] = array_replace_recursive($defaults,(array)$this->item('bootstrap'));		
	}

	/* give me everything! */
	public function all() {
		return $this->data;
	}

	/* these are NOT saved between requests */
	public function set($filename,$field) {
		$this->data[$filename][$field];

		return $this;
	}

	public function item($filename,$field=NULL,$default=NULL) {
		$env_value = ENV;

		if (!isset($this->data[$filename])) {
			/* default empty */
			$base_config = $env_config = [];

			if ($config_filename = stream_resolve_include_path('config/'.$filename.'.php')) {
				include $config_filename;

				if (!isset($config)) {
					throw new Config_Variable_Not_Found_Exception('Config variable not found in "config/'.$filename.'.php"',800);
				}

				$base_config = $config;
			}

			if ($env_value) {
				if ($config_filename = stream_resolve_include_path('config/'.$env_value.'/'.$filename.'.php')) {
					include $config_filename;

					if (!isset($config)) {
						throw new Config_Variable_Not_Found_Exception('Config variable not found in "config/'.$env_value.'/'.$filename.'.php"',801);
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