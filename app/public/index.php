<?php
/* load composer autoloader */
$loader = require __DIR__.'/../vendor/autoload.php';

/* define some LOW level stuff */
define('ENV',$_SERVER['ENV']); /* this could be set VIA htaccess for example */
define('ROOT',realpath(__DIR__.'/..')); /* where is the root of this entire application? */

$packages = [
	''=>'app/',
	'myersd\\'=>'packages/myersd/'
];

/* setup our composer packages and include path for controllers, config, views */
foreach ($packages as $name=>$path) {
	/* composer PSR4 autoload */
	$loader->addpsr4($name, realpath(__DIR__.'/../'.$path));
	
	/* controller, view, config autoload */
	set_include_path(get_include_path().PATH_SEPARATOR.realpath(ROOT.'/'.$path.'/'.$name));
}

$c = new \myersd\core\container();

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

/* route and respond */
$c->router->route()->output->display();