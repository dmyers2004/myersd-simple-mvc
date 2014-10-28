<?php

class packageController extends \myersd\core\controller {

	public function indexAction() {
		$this->data['foo'] = $this->c->config->item('packagea','foobar','default!');
		$this->data['bar'] = $this->c->config->item('packagea','barfoo','empty!');
		
		$this->c->view->load('foobar/index',$this->data);
	}

} /* end */