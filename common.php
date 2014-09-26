<?php

class File_Not_Found_Exception extends Exception { }
class Method_Not_Found_Exception extends Exception { }
class View_Not_Found_Exception extends Exception { }

/* give me a reference to the global service locator */
function mvc() {
	global $app;
	return $app;
}

class mvc {
	public function route($config) {
		/* setup the runcode */
		$this->run_code = $config['runcode'];
		
		/* setup config variables */
		$this->post = $config['post'];
		$this->get = $config['get'];
		$this->cookies = $config['cookies'];
		$this->env = $config['env'];
		$this->files = $config['files'];
		$this->request = $config['request'];
		$this->put = [];

		/* Defaults to no errors displayed */
		error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
		ini_set('display_errors', 0);

		/* if it's DEBUG then turn the error display on */
		if ($this->run_code == 'DEBUG') {
			error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
			ini_set('display_errors', 1);
		}

		/* start session */
		session_id($config['session_id']);
		session_start();
		$this->session = &$_SESSION;

		/* Where is this bootstrap file */
		$this->path = $config['path'];

		/* app path */
		$this->app = $config['app_path'];

		/* register the autoloader */
		spl_autoload_register([$this,'load']);

		/* register the exception handler */
		set_exception_handler($config['exception_error_handler']);

		/* is this a ajax request? */
		$this->is_ajax = (isset($config['server']['HTTP_X_REQUESTED_WITH']) && strtolower($config['server']['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? 'Ajax' : FALSE;

		/* with http:// and with trailing slash */
		$this->base_url = trim('http://'.$config['server']['HTTP_HOST'].dirname($config['server']['SCRIPT_NAME']),'/');

		/* The GET method is default so controller methods look like openAction, others are handled directly openPostAction, openPutAction, openDeleteAction, etc... */
		$this->raw_request = ucfirst(strtolower($config['server']['REQUEST_METHOD']));
		$this->request = ($this->raw_request == 'Get') ? '' : $this->raw_request;

		/* Put PUT posted into into $_POST */
		if ($this->raw_request == 'Put') {
			parse_str(file_get_contents('php://input'), $this->put);
		}

		/* get the uri (uniform resource identifier) */
		$this->uri = $this->raw_uri = trim(urldecode(substr(parse_url($config['server']['REQUEST_URI'],PHP_URL_PATH),strlen(dirname($config['server']['SCRIPT_NAME'])))),'/');

		/* get the uri pieces */
		$this->segs = $this->raw_segs = explode('/',$this->uri);

		/* If they didn't include a controller and method use the defaults  main & index */
		$this->controller = (!@empty($this->segs[0])) ? str_replace('-','_',strtolower(array_shift($this->segs))) : 'main';
		$this->method = (!@empty($this->segs[0])) ? str_replace('-','_',strtolower(array_shift($this->segs))) : 'index';

		/* try to auto load the controller - will throw an error you must catch if it's not there */
		$this->classname = $this->controller.'Controller';

		/* instantiate it */
		$controller = new $this->classname();

		/* what method are we going to try to call? */
		$this->called = $this->method.$this->request.'Action';

		/* does that method even exist? */
		if (method_exists($controller, $this->called)) {
			/* call the method and echo what's returned */
			return call_user_func_array(array($controller,$this->called),$this->segs);
		} else {
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
		$filename = $this->app.$folder.'/'.$name.'.php';

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
			throw new File_Not_Found_Exception('File '.$name.' Not Found',404);
		}
	}

	/* auto load view and extract view data */
	public function view($_mvc_view_name,$_mvc_view_data=array()) {
		/* what file we looking for? */
		$_mvc_view_file = $this->app.'views/'.$_mvc_view_name.'.php';

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