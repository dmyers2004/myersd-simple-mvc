<?php

class mainController {

	public function indexAction() {
		$data['name'] = 'Johnny Appleseed';

		return mvc()->view('index',$data);
	}

}