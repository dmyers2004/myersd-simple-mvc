<?php

use \myersd\core\controller;

class testController extends controller {

	public function indexAction() {
		$this->c->testing->run($this->c->app->controller(), 'is_a[\myersd\core\controller]', 'Application has a \myersd\core\controller attached');

		$this->c->testing->run($this->c->config->item('bootstrap','timezone'),'is_string','Timezone is a string');
		$this->c->testing->run($this->c->config->item('bootstrap','timezone'),'America/New_York','Timezone is a America/New_York');

		$this->c->testing->run($this->c->config->item('unitest','foo'),'bar','Config file unittest value foo = bar');



		$this->c->testing->table();
	}

}