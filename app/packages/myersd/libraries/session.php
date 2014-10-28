<?php
namespace myersd\libraries;

class session extends \myersd\core\base {
	protected $flash_key = 'FLASHKEY';

	public function init() {
		session_set_cookie_params(
			$this->container->config->item('session','sess_cookie_lifetime'),
			$this->container->config->item('session','cookie_path'),
			$this->container->config->item('session','cookie_domain'),
			$this->container->config->item('session','cookie_secure'),
			TRUE /* HTTP Only Cannot be picked up VIA Javascript */
		);

		session_name($this->container->config->item('session','sess_cookie_name'));

		ini_set('session.gc_probability',$this->container->config->item('session','sess_gc_probability'));
		ini_set('session.gc_divisor',$this->container->config->item('session','sess_gc_divisor'));
		ini_set('session.gc_maxlifetime',$this->container->config->item('session','sess_gc_maxlifetime'));
		ini_set('session.cookie_lifetime',$this->container->config->item('session','sess_cookie_lifetime'));

		ini_set('session.use_trans_sid',FALSE); /* session IDs should never be passed via url if cookies are disabled */
		ini_set('session.use_only_cookies',TRUE); /* session IDs should be passed cookies only */

		ini_set('session.hash_function','whirlpool'); /* whirlpool */
		ini_set('session.hash_bits_per_character',4); /* 0-9 A-F */

		// make sure the session name starts with a letter
		session_name('a'.substr(md5($this->container->configuration['root']),7,23));

		// start session
		session_start();

		/* should we try to regenerate a new id? */
		/* WARNING! don't do this on a ajax request! */
		if (!$this->container->input->is_ajax()) {
			if (mt_rand(1,100) < $this->container->config->item('session','sess_chance_to_update')) {
				session_regenerate_id(TRUE);
			}
		}

		/* did they supply a mock? */
		$_SESSION = (isset($this->container->configuration['session'])) ? $this->container->configuration['session'] : $_SESSION;

		$this->flashdata_sweep()->flashdata_mark();		
	}

	public function all() {
		return $_SESSION;
	}

	public function get($key=NULL,$default=NULL) {
		return (isset($_SESSION[$key])) ? $_SESSION[$key] : $default;
	}

	public function set($key,$value=NULL) {
		if ($value === NULL) {
			unset($_SESSION[$key]);
		} else {
			$_SESSION[$key] = $value;
		}

		return $this;
	}

	public function get_flashdata($key) {
		return $this->get($name,$this->flash_key.':old:'.$key);
	}

	public function set_flashdata($key,$value) {
		return $this->set($this->flash_key.':new:'.$key,$value);
	}

	public function keep_flashdata($key=NULL) {
		foreach ($_SESSION as $key=>$value) {
			if (strpos($key,$this->flash_key.':old:') !== FALSE) {
				$new_key = str_replace(':old:',':new:',$key);
				$_SESSION[$new_key] = $value;
				unset($_SESSION[$key]);
			}
		}
		
		return $this;
	}

	protected function flashdata_mark() {
		foreach ($_SESSION as $key=>$value) {
			if (strpos($key,$this->flash_key.':new:') !== FALSE) {
				$new_key = str_replace(':new:',':old:',$key);
				$_SESSION[$new_key] = $value;
				unset($_SESSION[$key]);
			}
		}

		return $this;
	}

	protected function flashdata_sweep() {
		foreach ($_SESSION as $key=>$value) {
			if (strpos($key,$this->flash_key.':old:') !== FALSE) {
				unset($_SESSION[$key]);
			}
		}		

		return $this;
	}

} /* end session class */