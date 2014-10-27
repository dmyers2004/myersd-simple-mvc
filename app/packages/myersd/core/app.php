<?php
namespace myersd\core;

class app {
	protected $c;
	protected $data = [];

	public function __construct(container &$container) {
		$this->c = $container;

		/* setup timezone so PHP doesn't complain */
		$this->data['timezone'] = ($this->c->configuration['timezone']) ? $this->c->configuration['timezone'] : ((!ini_get('date.timezone') ? 'UTC' : ini_get('date.timezone')));

		/* set our timezone */
		date_default_timezone_set($this->data['timezone']);

		/* setup our error display */
		error_reporting($this->c->configuration['error_reporting']);
		ini_set('display_errors', $this->c->configuration['display_errors']);

		set_include_path(get_include_path().$this->c->configuration['include_path']);
	}

	public function timezone() {
		return $this->data['timezone'];
	}

	public function error_reporting() {
		return $this->data['error_reporting'];
	}

	public function display_errors() {
		return $this->data['display_errors'];
	}

	public function packages() {
		return $this->c->configuration['packages'];
	}

	public function root() {
		return $this->c->configuration['root'];
	}

	public function request_methods() {
		return $this->c->configuration['request_methods'];
	}

	public function ajax_aware() {
		return $this->c->configuration['ajax_aware'];
	}

	public function environment_variable() {
		return $this->c->configuration['environment_variable'];
	}

	public function default_controller() {
		return $this->c->configuration['default_controller'];
	}

	public function default_method() {
		return $this->c->configuration['default_method'];
	}

} /* end bootstrap */