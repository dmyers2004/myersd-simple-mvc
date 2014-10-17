<?php
require '../core.php';

/* send the init */
$init = [
	'config_env'=>'ENV',
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
	'session'=>NULL, /* if you want to mock in some put a array here */
	'put'=>[],
	'autoload'=>['core/session','core/config','core/view'],
	'exception_error_handler'=>function($exception) {
		echo('<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Syntax Error</title></head><body><code>
			Version: PHP '.phpversion().'<br>
			Memory: '.floor(memory_get_peak_usage()/1024).'K of '.ini_get('memory_limit').' used<br>
			Error Code: '.$exception->getCode().'<br>
			Error Message: '.$exception->getMessage().'<br>
			File: '.$exception->getFile().'<br>
			Line: '.$exception->getLine().'<br>
			</code></body></html>');
		exit(1);
	},
];

require_once '../vendor/autoload.php';

/* setup the application with our config */
$app = new app($init);

/* tell the application to route and echo the results */
echo $app->route();

