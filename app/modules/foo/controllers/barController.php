<?php

class barController {

	public function indexAction() {
		$data['name'] = 'Johnny Barseed';

		return app()->view('welcome',$data);
	}

	public function logAction() {
		app()->log->ALERT('Testing!');
		
		app()->log('warning','This is a warning');
		
		return 'done';
	}

}