<?php
require '../core.php';

/* send the init */
$init = [
	'runcode'=>getenv('RUNCODE'), /* you can also get this from $_SERVER */
	'default_controller'=>'main',
	'default_method'=>'index',
	'restful'=>TRUE,
	'path'=>__DIR__,
	'error_reporting'=>E_ALL,
	'display_errors'=>1,
	'modules'=>'../app/'.PATH_SEPARATOR.'../foo/',
	'server'=>$_SERVER,
	'post'=>$_POST,
	'get'=>$_GET,
	'cookie'=>$_COOKIE,
	'env'=>$_ENV,
	'files'=>$_FILES,
	'request'=>$_REQUEST,
	'put'=>[],
	'exception_error_handler'=>function($e) {
		echo 'My Exception Handler<br>';
		echo 'Error Number: '.$e->getCode().'<br>';
		echo 'Error Message: '.$e->getMessage().'<br>';
		exit(1);
	},
/*
	'session_handler'=>function(&$app) {
		// make sure the session name starts with a letter
		session_name('a'.substr(md5($app->init->modules),7,16));

		// start session
		session_start();

		// capture any session variables
		$app->session = &$_SESSION;
	},
	*/
];

require_once '../vendor/autoload.php';

/* setup the application with our config */
$app = new app($init);

/* tell the application to route and echo the results */
echo $app->route();
