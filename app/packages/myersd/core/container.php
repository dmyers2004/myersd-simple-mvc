<?php
// http://fabien.potencier.org/article/17/on-php-5-3-lambda-functions-and-closures
namespace myersd\core;

class container {
	protected $data = [];

	public function __set($key,$c){
		$this->data[$key]=$c;
	}

	public function __get($key){
		if (!isset($this->data[$key])) {
			throw new \Exception(sprintf('Value "%s" is not defined.', $key),802);
		}

		return is_callable($this->data[$key]) ? $this->data[$key]($this) : $this->data[$key];
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