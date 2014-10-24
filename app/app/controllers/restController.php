<?php

use \myersd\core\controller;

class restController extends controller {

	public function indexAction($a=NULL) {
		echo 'Get '.$a;
	}

	public function indexPostAction() {
		echo 'Post '.$this->c->input->post('a');
	}

	public function indexPutAction() {
		echo 'Put '.var_dump($this->c->input->all_put('a'));
	}

	public function indexDeleteAction($a=NULL) {
		echo 'Delete '.$a;
	}

} /* end restController */