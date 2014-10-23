<?php
namespace myersd\core;

class Controller_Not_Found_Exception extends \Exception {}
class Method_Not_Found_Exception extends \Exception {}

class app {
	protected static $data = [];
	
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
		self::$data = array_replace_recursive($defaults,$container->configuration);

		/* setup timezone so PHP doesn't complain */
		self::$data['timezone'] = (self::$data['timezone']) ? self::$data['timezone'] : ((!ini_get('date.timezone') ? 'UTC' : ini_get('date.timezone')));
		
		/* set our timezone */
		date_default_timezone_set(self::$data['timezone']);

		/* setup our error display */
		error_reporting(self::$data['error_reporting']);
		ini_set('display_errors', self::$data['display_errors']);

		/* add our modules to the search path */
		$add = [];
		
		foreach (self::$data['packages'] as $name=>$path) {
			$add[] = realpath(self::$data['root'].'/'.$path.'/'.$name);
		}
		
		set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR,$add));
	} /* end __construct() */

	public function __get($name) {
		return isset(self::$data[$name]) ? self::$data[$name] : NULL;
	}
	
} /* end bootstrap */