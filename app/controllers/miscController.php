<?php 

class miscController {

	public function indexAction() {
		echo 'misc index action';
	}
	
	public function getAction($seg1='',$seg2='') {
		echo 'seg1: '.$seg1.' seg2:'.$seg2;	
	}

}