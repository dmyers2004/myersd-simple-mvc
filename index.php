<?php
require 'common.php';

/* setup a native class object to act as a service locator */
$app = new mvc();

/* set this up so PHP doesn't complain */
date_default_timezone_set('America/New_York');

/* send the config */
$config = [
	'runcode'=>getenv('RUNCODE'),
	'path'=>__DIR__,
	'app_path'=>__DIR__.'/app/',
	'session_id'=>'woody',
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
	}
];

/* send in the config */
echo $app->route($config);
