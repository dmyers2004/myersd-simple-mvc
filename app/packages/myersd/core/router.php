<?php
namespace myersd\core;

class Controller_Not_Found_Exception extends \Exception {}
class Method_Not_Found_Exception extends \Exception {}

class router {
	protected $c;
	protected $data = [];
	protected $controller;
	
	public function __construct(container &$container) {
		$this->c = $container;
	}

	public function route($uri=NULL) {
		/* have the input object prep/save the uri */
		$segs = $this->c->input->prep_uri($uri);

		$segs = ($segs[0] == '') ? [$this->c->app->default_controller(),$this->c->app->default_method()] : $segs;

		/* setup the defaults */
		$this->data['controller'] = '';
		$this->data['classname'] = '';
		$this->data['method'] = '';
		$this->data['parameters'] = [];
		$this->data['directory'] = '';
		$this->data['controller_path'] = '';

		/* keep shifting off directories until we get a match */
		foreach ($segs as $idx=>$seg) {
			/* what controller are we testing for? */
			$this->data['classname'] = str_replace('-','_',$seg).'Controller';

			if ($this->data['controller_path'] = stream_resolve_include_path('controllers/'.$this->data['directory'].$this->data['classname'].'.php')) {
				/* match */
				$this->data['controller'] = substr($this->data['classname'],0,-10);

				/* what's the method? */
				$this->data['method'] = (isset($segs[$idx+1])) ? str_replace('-','_',$segs[$idx+1]) : $this->c->app->default_method();

				/* what are the parameters? */
				$this->data['parameters'] = array_slice($segs,$idx+2);

				/* load that controller */
				include $this->data['controller_path'];

				/* get out of here we found a match! */
				break;
			}

			/* we didn't find a match yet so add that segement to the directory */
			$this->data['directory'] .= $seg.'/';
		}

		/* was a class loaded */
		if (!class_exists($this->data['classname'])) {
			throw new Controller_Not_Found_Exception('Controller File '.$this->data['classname'].'.php Not Found',800);
		}

		/* try to instantiate the controller */
		$this->controller = new $this->data['classname']($this->c);

		/* what method are we going to try to call? */
		$this->data['called'] = $this->data['method'].$this->c->input->method().'Action';

		if (method_exists($this->controller,'_remap')) {
			$this->data['parameters'] = [$this->data['called'],$this->data['parameters']];
			$this->data['called'] = '_remap';
		}

		/* does that method even exist? */
		if (method_exists($this->controller, $this->data['called'])) {
			/* call the method and echo what's returned */
			echo call_user_func_array([$this->controller,$this->data['called']],$this->data['parameters']);
		} else {
			/* no throw a error */
			throw new Method_Not_Found_Exception('Method '.$this->data['called'].' Not Found',801);
		}

		return $this->c;
	} /* end route */

	public function controller_obj() {
		return $this->controller;
	}

	public function __get($name) {
		return isset($this->data[$name]) ? $this->data[$name] : NULL;
	}
	
} /* end router */