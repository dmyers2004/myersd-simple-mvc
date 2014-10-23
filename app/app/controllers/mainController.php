<?php

use \myersd\core\controller;

class mainController extends controller {

	public function indexAction() {
		$this->data['name'] = 'Johnny Appleseed';
		
		$page = $this->app->view->render('index',$this->data);

		$this->app->response->set_output($page);
	}

}