<?php 

class debugController extends \myersd\core\controller {

	public function indexAction() {
		//$this->app->load('test.cnf');
	
		echo '<pre>';
		var_dump($this->container);
	}

	public function fooAction() {
		echo 'bar!';
	}

}