<?php

class configController {

	public function indexAction() {
		$test = app()->config('application');

		echo '<pre>';
		var_dump($test);
		
		var_dump(app()->config->application);
	}

	public function libAction() {
		app()->attach('super');

		app()->super->input('Hello World');

		app()->super->output();
		
		return app()->view('welcome',['name'=>'John']);
	}
	
	public function stestAction() {
	 app()->session->set('name','Don');
	}
	
	public function gtestAction() {
		$s = app()->session->get('name');
		
		var_dump($s);
	}

} /* end controller */