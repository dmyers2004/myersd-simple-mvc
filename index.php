<?php
require 'mvc.php';

/* setup a native class object to act as a service locator */
$app = new app();

/* send the config */
$config = [
	'runcode'=>getenv('RUNCODE'),
	'default_controller'=>'main',
	'default_method'=>'index',
	'path'=>__DIR__,
	'modules'=>__DIR__.'/app/'.PATH_SEPARATOR.__DIR__.'/foo/',
	'server'=>$_SERVER,
	'post'=>$_POST,
	'get'=>$_GET,
	'cookies'=>$_COOKIE,
	'env'=>$_ENV,
	'files'=>$_FILES,
	'request'=>$_REQUEST,
	'put'=>[],
	'exception_error_handler'=>function($e) {
		echo 'My Exception<br><pre>';
		var_dump($e->getCode());
		var_dump($e->getMessage());
		die();
	},
	'session_handler'=>function(&$app) {
		/* make sure the session name starts with a letter */
		session_name('a'.substr(md5($app->config->app->modules),7,16));

		/* start session */
		session_start();

		/* capture any session variables */
		$app->session = &$_SESSION;
	},
];

/* send in the config */
echo $app->init($config)->route();
