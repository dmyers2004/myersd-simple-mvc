<?php
namespace myersd\core;

class input {
	protected static $data = [];
	protected static $c;

	public function __construct(container &$container) {
		self::$c = $container;

		$capture = ['server','post','get','cookie','env','files','request','put'];

		foreach ($capture as $c) {
			self::$data[$c] = $container->app->$c;
		}

		/* is this a ajax request? */
		self::$data['is_ajax'] = (isset(self::$data['server']['HTTP_X_REQUESTED_WITH']) && strtolower(self::$data['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? 'Ajax' : FALSE;

		/* is this a https request? */
		self::$data['https'] = ((!empty(self::$data['server']['HTTPS']) && self::$data['server']['HTTPS'] !== 'off') || self::$data['server']['SERVER_PORT'] == 443);

		/* with http(s):// and with trailing slash */
		self::$data['base_url'] = trim('http'.((self::$data['https']) ? 's' : '').'://'.self::$data['server']['HTTP_HOST'].dirname(self::$data['server']['SCRIPT_NAME']),'/');

		/* what type of request for REST or other */
		self::$data['raw_method'] = ucfirst(strtolower(self::$data['server']['REQUEST_METHOD']));

		/*
		is this a restful app?
		if so and the request is Get than make it empty since it's the "default"
		this makes our methods follow the following fooAction (Get), fooPostAction (Post), fooPutAction (Put), fooDeleteAction (delete), etc...
		*/
		
		self::$data['method'] = ($container->app->restful) ? self::$data['method'] = (self::$data['raw_method'] == 'Get') ? '' : self::$data['raw_method'] : '';

		/* PHP doesn't handle PUT very well so we need to capture that manually */
		if (self::$data['raw_method'] == 'Put') {
			parse_str(file_get_contents('php://input'), self::$data['put']);
		}
	}

	public function prep_uri($uri=NULL) {
		$uri = ($uri) ? $uri : self::$data['server']['REQUEST_URI'];

		/* get the uri (uniform resource identifier) and preform some basic clean up */
		self::$data['uri'] = filter_var(trim($uri,'/'),FILTER_SANITIZE_URL);

		/* ok let's split these up for futher processing */
		self::$data['segments'] = explode('/',self::$data['uri']);

		return self::$data['segments'];
	}

	public function is_ajax() {
		return (self::$data['is_ajax'] === 'Ajax');
	}

	public function is_https() {
		return self::$data['https'];
	}

	public function base_url() {
		return self::$data['base_url'];
	}

	/* http method */
	public function method() {
		return self::$data['method'];
	}

	public function raw_method() {
		return self::$data['raw_method'];
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

	protected function internal($key,$name,$default) {
		return (isset(self::$data[$key][$name])) ? self::$data[$key][$name] : $default;
	}

} /* end request */