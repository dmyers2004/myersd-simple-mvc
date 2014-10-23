<?php

use \myersd\core\controller;

class controllerController extends controller {

	public function indexAction() {
		$this->app->response->set_output('Controller Controler Index Action');
	}

}