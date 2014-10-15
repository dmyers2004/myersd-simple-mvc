<?php

class barController {

	public function indexAction() {
		$data['name'] = 'Johnny Barseed';

		return mvc()->view('welcome',$data);
	}

}