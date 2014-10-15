<?php 

class debugController {

	public function indexAction() {
		app()->load('test.cnf');
	
		echo '<pre>';
		var_dump(app());
	}

}