<?php

class File_Not_Found_Exception extends Exception { }
class Controller_Not_Found_Exception extends Exception { }
class Method_Not_Found_Exception extends Exception { }
class View_Not_Found_Exception extends Exception { }

/* give me a reference to the global service locator */
function app() {
	global $app;
	return $app;
}

class mvc {
	public $put = [];

	public function route($config) {
		/* now required by PHP */
		if (!ini_get('date.timezone')) {
			/* if date.timezone not set in php.ini or not sent in config date_timezone use UTC */
			$tz = ($config['date_timezone']) ? $config['date_timezone'] : 'UTC';

			date_default_timezone_set($tz);
		}

		/* attach all of the config variables to $app */
		foreach ($config as $key=>$value) {
			$this->$key = $value;
		}

		/* Defaults to no errors displayed */
		error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
		ini_set('display_errors', 0);

		/* if it's DEBUG then turn the error display on */
		if ($this->runcode == 'DEBUG') {
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		}

		/* add our application folder(s) to the search path */
		set_include_path(get_include_path().PATH_SEPARATOR.$this->modules);

		/* register the autoloader */
		spl_autoload_register([$this,'load']);
		
		/* register the exception handler */
		set_exception_handler($config['exception_error_handler']);

		/* is this a ajax request? */
		$this->is_ajax = (isset($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? 'Ajax' : FALSE;

		/* is this a https request? */
		$this->https = ((!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') || $this->server['SERVER_PORT'] == 443);

		/* with http:// and with trailing slash */
		$this->base_url = trim('http'.($this->https ? 's' : '').'://'.$this->server['HTTP_HOST'].dirname($this->server['SCRIPT_NAME']),'/');

		/* what type of request for REST or other */
		$this->raw_request = ucfirst(strtolower($this->server['REQUEST_METHOD']));

		/* if request is Get than make it empty since it's the "default" */
		$this->request = ($this->raw_request == 'Get') ? '' : $this->raw_request;

		/* this makes our methods follow the following fooAction (Get), fooPostAction (Post), fooPutAction (Put), fooDeleteAction (delete), etc... */

		/* PHP doesn't handle PUT very well so we need to capture that manually */
		if ($this->raw_request == 'Put') {
			parse_str(file_get_contents('php://input'), $this->put);
		}

		/* call the session handler */
		$config['session_handler']($this);

		/* get the uri (uniform resource identifier) */
		$this->uri = trim(urldecode(substr(parse_url($this->server['REQUEST_URI'],PHP_URL_PATH),strlen(dirname($this->server['SCRIPT_NAME'])))),'/');

		/* ok let's split these up for futher processing */
		$this->segments = explode('/',$this->uri);
		
		/* setup the defaults */
		$this->controller = $this->default_controller;
		$this->classname = $this->controller.'Controller';
		$this->method = $this->default_method;
		$this->parameters = [];
		$this->directory = '';
		$this->controller_path = '';
		
		/* is the url empty? if so use the defaults */
		if ($this->uri != '') {
			/* keep shifting off directories until we get a match */
			foreach ($this->segments as $idx=>$seg) {
				
				/* what controller are we testing for? */
				$this->classname = str_replace('-','_',$seg).'Controller';
				
				if ($controller_path = stream_resolve_include_path('controllers/'.$this->directory.$this->classname.'.php')) {
					/* match */
					$this->controller = substr($this->classname,0,-10);
					
					/* what's the method? */
					$this->method = (isset($this->segments[$idx+1])) ? str_replace('-','_',$this->segments[$idx+1]) : $this->method;
					
					/* what are the parameters? */				
					$this->parameters = array_slice($this->segments,$idx+2);
					
					/* load that controller */
					include $controller_path;
					
					/* get out of here we found a match! */
					break;
				}
				
				/* we didn't find a match yet so add that segement to the directory */
				$this->directory .= $seg.'/';
			}
		}

		if (!class_exists($this->classname)) {
			throw new Controller_Not_Found_Exception('Controller File '.$this->classname.'.php Not Found',408);
		}

		/* try to instantiate the controller */
		$controller = new $this->classname();

		/* what method are we going to try to call? */
		$this->called = $this->method.$this->request.'Action';

		/* does that method even exist? */
		if (method_exists($controller, $this->called)) {
			/* call the method and echo what's returned */
			return call_user_func_array(array($controller,$this->called),$this->parameters);
		} else {
			/* no throw a error */
			throw new Method_Not_Found_Exception('Method '.$this->called.' Not Found',405);
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

			/* simple error and exit */
			throw new File_Not_Found_Exception('View File views/'.$_mvc_view_name.'.php Not Found',406);
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