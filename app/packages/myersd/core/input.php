<?php
namespace myersd\core;

class input {
	protected $c;
	protected $data = [];

	public function __construct(container &$container) {
		$this->c = $container;

		$capture = ['server','post','get','cookie','env','files','request','put'];

		foreach ($capture as $c) {
			$this->data[$c] = $container->app->$c;
		}

		/* is this a ajax request? */
		$this->data['is_ajax'] = (isset($this->data['server']['HTTP_X_REQUESTED_WITH']) && strtolower($this->data['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? 'Ajax' : FALSE;

		/* is this a https request? */
		$this->data['https'] = ((!empty($this->data['server']['HTTPS']) && $this->data['server']['HTTPS'] !== 'off') || $this->data['server']['SERVER_PORT'] == 443);

		/* with http(s):// and with trailing slash */
		$this->data['base_url'] = trim('http'.(($this->data['https']) ? 's' : '').'://'.$this->data['server']['HTTP_HOST'].dirname($this->data['server']['SCRIPT_NAME']),'/');

		/* what type of request for REST or other */
		$this->data['raw_method'] = ucfirst(strtolower($this->data['server']['REQUEST_METHOD']));

		/*
		is this a restful app?
		if so and the request is Get than make it empty since it's the "default"
		this makes our methods follow the following fooAction (Get), fooPostAction (Post), fooPutAction (Put), fooDeleteAction (delete), etc...
		*/
		
		$this->data['method'] = ($container->app->restful) ? $this->data['method'] = ($this->data['raw_method'] == 'Get') ? '' : $this->data['raw_method'] : '';

		/* PHP doesn't handle PUT very well so we need to capture that manually */
		if ($this->data['raw_method'] == 'Put') {
			parse_str(file_get_contents('php://input'),$this->data['put']);
		}
		
	}

	public function prep_uri($uri=NULL) {
		$uri = ($uri) ? $uri : $this->data['server']['REQUEST_URI'];

		/* get the uri (uniform resource identifier) and preform some basic clean up */
		$this->data['uri'] = filter_var(trim($uri,'/'),FILTER_SANITIZE_URL);

		/* ok let's split these up for futher processing */
		$this->data['segments'] = explode('/',$this->data['uri']);

		return $this->data['segments'];
	}

	public function is_ajax() {
		return ($this->data['is_ajax'] === 'Ajax');
	}

	public function is_https() {
		return $this->data['https'];
	}

	public function base_url() {
		return $this->data['base_url'];
	}

	/* http method */
	public function method() {
		return $this->data['method'];
	}

	public function raw_method() {
		return $this->data['raw_method'];
	}

	public function post($name,$default=NULL) {
		return $this->internal('post',$name,$default);
	}

	public function get($name,$default=NULL) {
		return $this->internal('get',$name,$default);
	}

	public function server($name,$default=NULL) {
		return $this->internal('server',$name,$default);
	}

	public function cookie($name,$default=NULL) {
		return $this->internal('cookie',$name,$default);
	}

	public function env($name,$default=NULL) {
		return $this->internal('env',$name,$default);
	}

	public function files($name,$default=NULL) {
		return $this->internal('files',$name,$default);
	}

	public function request($name,$default=NULL) {
		return $this->internal('request',$name,$default);
	}

	public function put($name,$default=NULL) {
		return $this->internal('put',$name,$default);
	}

	public function all_post() {
		return $this->data['post'];
	}
	
	public function all_put() {
		return $this->data['put'];
	}
	
	public function all_get() {
		return $this->data['get'];
	}
	
	public function all_server() {
		return $this->data['server'];
	}
	
	public function all_cookie() {
		return $this->data['cookie'];
	}
	
	public function all_env() {
		return $this->data['env'];
	}
	
	public function all_request() {
		return $this->data['request'];
	}
	
	public function all_files() {
		return $this->data['files'];
	}

	protected function internal($key,$name,$default) {
		return (isset($this->data[$key][$name])) ? $this->data[$key][$name] : $default;
	}

} /* end request */