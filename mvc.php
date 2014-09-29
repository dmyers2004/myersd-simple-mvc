<?php

class File_Not_Found_Exception extends Exception { }
class Controller_Not_Found_Exception extends Exception { }
class Method_Not_Found_Exception extends Exception { }
class View_Not_Found_Exception extends Exception { }

/* give me a reference to the global service locator */
function mvc() {
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
		if ($this->run_code == 'DEBUG') {
			error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
			ini_set('display_errors', 1);
		}

		/* make sure the session name starts with a letter */
		session_name('s'.substr(md5($this->app_path),0,16));

		/* start session */
		session_start();

		/* capture any session variables */
		$this->session = &$_SESSION;

		/* register the autoloader */
		spl_autoload_register([$this,'load']);

		/* register the exception handler */
		set_exception_handler($config['exception_error_handler']);

		/* is this a ajax request? */
		$this->is_ajax = (isset($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? 'Ajax' : FALSE;

		/* with http:// and with trailing slash */
		$this->base_url = trim('http://'.$this->server['HTTP_HOST'].dirname($this->server['SCRIPT_NAME']),'/');

		/* what type of request for REST or other */
		$this->raw_request = ucfirst(strtolower($this->server['REQUEST_METHOD']));

		/* if request is Get than make it empty since it's the "default" */
		$this->request = ($this->raw_request == 'Get') ? '' : $this->raw_request;

		/* this makes our methods follow the following fooAction (Get), fooPostAction (Post), fooPutAction (Put), fooDeleteAction (delete), etc... */

		/* PHP doesn't handle PUT very well so we need to capture that manually */
		if ($this->raw_request == 'Put') {
			parse_str(file_get_contents('php://input'), $this->put);
		}

		/* get the uri (uniform resource identifier) */
		$this->uri = $this->raw_uri = trim(urldecode(substr(parse_url($this->server['REQUEST_URI'],PHP_URL_PATH),strlen(dirname($this->server['SCRIPT_NAME'])))),'/');

		/* get the uri pieces */
		$this->segs = $this->raw_segs = explode('/',$this->uri);

		/* If they didn't include a controller and method use the defaults main & index */
		$this->controller = (!@empty($this->segs[0])) ? str_replace('-','_',strtolower(array_shift($this->segs))) : 'main';
		$this->method = (!@empty($this->segs[0])) ? str_replace('-','_',strtolower(array_shift($this->segs))) : 'index';

		/* what the Controller Name? */
		$this->classname = $this->controller.'Controller';

		/* try to instantiate (+autoload) the controller */
		$controller = new $this->classname();

		/* what method are we going to try to call? */
		$this->called = $this->method.$this->request.'Action';

		/* does that method even exist? */
		if (method_exists($controller, $this->called)) {
			/* call the method and echo what's returned */
			return call_user_func_array(array($controller,$this->called),$this->segs);
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

		/* where is our file? */
		$filename = $this->app_path.'/'.$folder.'/'.$name.'.php';

		/* is the file their? */
		if (file_exists($filename)) {
			if ($folder == 'config') {
				/* include the config file */
				include $filename;

				/* this fills $config so return it */
				return $config;
			} else {
				/* then let's load it */
				require_once $filename;
			}
		} else {

			/* simple error and exit */
			if ($folder == 'controllers') {
				throw new Controller_Not_Found_Exception('Controller '.$name.' Not Found',404);
			} else {
				throw new File_Not_Found_Exception('File '.$name.' Not Found',404);
			}
		}
	}

	/* auto load view and extract view data */
	public function view($_mvc_view_name,$_mvc_view_data=array()) {
		/* what file we looking for? */
		$_mvc_view_file = $this->app_path.'views/'.$_mvc_view_name.'.php';

		/* is it there? if not return nothing */
		if (file_exists($_mvc_view_file)) {
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
			throw new File_Not_Found_Exception('View File '.$_mvc_view_file.' Not Found',406);
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
