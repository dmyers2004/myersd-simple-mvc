<?php

use \myersd\core\controller;

class mainController extends controller {

	public function indexAction() {
		$this->data['name'] = 'Johnny Appleseed';
		
		$this->app->view->render('index',$this->data);
	}

}