<?php

class Controller_Not_Found_Exception extends Exception { }
class Method_Not_Found_Exception extends Exception { }
class View_Not_Found_Exception extends Exception { }

/* give me a reference to the global service locator */
function app() {
	global $app;
	return $app;
}

class app {
	public $config;
	public $init;
	public $properties;

	public function __construct($config = NULL) {
		/* let's organize some of these */
		
		/* this is to store the config files once loaded */
		$this->config = new stdClass;
		
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

		/* Defaults to no errors displayed */
		error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
		ini_set('display_errors', 0);

		/* if it's DEBUG then turn the error display on */
		if ($this->init->runcode == 'DEBUG') {
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		}

		/* add our modules to the search path */
		set_include_path(get_include_path().PATH_SEPARATOR.$this->init->modules);

		/* register the autoloader */
		spl_autoload_register([$this,'load']);

		/* register the exception handler */
		set_exception_handler($config['exception_error_handler']);

		/* is this a ajax request? */
		$this->properties->is_ajax = (isset($this->properties->server->HTTP_X_REQUESTED_WITH) && strtolower($this->properties->server->HTTP_X_REQUESTED_WITH) === 'xmlhttprequest') ? 'Ajax' : FALSE;

		/* is this a https request? */
		$this->properties->https = ((!empty($this->properties->server->HTTPS) && $this->properties->server->HTTPS !== 'off') || $this->properties->server->SERVER_PORT == 443);

		/* with http(s):// and with trailing slash */
		$this->properties->base_url = trim('http'.(($this->properties->https) ? 's' : '').'://'.$this->properties->server->HTTP_HOST.dirname($this->properties->server->SCRIPT_NAME),'/');

		/* what type of request for REST or other */
		$this->properties->raw_request = ucfirst(strtolower($this->properties->server->REQUEST_METHOD));

		/* if request is Get than make it empty since it's the "default" */
		$this->properties->request = ($this->properties->raw_request == 'Get') ? '' : $this->properties->raw_request;

		/* this makes our methods follow the following fooAction (Get), fooPostAction (Post), fooPutAction (Put), fooDeleteAction (delete), etc... */

		/* PHP doesn't handle PUT very well so we need to capture that manually */
		if ($this->properties->raw_request == 'Put') {
			parse_str(file_get_contents('php://input'), $this->properties->put);
		}

		/* call the session handler */
		if ($config['session_handler']) {
			$config['session_handler']($this);
		}
	}

	public function route($uri = NULL) {
		$this->properties->uri = ($uri) ? $uri : $this->properties->server->REQUEST_URI;

		/* get the uri (uniform resource identifier) */
		$this->properties->uri = trim(urldecode(substr(parse_url($this->properties->uri,PHP_URL_PATH),strlen(dirname($this->properties->server->SCRIPT_NAME)))),'/');

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
	}

	/* class autoloader */
	public function load($name,$folder=NULL) {
		if (!$folder) {
			/* default folder */
			$folder = 'libraries';

			/* based on a suffix setup the folder */
			if (substr($name,-5) == 'model') {
				$folder = 'models';
			} elseif (substr($name,-4) == '.cnf') {
				$folder = 'config';
			} elseif (substr($name,-10) == 'Controller') {
				$folder = 'controllers';
			}
		}

		/* is the file their? */
		if ($filename = stream_resolve_include_path($folder.'/'.$name.'.php')) {
			if ($folder == 'config') {
				/* include the config file */
				include $filename;
				
				/* attach it to the app */
				$this->config->{substr($name,0,-4)} = (object)$config;

				/* this fills $config so return it */
				return $config;
			} else {
				/* then let's load it */
				require_once $filename;

				/* it's there and loaded return true */
				return TRUE;
			}
		}

		/* it's not there return false */
		return FALSE;
	}

	/* auto load view and extract view data */
	public function view($_mvc_view_name,$_mvc_view_data=array()) {
		/* is it there? */
		if ($_mvc_view_file = stream_resolve_include_path('views/'.$_mvc_view_name.'.php')) {
			/* extract out view data and make it in scope */
			extract($_mvc_view_data);

			/* start output cache */
			ob_start();

			/* load in view (which now has access to the in scope view data */
			include $_mvc_view_file;

			/* capture cache and return */
			return ob_get_clean();
		} else {

			/* simply error and exit */
			throw new View_Not_Found_Exception('View File views/'.$_mvc_view_name.'.php Not Found',802);
		}
	}

	/* redirect - cuz you always need one */
	public function redirect($url='/') {
		/* send redirect header */
		header("Location: $url");

		/* exit */
		exit(1);
	}

} /* end mvc class */