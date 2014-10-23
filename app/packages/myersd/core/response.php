<?php
namespace myersd\core;

class response extends container {
	protected $final_output;
	protected $headers = [];
	protected $mimes = [];
	protected $mime_type = 'text/html';

	public function set_output($output) {
		$this->final_output = $output;

		return $this;
	}

	public function set_content_type($type) {

	}

	public function get_content_type()
	{
		for ($i = 0, $c = count($this->headers); $i < $c; $i++)
		{
			if (sscanf($this->headers[$i][0], 'Content-Type: %[^;]', $content_type) === 1)
			{
				return $content_type;
			}
		}

		return 'text/html';
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

	public function append_output($output) {
		$this->final_output .= $output;
		return $this;
	}

	public function set_header($header, $replace = TRUE) {
		$this->headers[] = [$header, $replace];
		return $this;
	}

	public function set_status_header($code = 200, $text = '') {
		set_status_header($code, $text);
		return $this;
	}

	/* dump the output */
	public function _display($output='') {
		// Set the output data
		if ($output === '') {
			$output = $this->final_output;
		}

		if (count($this->headers) > 0) {
			foreach ($this->headers as $header) {
				@header($header[0], $header[1]);
			}
		}

		echo $output;
	}

} /* end response */