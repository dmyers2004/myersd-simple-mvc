<?php 
/*

*/
$config[803] = function(\Exception $exception,$container) {
	echo 'Yo! Bro! '.$exception->getMessage();
	echo $container->config->item('bootstrap','timezone');
	exit(1);
};
