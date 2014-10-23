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
$container = new \myersd\core\container();

/* setup the application with our config */
$container->app = new \myersd\core\app($container,$config);

$container->request = new \myersd\core\request($container);
$container->response = new \myersd\core\response($container);

$container->view = new \myersd\libraries\view($container);
$container->config = new \myersd\libraries\config($container);

$container->router = new \myersd\core\router($container);

$container->response->_display();
