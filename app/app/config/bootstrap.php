<?php 

if (ENV == 'dev') {
	$config = [
		'timezone'=>'America/New_York',
		'error_reporting'=>E_ALL,
		'display_errors'=>1,
		'packages'=>[
			''=>'app',
			'myersd'=>'packages'
		],
	];
}