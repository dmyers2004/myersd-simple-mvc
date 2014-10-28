<?php
namespace myersd\libraries;

// !todo log all denied & forged

class security extends \myersd\core\base {
	protected $default_error_msg_html = '<strong>Forged Request Detected</strong> If you clicked on a link and arrived here...that is bad.';
	protected $default_error_msg_json = 'Forged Request Detected';

	public function forged() {
		if ($this->c->input->is_ajax()) {
			$this->c->output->json(['err'=>TRUE,'errors_array'=>[$this->default_error_msg_json],'errors'=>$this->default_error_msg_json])->display();
		} else {
			/* show a message or just redirect? */
			if (!empty($this->c->config->item('security','forge_url')) {
				redirect($this->c->config->item('security','forge_url'));
			} else {
				$error_msg = (!empty($this->c->config->item('security','forge_error'))) ? $this->c->config->item('security'$this->c->config->item('security','forge_error') : $this->default_error_msg_html;

				throw new \Exception('Error Message: '.$error_msg.' Error String: '.$this->c->validate->error_string(' ','<br>'),805);
			}
		}

		exit(1);
	}

	public function denied($ur='') {
		/* set a flash message and send them to the login page */
		if ($this->c->config->item('security','hard_error') == FALSE) {
			$redirect = (!empty($this->c->config->item('security','redirect_to'))) ? $this->c->config->item('security','redirect_to') : '/redirect/login';

			/* if flash msg isn't loaded you will drop down to the next level */
			if (is_object($this->c->flash_msg)) {
				$this->c->flash_msg->denied($redirect);
			}
		}

		/* if they want to see a hard error - here you go! */
		throw new \Exception('Access Denied: Please try to login again.',806);

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