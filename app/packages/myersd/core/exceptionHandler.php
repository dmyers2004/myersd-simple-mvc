<?php
namespace myersd\core;

class exceptionHandler {
	protected static $attached = [];
	protected static $container;

	public static function attach($number,$function) {
		self::$attached[$number] = $function;
	}

	public static function handleException(\Exception $exception) {
		$num = $exception->getCode();

		if (array_key_exists($num,self::$attached)) {
			$closure = self::$attached[$num];
			$closure($exception,self::$container);
		} else {
			echo('<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Syntax Error</title></head><body><code>
				Version: PHP '.phpversion().'<br>
				Memory: '.floor(memory_get_peak_usage()/1024).'K of '.ini_get('memory_limit').' used<br>
				Error Code: '.$num.'<br>
				Error Message: '.$exception->getMessage().'<br>
				File: '.$exception->getFile().'<br>
				Line: '.$exception->getLine().'<br>
				</code></body></html>');
			exit(1);
		}
	}

	public static function load(container &$container) {
		self::$container = $container;

		$configs = $container->config->item('exception');

		foreach ($configs as $num=>$function) {
			self::$attached[$num] = $function;
		}

	}
} /* end exception */