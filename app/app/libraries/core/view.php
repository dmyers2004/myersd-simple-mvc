<?php
class View_Not_Found_Exception extends Exception { }

class view {

	public function view($_mvc_view_name=NULL,$_mvc_view_data=[]) {
		if (is_string($_mvc_view_name)) {
			/* is it there? */
			if ($_mvc_view_file = stream_resolve_include_path('views/'.$_mvc_view_name.'.php')) {
	
				/* extract out view data and make it in scope */
				extract($_mvc_view_data);
	
				/* start output cache */
				ob_start();
	
				/* load in view (which now has access to the in scope view data */
				include $_mvc_view_file;
	
				/* capture cache and return */
				return ob_get_clean();
			} else {
	
				/* simply error and exit */
				throw new View_Not_Found_Exception('View File views/'.$_mvc_view_name.'.php Not Found',802);
			}
		}
	}

} /* end view class */