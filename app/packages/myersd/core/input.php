<?php
namespace myersd\core;

class input {
	protected $c;
	protected $data = [];
	protected $capture = ['server','post','get','cookie','env','files','request','put'];

	public function __construct(container &$container) {
		$this->c = $container;

		foreach ($this->capture as $var) {
			$this->data[$var] = $this->c->config->item('bootstrap',$var);
		}

		/* is this a ajax request? */
		$this->data['is_ajax'] = (isset($this->data['server']['HTTP_X_REQUESTED_WITH']) && strtolower($this->data['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? 'Ajax' : FALSE;

		/* is this a https request? */
		$this->data['https'] = ((!empty($this->data['server']['HTTPS']) && $this->data['server']['HTTPS'] !== 'off') || $this->data['server']['SERVER_PORT'] == 443);

		/* with http(s):// and with trailing slash */
		$this->data['base_url'] = trim('http'.(($this->data['https']) ? 's' : '').'://'.$this->data['server']['HTTP_HOST'].dirname($this->data['server']['SCRIPT_NAME']),'/');

		/* what type of request for REST or other */
		$this->data['raw_method'] = strtolower($this->data['server']['REQUEST_METHOD']);

		$this->data['method'] = $this->c->config->item('bootstrap','request_methods')[$this->data['raw_method']];
			
		/* PHP doesn't handle PUT very well so we need to capture that manually */
		if ($this->data['raw_method'] == 'put') {
			parse_str(file_get_contents('php://input'),$this->data['put']);
		}
		
		$this->_prep();
	}

	protected function _prep($uri=NULL) {
		$uri = ($uri) ? $uri : $this->data['server']['REQUEST_URI'];
	
		/* get the uri (uniform resource identifier) and preform some basic clean up */
		$this->data['uri'] = filter_var(trim($uri,'/'),FILTER_SANITIZE_URL);

		/* ok let's split these up for futher processing */
		$this->data['segments'] = explode('/',$this->data['uri']);
	}

	public function segments($uri=NULL) {
		if ($uri != NULL) {
			$this->_prep($uri);
		}
	
		return $this->data['segments'];
	}
	
	public function uri() {
		return $this->data['uri'];
	}

	public function base_url() {
		return $this->data['base_url'];
	}

	public function raw_method() {
		return $this->data['raw_method'];
	}

	public function method() {
		return $this->data['method'];
	}

	public function server($key=NULL,$default=NULL) {
		return $this->_internal('server',$key,$default);
	}

	public function post($key=NULL,$default=NULL) {
		return $this->_internal('post',$key,$default);
	}

	public function get($key=NULL,$default=NULL) {
		return $this->_internal('get',$key,$default);
	}

	public function request($key=NULL,$default=NULL) {
		return $this->_internal('request',$key,$default);
	}

	public function put($key=NULL,$default=NULL) {
		return $this->_internal('put',$key,$default);
	}

	public function cookie($key=NULL,$default=NULL) {
		return $this->_internal('cookie',$key,$default);
	}

	public function env($key=NULL,$default=NULL) {
		return $this->_internal('env',$key,$default);
	}

	public function files($key=NULL,$default=NULL) {
		return $this->_internal('files',$key,$default);
	}

	protected function _internal($key,$idx,$default) {
		return ($idx == NULL) ? $this->data[$key] : (isset($this->data[$key][$idx])) ? $this->data[$key][$idx] : $default;
	}

	public function is_ajax() {
		return (bool)($this->data['is_ajax'] === 'Ajax');
	}
	
	public function ajax() {
		return $this->data['is_ajax'];
	}

	public function is_https() {
		return (bool)$this->data['https'];
	}

	public function map($fields,&$data,$method='post') {
		if (!is_array($fields)) {
			$fields = explode(',',$fields);
		}

		foreach ($fields as $field) {
			$post_field = $from_field = $field;

			if (strpos($field,' as ') !== FALSE) {
				list($post_field,$from_field) = explode(' as ',$field,2);
			}
			
			$default = NULL;
			
			if (strpos($from_field,' default ') !== FALSE) {
				list($from_field,$default) = explode(' default ',$from_field,2);
			}
			
			/* passed by reference so modified directly */
			$data[$from_field] = $this->$method($post_field,$default);
		}
		
		return $this; /* allow chaining */
	}

} /* end request */