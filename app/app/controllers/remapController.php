<?php

use \myersd\core\controller;

class remapController extends controller {

	public function _remap($method,$params=[]) {
		$this->c->output->set_output('Method: '.$method.' '.print_r($params,TRUE));
	}
	
	public function _output($output) {
		echo 'redirected output '.$output;
	}

} /* end remap */
