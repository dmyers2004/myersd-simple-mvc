<?php
namespace myersd\libraries;

use myersd\core\container;

class log {
	protected static $log_level = 0;
	protected static $log_file;
	protected static $log_format;
	protected static $log_generic;
	protected static $c;

	protected static $psr_levels = [
		'EMERGENCY' => 1,
		'ALERT'     => 2,
		'CRITICAL'  => 4,
		'ERROR'     => 8,
		'WARNING'   => 16,
		'NOTICE'    => 32,
		'INFO'      => 64,
		'DEBUG'     => 128,
	];
	protected static $rfc_log_levels = [
		'DEBUG'			=> 100,
		'INFO'			=> 200,
		'NOTICE'		=> 250,
		'WARNING'		=> 300,
		'ERROR'			=> 400,
		'CRITICAL'	=> 500,
		'ALERT'			=> 550,
		'EMERGENCY'	=> 600,
	];

	public function __construct(container &$container) {
		self::$c = $container;

		self::$log_level = self::$c->config->item('log','log_level');
		self::$log_file = self::$c->app->root.self::$c->config->item('log','log_file');
		self::$log_format = self::$c->config->item('log','log_format','Y-m-d H:i:s');
		self::$log_generic = self::$c->config->item('log','log_generic','GENERAL');
	}

	public function __call($level,$value) {
		$level = strtoupper($level);

		if (array_key_exists($level, self::$psr_levels)) {
			return $this->_write($level,$value[0]);
		}

		return FALSE;
	} /* end __call */

	public function write($msg, $level) {
		$level = ($level) ? $level : self::$log_generic;

		return file_put_contents(self::$log_file,date(self::$log_format).' '.$level.' '.$msg.chr(10),FILE_APPEND);
	} /* end write */

	protected function _write($level, $msg='') {
		if (self::$log_level > 0) {
			$level = strtoupper($level);

			if ((!array_key_exists($level,self::$psr_levels)) || (!(self::$log_level & self::$psr_levels[$level]))) {
				return FALSE;
			}
			
			return $this->write($msg,$level);
		}
	} /* end _write */

} /* end log */