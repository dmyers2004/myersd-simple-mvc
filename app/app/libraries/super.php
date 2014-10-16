<?php 

class super {
	protected $app;
	protected $i;

	public function __construct($app) {
		$this->app = $app;
	}

	public function output() {
		echo $this->i;
	}
	
	public function input($i) {
		$this->i = $i;	
	}

} /* end class */