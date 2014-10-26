<?php 
/*
true is success
false is fail
*/
$config['functions'] = [

	'yyy' => function($name,$validate,&$field,$parameters=NULL) {
		$validate->set_message($name,'The %s is not a valid MD5 value.');	

		return (bool)preg_match('/^([a-fA-F0-9]{32})$/',$field);
	},

];
