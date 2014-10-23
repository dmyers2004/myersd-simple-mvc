<?php

class session {
	protected $app;
	protected $session;
	protected $user_key = 'USER';
	protected $flash_key = 'FLASHKEY';
	protected $snap_key = 'SNAPKEY';

	public function __construct(&$app) {
		$this->app = $app;

		// make sure the session name starts with a letter
		session_name('a'.substr(md5($app->init->modules),7,16));

		// start session
		session_start();
		
		/* did they supply a mock? */
		$this->session = ($app->init->session) ? $app->init->session : $_SESSION;
	}

	public function all() {
		return @$this->session;
	}

	public function get($name=NULL,$key=NULL) {
		$key = ($key) ? $key : $this->user_key;
	
		return ($name) ? @$this->session[$key][$name] : @$this->session[$key];
	}

	public function set($name,$value=NULL,$key=NULL) {
		$key = ($key) ? $key : $this->user_key;

		if ($value === NULL) {
			unset($this->session[$this->user_key][$name]);
			unset($_SESSION[$this->user_key][$name]);
		} else {
			$this->session[$key][$name] = $_SESSION[$key][$name] = $value;
		}

		return $this;
	}
	
	public function get_flash($name) {
		return $this->get($name,$this->flash_key);		
	}
	
	public function set_flash($name,$value=NULL) {
		return $this->set($name,$value,$this->flash_key);
	}
	
	public function keep_flashdata($key=NULL) {

	}
	
	public function get_stash() {
	
	}
	
	public function set_stash() {
	
	}
	
	public function get_snap() {
	
	}
	
	public function set_snap() {
	
	}
	
	public function keep_snapdata($key) {
		/* read and return the snap data */
		return $this->userdata($this->snapdata_key.':'.$key);
	}

} /* end session */