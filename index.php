<?php
require 'mvc.php';

/* send the config */
$config = [
	'runcode'=>getenv('RUNCODE'), /* you can also get this from $_SERVER */
	'default_controller'=>'main',
	'default_method'=>'index',
	'path'=>__DIR__,
	'modules'=>__DIR__.'/app/'.PATH_SEPARATOR.__DIR__.'/foo/',
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
	'session_handler'=>function(&$app) {
		/* make sure the session name starts with a letter */
		session_name('a'.substr(md5($app->init->modules),7,16));

		/* start session */
		session_start();

		/* capture any session variables */
		$app->session = &$_SESSION;
	},
];

/* setup the application with our config */
$app = new app($config);

/* tell the application to route and echo the results */
echo $app->route();
