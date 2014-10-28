<?php
namespace myersd\core;

class router extends \myersd\core\base {
	public function route($uri=NULL) {
		/* have the input object prep/save the uri */
		$segs = $this->c->input->segments($uri);

		$segs = ($segs[0] == '') ? [$this->c->config->item('bootstrap','default_controller'),$this->c->config->item('bootstrap','default_method')] : $segs;

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
				$this->data['method'] = (isset($segs[$idx+1])) ? str_replace('-','_',$segs[$idx+1]) : $this->c->config->item('bootstrap','default_method');

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
			throw new \Exception('Controller File "'.$this->data['classname'].'.php" Not Found',803);
		}

		/* try to instantiate the controller */
		$controller = new $this->data['classname']($this->c);

		/* what method are we going to try to call? */
		$this->data['called'] = $this->c->config->item('bootstrap','request_method_format');

		$this->data['called'] = str_replace('%c',$this->data['method'],$this->data['called']);
		$this->data['called'] = str_replace('%a',$this->c->input->ajax(),$this->data['called']);
		$this->data['called'] = str_replace('%m',$this->c->input->method(),$this->data['called']);

		if (method_exists($controller,'_remap')) {
			$this->data['parameters'] = [$this->data['called'],$this->data['parameters']];
			$this->data['called'] = '_remap';
		}

		/* does that method even exist? */
		if (method_exists($controller, $this->data['called'])) {
			/* attach this to the application instance */
			$this->c->app->controller($controller);
		
			/* call the method and echo what's returned */
			echo call_user_func_array([$controller,$this->data['called']],$this->data['parameters']);
		} else {
			/* no throw a error */
			throw new \Exception('Method "'.$this->data['called'].'" Not Found',804);
		}

		return $this->c;
	} /* end route */

	public function called() {
		return $this->data['called'];
	}

	public function classname() {
		return $this->data['classname'];
	}

	public function parameters() {
		return $this->data['parameters'];
	}

	public function controller() {
		return $this->data['controller'];
	}

	public function method() {
		return $this->data['method'];
	}

	public function directory() {
		return $this->data['directory'];
	}

	public function controller_path() {
		return $this->data['controller_path'];
	}

} /* end router */