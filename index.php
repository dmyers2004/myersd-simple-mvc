<?php
require 'mvc.php';

/* setup a native class object to act as a service locator */
$app = new mvc();

/* send the config */
$config = [
	'runcode'=>getenv('RUNCODE'),
	'path'=>__DIR__,
	'app_path'=>__DIR__.'/app/',
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
