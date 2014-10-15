<?php 

class miscController {

	public function indexAction() {
		echo 'misc index action';
	}
	
	public function getAction($seg1='',$seg2='') {
		echo 'seg1: '.$seg1.' seg2:'.$seg2;	
	}

	public function debugAction() {
		$data['mvc'] = mvc();
		$data['c'] = mvc()->load('application.cnf');

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

}