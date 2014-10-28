<?php
namespace myersd\core;

class app {
	protected $c;
	protected $controller;

	/* setup a few basic items */
	public function __construct(container &$container) {
		$this->c = $container;

		/* set our timezone */
		date_default_timezone_set($this->c->config->item('bootstrap','timezone'));

		/* setup our error display */
		error_reporting($this->c->config->item('bootstrap','error_reporting'));
		ini_set('display_errors',$this->c->config->item('bootstrap','display_errors'));
	}
	
	/* get & set the controller object */
	public function controller(&$obj=NULL) {
		$return = $this;
		
		if ($obj == NULL) {
			$return = $this->controller;
		} else {
			$this->controller = $obj;
		}	
		
		return $return;
	}

} /* end bootstrap */