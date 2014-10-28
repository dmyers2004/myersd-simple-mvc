<?php
namespace myersd\libraries;

class testing extends \myersd\core\base {
	public $results = [];
	public $strict = TRUE;

	public function run($test, $expected = TRUE, $test_name = 'undefined', $notes = '') {
		if (substr($expected,0,4) == 'is_a') { /* is_a[foobar] */
			$class_name = substr($expected,5,-1);
			$result = is_a($test,$class_name);
			$extype = get_class($test);
		} elseif (in_array($expected, array('is_object', 'is_string', 'is_bool', 'is_true', 'is_false', 'is_int', 'is_numeric', 'is_float', 'is_double', 'is_array', 'is_null'), TRUE)) {
			$expected = str_replace('is_float', 'is_double', $expected);
			$result = ($expected($test)) ? TRUE : FALSE;
			$extype = str_replace(array('true', 'false'), 'bool', str_replace('is_', '', $expected));
		} else {
			if ($this->strict == TRUE) {
				$result = ($test === $expected) ? TRUE : FALSE;
			} else {
				$result = ($test == $expected) ? TRUE : FALSE;
			}
			
			$extype = gettype($expected);
		}

		$back = $this->_backtrace();

		$report[] = array (
			'test_name'			=> $test_name,
			'test_datatype'		=> gettype($test),	
			'res_datatype'		=> $extype,
			'result'			=> ($result === TRUE) ? 'passed' : 'failed',
			'file'				=> $back['file'],
			'line'				=> $back['line'],
			'notes'				=> $notes
		);

		$this->results[] = $report;

		return $this->results;
	}

	public function results() {
		return $this->results;
	}

	public function is_true($test) {
		return (is_bool($test) AND $test === TRUE) ? TRUE : FALSE;
	}
	
	public function is_false($test) {
		return (is_bool($test) AND $test === FALSE) ? TRUE : FALSE;
	}

	/**
	 * Generate a backtrace
	 *
	 * This lets us show file names and line numbers
	 *
	 * @access	private
	 * @return	array
	 */
	public function _backtrace() {
		if (function_exists('debug_backtrace')) {
			$back = debug_backtrace();

			$file = ( ! isset($back['1']['file'])) ? '' : $back['1']['file'];
			$line = ( ! isset($back['1']['line'])) ? '' : $back['1']['line'];

			return array('file' => $file, 'line' => $line);
		}
		return array('file' => 'Unknown', 'line' => 'Unknown');
	}
	
	public function use_strict($state = TRUE) {
		$this->strict = ($state == FALSE) ? FALSE : TRUE;
	}
	
	public function table() {
		echo '<style>
				table { font-family: arial; font-size: 13px}
				tr { background: #eee }
				td, th { padding: 3px; }
				i { color: #7e272a}
				b { color: #638239 }
			</style>';
		
		echo '<table style="">';
		echo '<tr>';
		echo '<th>Test Name</th>';
		echo '<th>Data Type</th>';
		echo '<th>Result Type</th>';
		echo '<th>Result</th>';
		echo '<th>File</th>';
		echo '<th>Line</th>';
		echo '<th>Note</th>';
		echo '</tr>';
		
		foreach ($this->results as $result) {
			$result = $result[0];
			
			if ($result['result']) {
				$foo = '<b>PASS</b>';
			} else {
				$foo = '<i>FAIL</i>';
			}
			
			echo '<tr>';
			echo '<td>'.$result['test_name'].'</td>';
			echo '<td style="text-align: center">'.$result['test_datatype'].'</td>';
			echo '<td style="text-align: center">'.$result['res_datatype'].'</td>';
			echo '<td style="text-align: center">'.$foo.'</td>';
			echo '<td>'.$result['file'].'</td>';
			echo '<td style="text-align: center">'.$result['line'].'</td>';
			echo '<td>'.$result['notes'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

} /* end testing */