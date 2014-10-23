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
	
	public function routerAction() {
		echo '<pre>';
		
		var_dump($this->app->router->controller());
		var_dump($this->app->router->classname());
		var_dump($this->app->router->method());
		var_dump($this->app->router->parameters());
		var_dump($this->app->router->directory());
		var_dump($this->app->router->controller_path());
		var_dump($this->app->router->called());

	}

	public function appAction() {
		echo '<pre>';
		
		var_dump($this->app->app->timezone());
		var_dump($this->app->app->env());
		var_dump($this->app->app->restful());
		var_dump($this->app->app->root());
	}

}