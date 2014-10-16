<?php

class configController {

	public function indexAction() {
		app()->attach('config');
	
		$test = app()->config('application');

		echo '<pre>';
		var_dump($test);
		
		var_dump(app()->config->application);
	}

	public function libAction() {
		app()->attach('super');

		app()->super->input('Hello World');

		app()->super->output();
		
		app()->attach('view');
		
		return app()->view('welcome',['name'=>'John']);
	}

} /* end controller */