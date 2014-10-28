<?php
/* load composer autoloader */
$loader = require __DIR__.'/../vendor/autoload.php';

/* What environment is this server running in? */
define('ENV',isset($_SERVER['ENV']) ? $_SERVER['ENV'] : 'cli');
define('ROOT',realpath(__DIR__.'/../'));

$packages = [
	''=>'app/',
	'myersd\\'=>'packages/myersd/',
	'packagea\\'=>'packages/packagea/',
];

/* setup our composer packages and include path for controllers, config, views */
foreach ($packages as $name=>$path) {
	/* composer PSR4 autoload */
	$loader->addpsr4($name, ROOT.'/'.$path);

	/* controller, view, config (include style) autoload */
	set_include_path(get_include_path().PATH_SEPARATOR.ROOT.'/'.$path);
}

$c = new \myersd\core\container;

$c->config = $c->shared(function($c) { return new \myersd\core\config($c); });
$c->app = $c->shared(function($c) { return new \myersd\core\app($c); });
$c->event = $c->shared(function($c) { return new \myersd\core\event($c); });

$c->router = $c->shared(function($c) { return new \myersd\core\router($c); });

$c->input = $c->shared(function($c) { return new \myersd\core\input($c); });
$c->output = $c->shared(function($c) { return new \myersd\core\output($c); });

$c->log = $c->shared(function($c) { return new \myersd\libraries\log($c); });
$c->session = $c->shared(function($c) { return new \myersd\libraries\session($c); });
$c->view = $c->shared(function($c) { return new \myersd\libraries\view($c); });
$c->validate = $c->shared(function($c) { return new \myersd\libraries\validate($c); });

set_exception_handler(['\myersd\core\exceptionHandler','handleException']);
\myersd\core\exceptionHandler::load($c);

/* route and respond */
$c->router->route()->output->display();