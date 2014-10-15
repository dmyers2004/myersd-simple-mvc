<?php

class mainController {

	public function indexAction() {
		$data['name'] = 'Johnny Appleseed';

		return app()->view('index',$data);
	}

}