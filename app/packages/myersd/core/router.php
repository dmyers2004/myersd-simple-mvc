<?php
namespace myersd\core;

class Controller_Not_Found_Exception extends \Exception {}
class Method_Not_Found_Exception extends \Exception {}

class router {
	protected static $data = [];
	protected static $c;
	protected static $controller;
	
	public function __construct(container &$container) {
		self::$c = $container;
	}

	public function route($uri=NULL) {
		/* have the input object prep/save the uri */
		$segs = self::$c->input->prep_uri($uri);

		$segs = ($segs[0] == '') ? [self::$c->app->default_controller,self::$c->app->default_method] : $segs;

		/* setup the defaults */
		self::$data['controller'] = '';
		self::$data['classname'] = '';
		self::$data['method'] = '';
		self::$data['parameters'] = [];
		self::$data['directory'] = '';
		self::$data['controller_path'] = '';

		/* keep shifting off directories until we get a match */
		foreach ($segs as $idx=>$seg) {
			/* what controller are we testing for? */
			self::$data['classname'] = str_replace('-','_',$seg).'Controller';

			if (self::$data['controller_path'] = stream_resolve_include_path('controllers/'.self::$data['directory'].self::$data['classname'].'.php')) {
				/* match */
				self::$data['controller'] = substr(self::$data['classname'],0,-10);

				/* what's the method? */
				self::$data['method'] = (isset($segs[$idx+1])) ? str_replace('-','_',$segs[$idx+1]) : self::$c->app->default_method;

				/* what are the parameters? */
				self::$data['parameters'] = array_slice($segs,$idx+2);

				/* load that controller */
				include self::$data['controller_path'];

				/* get out of here we found a match! */
				break;
			}

			/* we didn't find a match yet so add that segement to the directory */
			self::$data['directory'] .= $seg.'/';
		}

		/* was a class loaded */
		if (!class_exists(self::$data['classname'])) {
			throw new Controller_Not_Found_Exception('Controller File '.self::$data['classname'].'.php Not Found',800);
		}

		/* try to instantiate the controller */
		self::$controller = new self::$data['classname'](self::$c);

		/* what method are we going to try to call? */
		self::$data['called'] = self::$data['method'].self::$c->input->method().'Action';

		if (method_exists(self::$controller,'_remap')) {
			self::$data['parameters'] = [self::$data['called'],self::$data['parameters']];
			self::$data['called'] = '_remap';
		}

		/* does that method even exist? */
		if (method_exists(self::$controller, self::$data['called'])) {
			/* call the method and echo what's returned */
			echo call_user_func_array([self::$controller,self::$data['called']],self::$data['parameters']);
		} else {
			/* no throw a error */
			throw new Method_Not_Found_Exception('Method '.self::$data['called'].' Not Found',801);
		}

		return self::$c;
	} /* end route */

	public function controller_obj() {
		return self::$controller;
	}

	public function controller() {
		return self::$data['controller'];
	}

	public function classname() {
		return self::$data['classname'];
	}

	public function method() {
		return self::$data['method'];
	}
	
	public function parameters() {
		return self::$data['parameters'];
	}
	
	public function directory() {
		return self::$data['directory'];
	}
	
	public function controller_path() {
		return self::$data['controller_path'];
	}
	
	public function called() {
		return self::$data['called'];
	}
	
} /* end router */