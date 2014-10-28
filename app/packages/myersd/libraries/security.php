<?php
namespace myersd\libraries;

use myersd\core\container;

class Security_Exception extends \Exception { }

// !todo log all denied & forged

class security {
	protected $c;
	protected $default_error_msg_html = '<strong>Forged Request Detected</strong> If you clicked on a link and arrived here...that is bad.';
	protected $default_error_msg_json = 'Forged Request Detected';
	protected $config;

	public function __construct(container &$container) {
		$this->c = $container;
		$this->config = $this->c->config->item('security');
	}

	public function forged() {
		if ($this->c->input->is_ajax()) {
			$this->c->output->json(['err'=>TRUE,'errors_array'=>[$this->default_error_msg_json],'errors'=>$this->default_error_msg_json])->display();
		} else {
			/* show a message or just redirect? */
			if (!empty($this->config['forge_url'])) {
				redirect($this->config['forge_url']);
			} else {
				$error_msg = (!empty($this->config['forge_error'])) ? $this->config['forge_error'] : $this->default_error_msg_html;

				throw new Security_Exception('Error Message: '.$error_msg.' Error String: '.$this->c->validate->error_string(' ','<br>'),805);
			}
		}

		exit(1);
	}

	public function denied($ur='') {
		/* set a flash message and send them to the login page */
		if ($this->config['hard_error'] == FALSE) {
			$redirect = (!empty($this->config['redirect_to'])) ? $this->config['redirect_to'] : '/redirect/login';

			/* if flash msg isn't loaded you will drop down to the next level */
			if (is_object($this->c->flash_msg)) {
				$this->c->flash_msg->denied($redirect);
			}
		}

		/* if they want to see a hard error - here you go! */
		throw new Security_Exception('Access Denied: Please try to login again.',806);

		/*
		you should never, never get to this
		because the show error stops processing
		but incase you do
		*/
		die('Access Denied: Please try to login again.');

		/* should NEVER, NEVER, NEVER get here... */
		exit(1);
	}

} /* end class */