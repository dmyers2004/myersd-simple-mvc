<?php
namespace myersd\core;

class router extends container {

	public function route($uri=NULL) {
		$container = &$this->container;
	
		$segs = $container->input->prep_uri($uri);

		$segs = ($segs[0] == '') ? [$container['app']['default_controller'],$container['app']['default_method']] : $segs;

		/* setup the defaults */
		$container['app']['controller'] = $container['app']['default_controller'];
		$container['app']['classname'] = $container['app']['default_controller'].'Controller';
		$container['app']['method'] = $container['app']['default_method'];
		$container['app']['parameters'] = [];
		$container['app']['directory'] = '';
		$container['app']['controller_path'] = '';

		/* keep shifting off directories until we get a match */
		foreach ($segs as $idx=>$seg) {

			/* what controller are we testing for? */
			$container['app']['classname'] = str_replace('-','_',$seg).'Controller';

			if ($container['app']['controller_path'] = stream_resolve_include_path('controllers/'.$container['app']['directory'].$container['app']['classname'].'.php')) {
				/* match */
				$container['app']['controller'] = substr($container['app']['classname'],0,-10);

				/* what's the method? */
				$container['app']['method'] = (isset($segs[$idx+1])) ? str_replace('-','_',$segs[$idx+1]) : $container['app']['default_method'];

				/* what are the parameters? */
				$container['app']['parameters'] = array_slice($segs,$idx+2);

				/* load that controller */
				include $container['app']['controller_path'];

				/* get out of here we found a match! */
				break;
			}

			/* we didn't find a match yet so add that segement to the directory */
			$container['app']['directory'] .= $seg.'/';
		}

		/* was a class loaded */
		if (!class_exists($container['app']['classname'])) {
			throw new Controller_Not_Found_Exception('Controller File '.$container['app']['classname'].'.php Not Found',800);
		}

		/* try to instantiate the controller */
		$controller = new $container['app']['classname']($container);

		/* what method are we going to try to call? */
		$container['app']['called'] = $container['app']['method'].$container['input']['method'].'Action';

		/* does that method even exist? */
		if (method_exists($controller, $container['app']['called'])) {
			/* call the method and echo what's returned */
			call_user_func_array(array($controller,$container['app']['called']),$container['app']['parameters']);
		} else {
			/* no throw a error */
			throw new Method_Not_Found_Exception('Method '.$container['app']['called'].' Not Found',801);
		}

		return $container;
	} /* end route */

} /* end router */