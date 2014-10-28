<?php 

if (ENV == 'dev' || ENV == 'unittest' || ENV == 'cli') {
	$config = [
		'timezone'=>'America/New_York',
		'error_reporting'=>E_ALL,
		'display_errors'=>1,
	];
}

if (ENV == 'cli') {
	$config['server'] = [
		'HTTP_X_REQUESTED_WITH' => '',
		'HTTPS' => '',
		'SERVER_PORT' => 80,
		'HTTP_HOST' => 'mvc.vcap.me',
		'SCRIPT_NAME' => '/index.php',
		'REQUEST_METHOD' => 'cli',
		'REQUEST_URI' => $_SERVER['argv'][1],
	];
}