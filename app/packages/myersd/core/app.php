<?php
namespace myersd\core;

class app {
	protected $data = [];
	
	public function __construct(container &$container) {
		/* You can send in 1 or more of these for mocking */
		$defaults = [
			'environment_variable'=>'ENV',
			'default_controller'=>'main',
			'default_method'=>'index',
			'restful'=>TRUE,
			'error_reporting'=>E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT,
			'display_errors'=>0,
			'server'=>$_SERVER,
			'post'=>$_POST,
			'get'=>$_GET,
			'cookie'=>$_COOKIE,
			'env'=>$_ENV,
			'files'=>$_FILES,
			'request'=>$_REQUEST,
			'session'=>@$_SESSION, /* send it in on mocking */
			'put'=>[],
		];
		
		/* merge sent in configuration over the defaults */
		$this->data = array_replace_recursive($defaults,$container->configuration);

		/* setup timezone so PHP doesn't complain */
		$this->data['timezone'] = ($this->data['timezone']) ? $this->data['timezone'] : ((!ini_get('date.timezone') ? 'UTC' : ini_get('date.timezone')));
		
		/* set our timezone */
		date_default_timezone_set($this->data['timezone']);

		/* setup our error display */
		error_reporting($this->data['error_reporting']);
		ini_set('display_errors', $this->data['display_errors']);

		/* add our modules to the search path */
		$add = [];
		
		foreach ($this->data['packages'] as $name=>$path) {
			$add[] = realpath($this->data['root'].'/'.$path.'/'.$name);
		}
		
		set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR,$add));
	}

	public function __get($name) {
		return isset($this->data[$name]) ? $this->data[$name] : NULL;
	}
	
} /* end bootstrap */