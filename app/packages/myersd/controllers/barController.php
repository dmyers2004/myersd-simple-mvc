<?php

class barController {

	public function indexAction() {
		echo 'Bar Controller Index Action';
	}

	public function logAction() {
		app()->log->ALERT('Testing!');
		
		app()->log('warning','This is a warning');
		
		return 'done';
	}

}