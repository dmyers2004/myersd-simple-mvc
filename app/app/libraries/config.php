<?php 
class Config_Not_Found_Exception extends Exception { }

class config {
	protected $app;
	protected $config;

	public function __get($name) {
		return @$this->config->$name;
	}

	public function config($name=NULL,$env=NULL) {
		if (is_a($name,'app')) {
			$this->app = $name;
			
			$this->config = new stdClass;
			
		} elseif (is_string($name)) {
			$env = ($env) ? $env : @$this->app->properties->server->{$this->app->init->config_env};
	
			if ($filename = stream_resolve_include_path('config/'.$name.'.php')) {
				/* include the config file */
				include_once $filename;
		
				$base_config = $config;
	
				if ($env) {
					if ($filename = stream_resolve_include_path('config/'.$env.'/'.$name.'.php')) {
						include_once $filename;
	
						$base_config = array_replace_recursive($base_config,$config);
					}
				}
	
				/* attach it to the app */
				$this->config->$name = (object)$base_config;
	
				/* this fills $config so return it */
				return $this->config->$name;
			} else {
				throw new Config_Not_Found_Exception('Config File config/'.$name.'.php Not Found',803);
			}
		}
	}

}