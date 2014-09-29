<?php

class mainController {

	public function indexAction() {
		$data['name'] = 'Johnny Appleseed';
		return mvc()->view('welcome',$data);
	}

	public function debugAction() {
		$data['mvc'] = mvc();
		$data['c'] = mvc()->load('application.cnf');

		return mvc()->view('debug',$data);
	}

	public function indexPostAjaxAction() {
		$data['name'] = print_r($_POST,true);
		return mvc()->view('welcome',$data);
	}

	public function indexPostAction() {
		$data['name'] = print_r($_POST,true);
		return mvc()->view('welcome',$data);
	}

}
