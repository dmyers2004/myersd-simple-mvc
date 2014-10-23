<?php 
namespace myersd\core;

class controller {
	protected $data = [];
	protected $c;
	
	public function __construct(container &$container) {
		$this->c = $container;

		if (method_exists($this,'init')) {
			$this->init();
		}
	} /* end __construct */
} /* end controller */


