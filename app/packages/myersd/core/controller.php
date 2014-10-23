<?php 
namespace myersd\core;

class controller {
	protected $app;
	protected $data = [];
	
	public function __construct(container $container) {
		$this->app = $container;
		
		if (method_exists($this,'init')) {
			$this->init();
		}
	}
}

