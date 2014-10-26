<?php
/* load composer autoloader */
$loader = require '../vendor/autoload.php';

/* this is the only include to load the $config variable based on the environment */
include '../app/config/'.getenv('ENV').'/bootstrap.php';

/* add our package */
foreach ($config['packages'] as $name=>$path) {
	$loader->add($name, realpath(__DIR__.'/../'.$path));
}

/*
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();
*/

$c = new \myersd\core\container();

$c->configuration = $config;
$c->app = $c->shared(function($c) { return new \myersd\core\app($c); });

$c->router = $c->shared(function($c) { return new \myersd\core\router($c); });
$c->event = $c->shared(function($c) { return new \myersd\core\event($c); });

$c->input = $c->shared(function($c) { return new \myersd\core\input($c); });
$c->output = $c->shared(function($c) { return new \myersd\core\output($c); });

$c->config = $c->shared(function($c) { return new \myersd\libraries\config($c); });
$c->log = $c->shared(function($c) { return new \myersd\libraries\log($c); });
$c->session = $c->shared(function($c) { return new \myersd\libraries\session($c); });
$c->view = $c->shared(function($c) { return new \myersd\libraries\view($c); });

$c->validate = $c->shared(function($c) { return new \myersd\libraries\validate($c); });

/* route and respond */
$c->router->route()->output->display();
