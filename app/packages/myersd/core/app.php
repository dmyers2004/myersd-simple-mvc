<?php
namespace myersd\core;

class app {
	protected $c;
	protected $controller;

	/* setup a few basic items */
	public function __construct(container &$container) {
		$this->c = $container;

		/* setup timezone so PHP doesn't complain */
		$tz = $this->c->config->item('bootstrap','timezone') ? $this->c->config->item('bootstrap','timezone') : (!ini_get('date.timezone')) ? 'UTC' : ini_get('date.timezone');

		/* set our timezone */
		date_default_timezone_set($tz);

		$this->c->config->set('bootstrap','timezone',$tz);

		/* setup our error display */
		error_reporting($this->c->config->item('bootstrap','error_reporting'));
		ini_set('display_errors',$this->c->config->item('bootstrap','display_errors'));
	}
	
	public function controller(&$obj=NULL) {
		if ($obj == NULL) {
			return $this->controller;
		}
		
		$this->controller = $obj;
		
		return $this;
	}

} /* end bootstrap */