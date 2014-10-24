<?php
namespace myersd\core;

class event {
	protected static $events = [];

	public function register($name, $callback) {
		$key = strtolower((is_array($callback)) ? get_class($callback[0]).'/'.$callback[1] : $callback);
		$name = strtolower($name);

		self::$events[$name][$key] = $callback;

		return $this;
	}

	public function trigger($name, $data=NULL) {
		$name = strtolower($name);

		if ($this->has_event($name)) {
			foreach (self::$events[$name] as $listener) {
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

		unset(self::$events[$name][$key]);

		return $this;
	}

	public function has_event($name) {
		return (isset(self::$events[$name]) && count(self::$events[$name]) > 0);
	}

} /* end of events */