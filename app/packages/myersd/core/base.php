<?php
namespace myersd\core;

use myersd\core\container;

class base {
	protected $c;
	protected $data = [];

	public function __construct(container &$container) {
		$this->c = $container;

		if (method_exists($this,'init')) {
			$this->init();
		}
	}

} /* end base */