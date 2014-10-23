<?php
namespace myersd\core;

class output {
	protected static $final_output;
	protected static $headers = [];
	protected static $mimes = [];
	protected static $mime_type = 'text/html';

	public function __construct(container &$container) {
	
	}

	public function set_output($output) {
		self::$final_output = $output;

		return $this;
	}

	public function set_content_type($type) {

	}

	public function get_content_type() {
		for ($i = 0, $c = count(self::$headers); $i < $c; $i++) {
			if (sscanf(self::$headers[$i][0], 'Content-Type: %[^;]', $content_type) === 1) {
				return $content_type;
			}
		}

		return 'text/html';
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

	public function append_output($output) {
		self::$final_output .= $output;

		return $this;
	}

	public function set_header($header, $replace = TRUE) {
		self::$headers[] = [$header, $replace];

		return $this;
	}

	public function set_status_header($code = 200, $text = '') {
		set_status_header($code, $text);

		return $this;
	}

	/* dump the output */
	public function _display($output=NULL) {
		// Set the output data
		if ($output === NULL) {
			$output = self::$final_output;
		}

		if (count(self::$headers) > 0) {
			foreach (self::$headers as $header) {
				@header($header[0], $header[1]);
			}
		}

		echo $output;
	}

} /* end response */