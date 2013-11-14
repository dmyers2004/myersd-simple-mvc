<?php

/* setup a native class object to act as a service locator */
$mvc = new stdclass();

/* set this up so PHP doesn't complain */
date_default_timezone_set('America/New_York');

/* get the run code from the htaccess file */
$mvc->run_code = getenv('RUNCODE');

/* Defaults to no errors displayed */
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
ini_set('display_errors', 0);

/* if it's DEBUG then turn the error display on */
if ($mvc->run_code == 'DEBUG') {
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	ini_set('display_errors', 1);
}

/* start session */
session_start();

/* Where is this bootstrap file */
$mvc->path = __DIR__;

/* app path */
$mvc->app = $mvc->path.'/app/';

/* register the autoloader */
spl_autoload_register('mvc_load');

/* is this a ajax request? */
$mvc->is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? 'Ajax' : false;

/* with http:// and with trailing slash */
$mvc->base_url = trim('http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']),'/');

/* The GET method is default so controller methods look like openAction, others are handled directly openPostAction, openPutAction, openDeleteAction, etc... */
$mvc->raw_request = ucfirst(strtolower($_SERVER['REQUEST_METHOD']));
$mvc->request = ($mvc->raw_request == 'Get') ? '' : $mvc->raw_request;

/* Put PUT posted into into $_POST */
if ($mvc->raw_request == 'Put') {
	parse_str(file_get_contents('php://input'), $_POST);
}

/* get the uri (uniform resource identifier) */
$mvc->uri = $mvc->raw_uri = trim(urldecode(substr(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),strlen(dirname($_SERVER['SCRIPT_NAME'])))),'/');

/* get the uri pieces */
$mvc->segs = $mvc->raw_segs = explode('/',$mvc->uri);

/* If they didn't include a controller and method use the defaults  main & index */
$mvc->controller = (!@empty($mvc->segs[0])) ? strtolower(array_shift($mvc->segs)) : 'main';
$mvc->method = (!@empty($mvc->segs[0])) ? strtolower(array_shift($mvc->segs)) : 'index';

/* try to auto load the controller - will throw an error you must catch if it's not there */
$mvc->classname = $mvc->controller.'Controller';

/* instantiate it */
$controller = new $mvc->classname();

/* what method are we going to try to call? */
$mvc->called = $mvc->method.$mvc->request.$mvc->is_ajax.'Action';

/* does that method even exist? */
if (method_exists($controller, $mvc->called)) {
	/* call the method and echo what's returned */
	echo call_user_func_array(array($controller,$mvc->called),$mvc->segs);
} else {
	/* if the method isn't there die gracefully */
	mvc_die_error('Method '.$mvc->called.' Not Found');	
}

/* give me a reference to the global service locator */
function mvc() {
	global $mvc;
	return $mvc;
}

/* class autoloader */
function mvc_load($name,$folder=null) {
	if (!$folder) {
		$folder = 'libraries';
		
		if (substr($name,-5) == 'model') {
			$folder = 'models';
		} elseif (substr($name,-10) == 'Controller') {
			$folder = 'controllers';
		}
	}

	$filename = mvc()->app.$folder.'/'.$name.'.php';

	/* is the file their? */
	if (file_exists($filename)) {
		/* then let's load it */
		require_once($filename);
	} else {
		/* simple error and exit */
		mvc_die_error('File '.$name.' Not Found');	
	}
}

/* auto load view and extract view data */
function mvc_view($_mvc_view_name,$_mvc_view_data=array()) {
	/* what file we looking for? */
	$_mvc_view_file = mvc()->app.'views/'.$_mvc_view_name.'.php';

	/* is it there? if not return nothing */
	if (file_exists($_mvc_view_file)) {
		/* extract out view data and make it in scope */
		extract($_mvc_view_data);

		/* start output cache */
		ob_start();

		/* load in view (which now has access to the in scope view data */
		require($_mvc_view_file);

		/* capture cache and return */
		return ob_get_clean();

	} else {

		/* if not found die with some info */
		mvc_die_error('View File '.$_mvc_view_file.' Not Found');	
	}
}

/* single die method */
function mvc_die_error($str) {
	if (mvc()->run_code == 'DEBUG') {
		/* show our error and die */
		die($str);
	} else {
		/* log it?? */
		
		redirect('/');		
	}
}

/* redirect - cuz you always need one */
function redirect($url='/') {
	/* send redirect header */
	header("Location: $url");

	/* exit */
	exit;
}
