<?php

class test_aController {

	public function indexAction() {
		return 'test a';
	}

	public function method_bAction() {
		return 'method_bAction';
	}

	public function methodAction() {
		$data['name'] = 'Johnny Barseed';

		return mvc()->view('welcome',$data);
	}

	public function fooAction($bar=NULL) {
		$data['name'] = 'Johnny '.$bar;

		return mvc()->view('welcome',$data);
	}

}