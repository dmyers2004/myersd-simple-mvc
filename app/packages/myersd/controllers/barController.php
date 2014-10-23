<?php

class barController extends \myersd\core\controller {

	public function indexAction() {
		echo 'Bar Controller Index Action';
	}

	public function logAction() {
		app()->log->ALERT('Testing!');
		
		app()->log('warning','This is a warning');
		
		return 'done';
	}
	
	public function sessionaAction() {
		$this->app->session->set('name','Don Myers');
		$this->app->session->set_flashdata('name','flash data');

		echo 'done';

		var_dump($this->app->session->all());
	}
	
	public function sessionbAction() {
		echo $this->app->session->get('name');
	}
	
	public function session_allAction() {
		var_dump($this->app->session->all());
	}

}