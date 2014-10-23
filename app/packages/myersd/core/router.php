<?php
namespace myersd\core;

class router extends container {

	public function route($uri=NULL) {
		/* have the input object prep/save the uri */
		$segs = $this->container->input->prep_uri($uri);

		$segs = ($segs[0] == '') ? [$this->container['app']['default_controller'],$this->container['app']['default_method']] : $segs;

		/* setup the defaults */
		$this->data['controller'] = '';
		$this->data['classname'] = '';
		$this->data['method'] = '';
		$this->data['parameters'] = [];
		$this->data['directory'] = '';
		$this->data['controller_path'] = '';

echo '<pre>';

		/* keep shifting off directories until we get a match */
		foreach ($segs as $idx=>$seg) {
			/* what controller are we testing for? */
			$this->data['classname'] = str_replace('-','_',$seg).'Controller';

			if ($this->data['controller_path'] = stream_resolve_include_path('controllers/'.$this->data['directory'].$this->data['classname'].'.php')) {
				/* match */
				$this->data['controller'] = substr($this->data['classname'],0,-10);

				/* what's the method? */
				$this->data['method'] = (isset($segs[$idx+1])) ? str_replace('-','_',$segs[$idx+1]) : $this->container['app']['default_method'];

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
		$controller = new $this->data['classname']($this->container);

		/* what method are we going to try to call? */
		$this->data['called'] = $this->data['method'].$this->container['input']['method'].'Action';

		/* does that method even exist? */
		if (method_exists($controller, $this->data['called'])) {
			/* call the method and echo what's returned */
			call_user_func_array(array($controller,$this->data['called']),$this->data['parameters']);
		} else {
			/* no throw a error */
			throw new Method_Not_Found_Exception('Method '.$this->data['called'].' Not Found',801);
		}

		return $this->container;
	} /* end route */

	public function controller() {
		return $this->data['controller'];
	}

	public function classname() {
		return $this->data['classname'];
	}

	public function method() {
		return $this->data['method'];
	}
	
	public function parameters() {
		return $this->data['parameters'];
	}
	
	public function directory() {
		return $this->data['directory'];
	}
	
	public function controller_path() {
		return $this->data['controller_path'];
	}
	
	public function called() {
		return $this->data['called'];
	}
	
} /* end router */