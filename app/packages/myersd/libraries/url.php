<?php 

class url extends \myersd\core\base {

	/* redirect - cuz you always need one */
	public function redirect($url='/') {
		/* send redirect header */
		header("Location: $url");

		/* exit */
		exit(1);
	} /* end redirect() */

}