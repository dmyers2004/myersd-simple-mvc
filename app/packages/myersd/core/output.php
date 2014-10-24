<?php
namespace myersd\core;

class output {
	protected static $final_output = '';
	protected static $headers = [];
	protected static $mimes = [];
	protected static $mime_type = 'text/html';
	protected static $c;
	protected static $init = FALSE;

	public function __construct(container &$container) {
		self::$c = &$container;

		if (!self::$init) {
			$this->init($container);
		}
	}

	protected function init(container &$container) {
		self::$mimes = $container->config->item('mimes','mimes');
		self::$init = TRUE;
	}

	public function get_output() {
		return self::$final_output;
	}

	public function set_output($output) {
		self::$final_output = $output;

		return $this;
	}

	public function append_output($output) {
		if (self::$final_output == '') {
			self::$final_output = $output;
		} else {
			self::$final_output .= $output;
		}

		return $this;
	}

	public function set_header($header, $replace = TRUE) {
		self::$headers[] = [$header, $replace];

		return $this;
	}

	public function set_content_type($mime_type) {
		if (strpos($mime_type, '/') === FALSE) {
			$extension = ltrim($mime_type, '.');

			// Is this extension supported?
			if (isset(self::$mime_types[$extension])) {
				$mime_type =& self::$mime_types[$extension];

				if (is_array($mime_type)) {
					$mime_type = current($mime_type);
				}
			}
		}

		$header = 'Content-Type: '.$mime_type;

		self::$headers[] = array($header, TRUE);

		return $this;
	}

	public function set_status_header($code = 200, $text = '') {
		set_status_header($code, $text);

		return $this;
	}

	public function get_content_type() {
		for ($i = 0, $c = count(self::$headers); $i < $c; $i++) {
			if (sscanf(self::$headers[$i][0], 'Content-Type: %[^;]', $content_type) === 1) {
				return $content_type;
			}
		}

		return self::$mime_type;
	}

	public function get_header($header) {
		// Combine headers already sent with our batched headers
		$headers = array_merge(
			// We only need [x][0] from our multi-dimensional array
			array_map('array_shift', self::$headers),
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
			$output = self::$final_output;
		}

		if (count(self::$headers) > 0) {
			foreach (self::$headers as $header) {
				@header($header[0], $header[1]);
			}
		}

		if (method_exists(self::$c->router->controller_obj(),'_output')) {
			self::$c->router->controller_obj()->_output($output);
		} else {
			echo $output;
		}
	}

} /* end response */