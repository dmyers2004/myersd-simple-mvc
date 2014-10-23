<?php
/* load composer autoloader */
$loader = require '../vendor/autoload.php';

/* this is the only include to load the $config variable based on the environment */
include '../app/config/'.getenv('ENV').'/bootstrap.php';

/* add our package */
foreach ($config['packages'] as $name=>$path) {
	$loader->add($name, realpath(__DIR__.'/../'.$path));
}

/* the di container */
$container = new \myersd\core\container($config);

/* setup the application with our config */
$container->app = new \myersd\core\app($container);

$container->input = new \myersd\core\input($container);
$container->output = new \myersd\core\output($container);

$container->config = new \myersd\libraries\config($container);
$container->view = new \myersd\libraries\view($container);
$container->log = new \myersd\libraries\log($container);
$container->session = new \myersd\libraries\session($container);

$container->log->emergency('Hello There');

$container->router = new \myersd\core\router($container);

/* route and respond */
$container->router->route()->output->_display();