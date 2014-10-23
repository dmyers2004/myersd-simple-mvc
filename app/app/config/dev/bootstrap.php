<?php 

$config = [
	'timezone'=>'America/New_York',
	'error_reporting'=>E_ALL,
	'display_errors'=>1,
	'root'=>realpath(__DIR__.'/../../..'),
	'packages'=>[
		''=>'app',
		'myersd'=>'packages'
	],
];
