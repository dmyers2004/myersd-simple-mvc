<?php

class session {
	protected $app;
	protected $session;

	public function __construct(&$app) {
		$this->app = $app;

		// make sure the session name starts with a letter
		session_name('a'.substr(md5($app->init->modules),7,16));

		// start session
		session_start();
		
		/* did they supply a mock? */
		$this->session = ($app->init->session) ? $app->init->session : $_SESSION;
	}

	public function get($name) {
		return @$this->session[$name];
	}

	public function set($name,$value) {
		$this->session[$name] = $_SESSION[$name] = $value;

		return $this;
	}

} /* end session */