<?php
/* load composer autoloader */
$loader = require '../vendor/autoload.php';

/* this is the only include to load the $config variable based on the environment */
include '../app/config/'.getenv('ENV').'/bootstrap.php';

/* setup our composer packages and include path for controllers, config, views */
$config['include_path'] = '';

foreach ($config['packages'] as $name=>$path) {
	$config['include_path'] .= PATH_SEPARATOR.realpath($config['root'].'/'.$path.'/'.$name);
	$loader->add($name, realpath(__DIR__.'/../'.$path));
}

$defaults = [
	'environment_variable'=>'ENV',
	'default_controller'=>'main',
	'default_method'=>'index',
	'ajax_aware'=>FALSE,
	'error_reporting'=>E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT,
	'display_errors'=>0,
	'timezone'=>'UTC',
	'request_methods'=>['get'=>'','post'=>'Post','put'=>'Put','delete'=>'Delete'],
	/* you can send in these in if you need to unittest */
	'server'=>$_SERVER,
	'post'=>$_POST,
	'get'=>$_GET,
	'cookie'=>$_COOKIE,
	'env'=>$_ENV,
	'files'=>$_FILES,
	'request'=>$_REQUEST,
	'session'=>@$_SESSION,
	'put'=>[],
];

/* merge sent in configuration over the defaults */
$config = array_replace_recursive($defaults,$config);

$c = new \myersd\core\container();

$c->configuration = $config;
$c->app = $c->shared(function($c) { return new \myersd\core\app($c); });

$c->router = $c->shared(function($c) { return new \myersd\core\router($c); });
$c->event = $c->shared(function($c) { return new \myersd\core\event($c); });

$c->input = $c->shared(function($c) { return new \myersd\core\input($c); });
$c->output = $c->shared(function($c) { return new \myersd\core\output($c); });
$c->config = $c->shared(function($c) { return new \myersd\core\config($c); });

$c->log = $c->shared(function($c) { return new \myersd\libraries\log($c); });
$c->session = $c->shared(function($c) { return new \myersd\libraries\session($c); });
$c->view = $c->shared(function($c) { return new \myersd\libraries\view($c); });
$c->validate = $c->shared(function($c) { return new \myersd\libraries\validate($c); });

/* route and respond */
$c->router->route()->output->display();
