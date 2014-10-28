<?php

namespace myersd\libraries;

use myersd\core\container;

class View_Not_Found_Exception extends \Exception { }

class view {
	protected $c;

	public function __construct(container &$container) {
		$this->c = $container;
	}

	public function partial($_mvc_view_name=NULL,$_mvc_view_data=[]) {
		return $this->render($_mvc_view_name,$_mvc_view_data,FALSE);
	}

	public function render($_mvc_view_name=NULL,$_mvc_view_data=[],$output=TRUE) {
		$output = '';

		if ($_mvc_view_file = stream_resolve_include_path('views/'.$_mvc_view_name.'.php')) {

			/* extract out view data and make it in scope */
			extract($_mvc_view_data);

			/* start output cache */
			ob_start();

			/* load in view (which now has access to the in scope view data */
			include $_mvc_view_file;

			/* capture cache and return */
			$output = ob_get_clean();
		} else {
			/* simply error and exit */
			throw new View_Not_Found_Exception('View File "views/'.$_mvc_view_name.'.php" Not Found',810);
		}
		
		if ($output) {
			$this->c->output->set_output($output);
		}
		
		return $output;
	}

} /* end view class */