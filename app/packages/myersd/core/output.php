<?php
namespace myersd\core;

class output {
	protected $c;
	protected $final_output = '';
	protected $headers = [];
	protected $mimes = [];
	protected $mime_type = 'text/html';

	public function __construct(container &$container) {
		$this->c = &$container;

		$this->mimes = $this->c->config->item('mimes','mimes');
	}

	public function get_output() {
		return $this->final_output;
	}

	public function set_output($output) {
		$this->final_output = $output;

		return $this;
	}

	public function append_output($output) {
		if ($this->final_output == '') {
			$this->final_output = $output;
		} else {
			$this->final_output .= $output;
		}

		return $this;
	}

	public function set_header($header, $replace = TRUE) {
		$this->headers[] = [$header, $replace];

		return $this;
	}

	public function set_content_type($mime_type) {
		if (strpos($mime_type, '/') === FALSE) {
			$extension = ltrim($mime_type, '.');

			// Is this extension supported?
			if (isset($this->mime_types[$extension])) {
				$mime_type =& $this->mime_types[$extension];

				if (is_array($mime_type)) {
					$mime_type = current($mime_type);
				}
			}
		}

		$header = 'Content-Type: '.$mime_type;

		$this->headers[] = array($header, TRUE);

		return $this;
	}

	public function set_status_header($code = 200, $text = '') {
		set_status_header($code, $text);

		return $this;
	}

	public function get_content_type() {
		for ($i = 0, $c = count($this->headers); $i < $c; $i++) {
			if (sscanf($this->headers[$i][0], 'Content-Type: %[^;]', $content_type) === 1) {
				return $content_type;
			}
		}

		return $this->mime_type;
	}

	public function get_header($header) {
		// Combine headers already sent with our batched headers
		$headers = array_merge(
			// We only need [x][0] from our multi-dimensional array
			array_map('array_shift', $this->headers),
			headers_list()
		);

		if (empty($headers) || empty($header)) {
			return NULL;
		}

		for ($i = 0, $c = count($headers); $i < $c; $i++) {
			if (strncasecmp($header, $headers[$i], $l = strlen($header)) === 0) {
				return trim(substr($headers[$i], $l+1));
			}
		}

		return NULL;
	}

	/* dump the output */
	public function display($output=NULL) {
		// Set the output data
		if (!$output) {
			$output = $this->final_output;
		}

		if (count($this->headers) > 0) {
			foreach ($this->headers as $header) {
				@header($header[0], $header[1]);
			}
		}

		if (method_exists($this->c->app->controller(),'_output')) {
			$this->c->app->controller()->_output($output);
		} else {
			echo $output;
		}
	}

	public function nocache() {
		$this->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		$this->set_header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
		$this->set_header('Cache-Control: post-check=0, pre-check=0', FALSE);
		$this->set_header('Pragma: no-cache');

		/* allow chaining */
		return $this;
	}

	/* wrapper for input delete cookie */
	public function delete_cookie($name='',$domain='',$path='/',$prefix='') {
		$this->c->input->set_cookie($name,'','',$domain,$path,$prefix);

		/* allow chaining */
		return $this;
	}

	/* wrapper for setting a cookie */
	public function cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE, $httponly = FALSE) {
		$this->c->input->set_cookie($name, $value, $expire, $domain, $path, $prefix, $secure,$httponly);

		/* allow chaining */
		return $this;
	}

	public function json($data=array(),$val=NULL) {
		$data = ($val !== NULL) ? array($data=>$val) : $data;
		$json = (is_array($data)) ? json_encode($data) : $data;

		$this
			->nocache()
			->set_content_type('application/json','utf=8')
			->set_output($json);

		/* allow chaining */
		return $this;
	}
	
} /* end response */