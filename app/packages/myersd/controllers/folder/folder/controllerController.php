<?php

use \myersd\core\controller;

class controllerController extends controller {

	public function indexAction() {
		$this->c->output->set_output('Controller Controler Index Action');
	}

}