<?php

class log {
	protected $app;
	protected $log_level = 0;
	protected $log_file;

	protected $psr_levels = [
		'EMERGENCY' => 1,
		'ALERT'     => 2,
		'CRITICAL'  => 4,
		'ERROR'     => 8,
		'WARNING'   => 16,
		'NOTICE'    => 32,
		'INFO'      => 64,
		'DEBUG'     => 128,
	];
	protected $rfc_log_levels = [
		'DEBUG'			=> 100,
		'INFO'			=> 200,
		'NOTICE'		=> 250,
		'WARNING'		=> 300,
		'ERROR'			=> 400,
		'CRITICAL'	=> 500,
		'ALERT'			=> 550,
		'EMERGENCY'	=> 600,
	];

	public function __call($level,$value='') {
		$level = strtoupper($level);

		if (array_key_exists($level, $this->psr_levels)) {
			return $this->log($level,$value[0]);
		}

		return FALSE;
	}

	public function log($level, $msg='') {
		if (is_a($level,'app')) {
			$this->app = $level;

			$this->log_level = $level->init->log_level;
			$this->log_file = $level->init->log_file;

		} elseif (is_string($msg)) {


			if ($this->log_level > 0) {
				$level = strtoupper($level);

				if ((!array_key_exists($level,$this->psr_levels)) || (!($this->log_level & $this->psr_levels[$level]))) {
					return FALSE;
				}

				return file_put_contents($this->log_file,date('Y-m-d H:i:s').' '.$level.' '.$msg.chr(10),FILE_APPEND);
			}
		}
	}

} /* end log */