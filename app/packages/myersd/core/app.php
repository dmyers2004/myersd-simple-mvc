<?php
namespace myersd\core;

class Controller_Not_Found_Exception extends \Exception {}
class Method_Not_Found_Exception extends \Exception {}

class app extends container {

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
		$this->data = array_replace_recursive($defaults,$container['configuration']);

		/* setup timezone so PHP doesn't complain */
		if (!ini_get('date.timezone')) {
			/* if date.timezone not set in php.ini or not sent in config date_timezone use UTC */
			$tz = ($this->data['date_timezone']) ? $this->data['date_timezone'] : 'UTC';

			date_default_timezone_set($tz);
		}

		/* setup our error display */
		error_reporting($this->data['error_reporting']);
		ini_set('display_errors', $this->data['display_errors']);

		/* add our modules to the search path */
		$add = [];
		
		foreach ($this->data['packages'] as $name=>$path) {
			$add[] = realpath(__DIR__.'/../../../'.$path.'/'.$name);
		}
		
		set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR,$add));
	} /* end __construct() */

} /* end bootstrap */