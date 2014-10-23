<?php

use \myersd\core\controller;

class mainController extends controller {

	public function indexAction() {
		echo 'mainController indexAction';
	}

	public function methodAction() {
		echo 'mainController methodAction';
	}

	public function viewAction() {
		$this->data['name'] = 'Johnny Appleseed';

		$this->c->view->render('index',$this->data);
	}
	
	public function configAction() {
		$c = c()->config->item('mongo');
		
		echo '<pre>';
		var_dump($c);
		
		$d = c()->config->item('mongo','dsn');
		var_dump($d);

		$d = c()->config->item('application');
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