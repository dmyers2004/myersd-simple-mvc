<?php
namespace myersd\core;

class input {
	protected $c;
	protected $data = [];
	protected $capture = ['server','post','get','cookie','env','files','request','put'];

	public function __construct(container &$container) {
		$this->c = $container;

		foreach ($this->capture as $var) {
			$this->data[$var] = $container->app->$var();
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
		$this->data['method'] = ($container->app->restful()) ? $this->data['method'] = ($this->data['raw_method'] == 'Get') ? '' : $this->data['raw_method'] : '';

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
		return (bool)($this->data['is_ajax'] === 'Ajax');
	}

	public function is_https() {
		return (bool)$this->data['https'];
	}

	public function __call($name,$arguments) {
		$name = (substr($name,0,4) == 'all_') ? substr($name,4) : $name;

		if (in_array($name,$this->capture)) {
			return (isset($this->data[$name][$arguments[0]])) ? $this->data[$name][$arguments[0]] : $arguments[1];
		} else {
			return isset($this->data[$name]) ? $this->data[$name] : NULL;
		}
	}

	public function map($fields,&$data) {
		if (!is_array($fields)) {
			$fields = explode(',',$fields);
		}

		foreach ($fields as $field) {
			$post_field = $from_field = $field;

			if (strpos($field,' as ') !== FALSE) {
				list($post_field,$from_field) = explode(' as ',$field,2);
			}
			
			$data[$from_field] = $this->post($post_field);
		}
		
		return $this; /* allow chaining */
	}

} /* end request */