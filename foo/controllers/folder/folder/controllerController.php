<?php

class controllerController {

	public function indexAction() {
		$data['name'] = 'Johnny Barseed';

		return mvc()->view('welcome',$data);
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