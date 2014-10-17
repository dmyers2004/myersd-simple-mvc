<?php

class Controller_Not_Found_Exception extends Exception { }
class Method_Not_Found_Exception extends Exception { }

/* give me a reference to the global service locator */
function app() {
	global $app;
	return $app;
}

class app {
	public $init; /* storage for all initial config settings */
	public $properties; /* storage for all properties built during initialization */

	public function __construct($config = NULL) {
		$defaults = [
			'config_env'=>'ENV',
			'default_controller'=>'main',
			'default_method'=>'index',
			'restful'=>TRUE,
			'path'=>__DIR__,
			'error_reporting'=>E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT,
			'display_errors'=>0,
			'modules'=>__DIR__.'/app/',
			'server'=>$_SERVER,
			'post'=>$_POST,
			'get'=>$_GET,
			'cookie'=>$_COOKIE,
			'env'=>$_ENV,
			'files'=>$_FILES,
			'request'=>$_REQUEST,
			'session'=>NULL, /* mock session */
			'put'=>[],
			'autoload'=>['core/session','core/config','core/view'],
		];

		$config = array_replace_recursive($defaults,$config);

		/* this is to store the application properties */
		$this->properties = new stdClass;

		/* which onces do we want to "namespace" */
		$copy = ['server','post','get','cookie','env','files','request','put'];

		/* loop over them and set them up */
		foreach ($copy as $c) {
			$this->properties->$c = (object)$config[$c];

			/* remove them from the config */
			unset($config[$c]);
		}

		/* this was the stuff sent in (that wasn't removed) */
		$this->init = (object)$config;

		/* setup timezone so PHP doesn't complain */
		if (!ini_get('date.timezone')) {
			/* if date.timezone not set in php.ini or not sent in config date_timezone use UTC */
			$tz = ($this->init->date_timezone) ? $this->init->date_timezone : 'UTC';

			date_default_timezone_set($tz);
		}

		/* setup our error display */
		error_reporting($this->init->error_reporting);
		ini_set('display_errors', $this->init->display_errors);

		/* add our modules to the search path */
		set_include_path(get_include_path().PATH_SEPARATOR.$this->init->modules);

		/* register the autoloader */
		spl_autoload_register([$this,'load']);

		/* register the exception handler */
		if (isset($config['exception_error_handler'])) {
			set_exception_handler($config['exception_error_handler']);
		}

		/* is this a ajax request? */
		$this->properties->is_ajax = (isset($this->properties->server->HTTP_X_REQUESTED_WITH) && strtolower($this->properties->server->HTTP_X_REQUESTED_WITH) === 'xmlhttprequest') ? 'Ajax' : FALSE;

		/* is this a https request? */
		$this->properties->https = ((!empty($this->properties->server->HTTPS) && $this->properties->server->HTTPS !== 'off') || $this->properties->server->SERVER_PORT == 443);

		/* with http(s):// and with trailing slash */
		$this->properties->base_url = trim('http'.(($this->properties->https) ? 's' : '').'://'.$this->properties->server->HTTP_HOST.dirname($this->properties->server->SCRIPT_NAME),'/');

		/* what type of request for REST or other */
		$this->properties->raw_request = ucfirst(strtolower($this->properties->server->REQUEST_METHOD));

		/*
		is this a restful app?
		if so and the request is Get than make it empty since it's the "default"
		this makes our methods follow the following fooAction (Get), fooPostAction (Post), fooPutAction (Put), fooDeleteAction (delete), etc...
		*/
		$this->properties->request = ($this->init->restful) ? $this->properties->request = ($this->properties->raw_request == 'Get') ? '' : $this->properties->raw_request : '';

		/* PHP doesn't handle PUT very well so we need to capture that manually */
		if ($this->properties->raw_request == 'Put') {
			parse_str(file_get_contents('php://input'), $this->properties->put);
		}

		/* autoload */
		foreach ($this->init->autoload as $lib) {
			$this->attach($lib);
		}

	} /* end __construct() */

	public function route($uri = NULL) {
		$this->properties->uri = ($uri) ? $uri : $this->properties->server->REQUEST_URI;

		/* get the uri (uniform resource identifier) and preform some basic clean up */
		$this->properties->uri = filter_var(trim(urldecode(substr(parse_url($this->properties->uri,PHP_URL_PATH),strlen(dirname($this->properties->server->SCRIPT_NAME)))),'/'),FILTER_SANITIZE_URL);

		/* ok let's split these up for futher processing */
		$this->properties->segments = explode('/',$this->properties->uri);

		/* setup the defaults */
		$this->properties->controller = $this->init->default_controller;
		$this->properties->classname = $this->properties->controller.'Controller';
		$this->properties->method = $this->init->default_method;
		$this->properties->parameters = [];
		$this->properties->directory = '';
		$this->properties->controller_path = '';

		/* is the url empty? if so use the defaults */
		if ($this->properties->uri != '') {
			/* keep shifting off directories until we get a match */
			foreach ($this->properties->segments as $idx=>$seg) {

				/* what controller are we testing for? */
				$this->properties->classname = str_replace('-','_',$seg).'Controller';

				if ($this->properties->controller_path = stream_resolve_include_path('controllers/'.$this->properties->directory.$this->properties->classname.'.php')) {
					/* match */
					$this->properties->controller = substr($this->properties->classname,0,-10);

					/* what's the method? */
					$this->properties->method = (isset($this->properties->segments[$idx+1])) ? str_replace('-','_',$this->properties->segments[$idx+1]) : $this->properties->method;

					/* what are the parameters? */
					$this->properties->parameters = array_slice($this->properties->segments,$idx+2);

					/* load that controller */
					include $this->properties->controller_path;

					/* get out of here we found a match! */
					break;
				}

				/* we didn't find a match yet so add that segement to the directory */
				$this->properties->directory .= $seg.'/';
			}
		}

		/* was the class loaded or is it the root controller which can be auto loaded */
		if (!class_exists($this->properties->classname)) {
			throw new Controller_Not_Found_Exception('Controller File '.$this->properties->classname.'.php Not Found',800);
		}

		/* try to instantiate the controller */
		$controller = new $this->properties->classname();

		/* what method are we going to try to call? */
		$this->properties->called = $this->properties->method.$this->properties->request.'Action';

		/* does that method even exist? */
		if (method_exists($controller, $this->properties->called)) {
			/* call the method and echo what's returned */
			return call_user_func_array(array($controller,$this->properties->called),$this->properties->parameters);
		} else {
			/* no throw a error */
			throw new Method_Not_Found_Exception('Method '.$this->properties->called.' Not Found',801);
		}
	} /* end route() */

	public function attach($name,$class_name=NULL) {
		$this->load($name,$class_name,TRUE);
	}

	/* class autoloader - you also use this for loading config files */
	public function load($name,$class_name=NULL,$attach=FALSE) {
		/* default folder */
		$folder = 'libraries';

		/* based on a suffix setup the folder */
		if (substr($name,-5) == 'model') {
			$folder = 'models';
		} elseif (substr($name,-10) == 'Controller') {
			$folder = 'controllers';
		}

		$class_name = ($class_name) ? $class_name : basename($name);

		/* is the file there? */
		if ($filename = stream_resolve_include_path($folder.'/'.$name.'.php')) {

			/* then let's load it */
			require_once $filename;

			if ($attach === TRUE && !isset($this->$class_name)) {
				$this->$class_name = new $class_name($this);
			}
		}
	} /* end load() */

	public function __call($name, $arguments) {
		if (is_callable([$this->$name,$name])) {
			return call_user_func_array([$this->$name,$name],$arguments);
		}
	}

} /* end mvc class */