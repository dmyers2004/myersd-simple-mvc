<?php
namespace myersd\core;

class container {
	protected $s = [];
	
	public function __set($k, $c){
		$this->s[$k]=$c;
	}
	
	public function __get($k){
		return $this->s[$k]($this);
	}
	
} /* end container */