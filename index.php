<?php

/* setup a native class object to act as a service locator */
$mvc = new stdclass();

/* Defaults to no errors displayed */
ini_set('display_errors','Off');

/* start session */
session_start();

/* Where is this bootstrap file */
$mvc->path = __DIR__;

/* app path */
$mvc->app = $mvc->path.'/app/';

/* register the autoloader */
spl_autoload_register('mvc_autoloader');

/* is this a ajax request? */
$mvc->is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? 'Ajax' : false;

/* with http:// and with trailing slash */
$mvc->base_url = trim('http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']),'/');

/* The GET method is default so controller methods look like openAction, others are handled directly openPostAction, openPutAction, openDeleteAction, etc... */
$mvc->raw_request = ucfirst(strtolower($_SERVER['REQUEST_METHOD']));
$mvc->request = ($mvc->raw_request == 'Get') ? '' : $mvc->raw_request;

/* Put ANY (POST, PUT, DELETE) posted into into $_POST */
parse_str(file_get_contents('php://input'), $_POST);

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

$method = $mvc->method.$mvc->request.$mvc->is_ajax.'Action';

if (method_exists($controller, $method)) {
	/* call the method - will throw an error you must catch if it's not there */
	echo call_user_func_array(array($controller,),$mvc->segs);
} else {
	mvc_die_error("Method %s Not Found",$method);	
}

/* give me a reference to the global service locator */
function mvc() {
	global $mvc;
	return $mvc;
}

/* class autoloader */
function mvc_autoloader($name) {
	/* autoload controllers or libraries */
	$filename = mvc()->app.'/'.((substr($name,-10) != 'Controller') ? 'libraries' : 'controllers').'/'.strtolower($name).'.php';

	/* is the file their? */
	if (file_exists($filename)) {
		/* then let's load it */
		require_once($filename);
	} else {
		/* simple error and exit */
		mvc_die_error("File %s Not Found",$name);	
	}
}

/* auto load view and extract view data */
function mvc_view($_mvc_view_name,$_mvc_view_data=array()) {
	/* what file we looking for? */
	$_mvc_view_file = mvc()->app.'/views/'.$_mvc_view_name.'.php';

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
		mvc_die_error("View File %s Not Found",$_mvc_view_file);	
	}
}

/* single die method */
function mvc_die_error($string,$replace) {
	/* don't show to much unless env var = DEBUG */
	$replace = (getenv('RUNCODE') == 'DEBUG') ? $replace : '';

	/* show our error and die */
	die(sprintf($string,$replace));
}

/* redirect - cuz you always need one */
function redirect($url='/') {
	/* send redirect header */
	header("Location: $url");

	/* exit */
	exit;
}
