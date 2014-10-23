<?php 

class debugController extends \myersd\core\controller {

	public function indexAction() {
		echo '<pre>';
		var_dump($this);
	}

	public function fooAction() {
		echo 'bar!';
	}

}