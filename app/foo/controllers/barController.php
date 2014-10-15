<?php

class barController {

	public function indexAction() {
		$data['name'] = 'Johnny Barseed';

		return app()->view('welcome',$data);
	}

}