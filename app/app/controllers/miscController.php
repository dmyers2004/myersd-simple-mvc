<?php 

use \myersd\core\controller;

class miscController extends controller {

	public function input_mapPostAction() {
		$data = [];
		
		$fields = 'name as foo,bar as chow default 123,test';
		
		$this->c->input->map($fields,$data);
		
		var_dump($data);
	}

	public function indexAction() {
		echo 'misc index action';
	}
	
	public function getAction($seg1='',$seg2='') {
		echo 'seg1: '.$seg1.' seg2:'.$seg2;	
	}

	public function debugAction() {
		$data['mvc'] = app();
		$data['c'] = app()->load('application.cnf');
		

		return app()->view('debug',$data);
	}

	public function indexPostAjaxAction() {
		$data['name'] = print_r($_POST,true);
		return app()->view('welcome',$data);
	}

	public function indexPostAction() {
		$data['name'] = print_r($_POST,true);
		return app()->view('welcome',$data);
	}

	public function mongoAction() {
		$config = app()->load('mongo.cnf');
		
		$qb = new \MongoQB\Builder(['dsn'=>$config->dsn]);
		
		$qb->insert('test_select', array(
			'firstname'	=>	'John',
			'surname'	=>	'Doe',
			'likes'	=>	array(
				'whisky',
				'gin',
				'rum'
			),
			'age'	=>	22
		));
		
	}
	
	public function testAction() {
		return 'hello from the post';
	}

} /* end controller */