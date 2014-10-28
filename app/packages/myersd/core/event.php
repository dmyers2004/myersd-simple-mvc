<?php
namespace myersd\core;

class event extends \myersd\core\base {

	public function register($name, $callback) {
		$key = strtolower((is_array($callback)) ? get_class($callback[0]).'/'.$callback[1] : $callback);
		$name = strtolower($name);

		$this->data[$name][$key] = $callback;

		return $this;
	}

	public function trigger($name, $data=NULL) {
		$name = strtolower($name);

		if ($this->has_event($name)) {
			foreach ($this->data[$name] as $listener) {
				if (is_callable($listener)) {
					$responds = call_user_func($listener, $data);

					if ($responds != NULL) {
						$data = $responds;
					}
				}
			}
		}

		return $data;
	}

	public function unregister($name, $callback) {
		$key = strtolower((is_array($callback)) ? get_class($callback[0]).'/'.$callback[1] : $callback);
		$name = strtolower($name);

		unset($this->data[$name][$key]);

		return $this;
	}

	public function has_event($name) {
		return (isset($this->data[$name]) && count($this->data[$name]) > 0);
	}

} /* end of events */