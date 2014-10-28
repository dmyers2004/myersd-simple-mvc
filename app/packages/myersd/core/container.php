<?php
// http://fabien.potencier.org/article/17/on-php-5-3-lambda-functions-and-closures

namespace myersd\core;

class InvalidArgumentException extends \Exception {}

class container {
	protected $s = [];

	public function __set($k,$c){
		$this->s[$k]=$c;
	}

	public function __get($k){
		if (!isset($this->s[$k])) {
			throw new InvalidArgumentException(sprintf('Value "%s" is not defined.', $k),802);
		}

		return is_callable($this->s[$k]) ? $this->s[$k]($this) : $this->s[$k];
	}

	public function shared($callable) {
    return function ($c) use ($callable) {
      static $object;
 
      if (is_null($object)) {
        $object = $callable($c);
      }
 
      return $object;
    };
  }

} /* end container */