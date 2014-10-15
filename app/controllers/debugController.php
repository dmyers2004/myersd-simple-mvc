<?php 

class debugController {

	public function indexAction() {
		echo '<pre>';
		var_dump(app());
	}

}