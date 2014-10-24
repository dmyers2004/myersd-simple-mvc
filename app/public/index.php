<?php
/* load composer autoloader */
$loader = require '../vendor/autoload.php';

/* this is the only include to load the $config variable based on the environment */
include '../app/config/'.getenv('ENV').'/bootstrap.php';

/* add our package */
foreach ($config['packages'] as $name=>$path) {
	$loader->add($name, realpath(__DIR__.'/../'.$path));
}

$c = new \myersd\core\container();

$c->configuration = function($c) use ($config) { return $config; };
$c->app = function($c) { return new \myersd\core\app($c); };
$c->router = function($c) { return new \myersd\core\router($c); };
$c->event = function($c) { return new \myersd\core\event($c); };

$c->input = function($c) { return new \myersd\core\input($c); };
$c->output = function($c) { return new \myersd\core\output($c); };

$c->config = function($c) { return new \myersd\libraries\config($c); };
$c->log = function($c) { return new \myersd\libraries\log($c); };
$c->session = function($c) { return new \myersd\libraries\session($c); };
$c->view = function($c) { return new \myersd\libraries\view($c); };

/* route and respond */
$c->router->route()->output->display();
