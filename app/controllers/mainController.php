<?php 

class mainController {

	public function indexAction() {
		$data['name'] = 'Johnny Appleseed';
		
		echo '<pre>';
		var_dump(mvc());
		echo '</pre>';
		
		return mvc_view('welcome',$data);
	}

	public function indexPostAjaxAction() {
		$data['name'] = print_r($_POST,true);
		return mvc_view('welcome',$data);
	}

	public function indexPostAction() {
		$data['name'] = print_r($_POST,true);
		return mvc_view('welcome',$data);
	}

}