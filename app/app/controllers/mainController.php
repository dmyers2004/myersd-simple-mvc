<?php

use \myersd\core\controller;

class mainController extends controller {

	public function indexAction() {
		$this->data['name'] = 'Johnny Appleseed';

		echo '<pre>';
		
		var_dump($this->app->app->data());

		$this->app->view->render('index',$this->data);
	}
	
	public function configAction() {
		$c = $this->app->config->item('mongo');
		
		echo '<pre>';
		var_dump($c);
		
		$d = $this->app->config->item('mongo','dsn');
		var_dump($d);

		$d = $this->app->config->item('application');
		var_dump($d);
	
	}
	
	public function inputAction() {
		echo '<pre>';
		
		var_dump($this->app->input->data());
	
	}

}