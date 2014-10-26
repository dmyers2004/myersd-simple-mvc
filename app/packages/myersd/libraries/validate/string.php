<?php
/*
true is success
false is fail
*/

$config['length'] = function($validate,&$input,$options=NULL) {
	$input = substr($input,0,($length = (!$length) ? 2048 : $options));
	
	return TRUE;
};

$config['items'] = function($validate,&$input,$options=NULL) {
	$validate->set_message('More than %s items selected');

	$items = explode(',',$input);

	return (bool)(count($items) <= $options);
};

$config['hexcolor'] = function($validate,&$input,$options=NULL) {
	$validate->set_message('The %s is not a hex color.');

	return (bool)preg_match('/^#?[a-fA-F0-9]{3,6}$/',$input);
};

$config['mongoid'] = function($validate,&$input,$options=NULL) {
	$validate->set_message('The %s is not in the correct format.');

	return (bool)preg_match('/^([a-fA-F0-9]{24})$/',$input);
};

$config['md5'] = function($validate,&$input,$options=NULL) {
	$validate->set_message('The %s is not a valid MD5 value.');

	return (bool)preg_match('/^([a-fA-F0-9]{32})$/',$input);
};

$config['alpha_extra'] = function($validate,&$input,$options=NULL) {
	// Alpha-numeric with periods, underscores, spaces and dashes
	$validate->set_message('The %s field may only contain alpha-numeric characters, spaces, periods, underscores, and dashes.');

	return (bool)preg_match("/^([\.\s-a-z0-9_-])+$/i",$input);
};

$config['uri'] = function($validate,&$input,$options=NULL) {
	$validate->set_message('The %s is an invalid uniform resource identifier');

	return (bool)(preg_match("#^/[0-9a-z_*/]*$#",$input));
};

$config['url'] = function($validate,&$input,$options=NULL) {
	$validate->set_message('The %s is a invalid url.');
	
	return (bool)(preg_match('#^([\.\/-a-z0-9_*-])+$#i',$input));
};

$config['country'] = function($validate,&$input,$options=NULL) {
	$validate->set_message('%s must be a valid country');

	$list = array(
      //generic
      'aero', 'asia', 'biz', 'cat', 'com', 'coop', 'edu', 'gov', 'info',
      'int', 'jobs', 'mil', 'mobi', 'museum', 'name', 'net', 'org', 'pro',
      'tel', 'travel',
      //country
      'ac', 'ad', 'ae', 'af', 'ag', 'ai', 'al', 'am', 'ao', 'aq', 'ar', 'as',
      'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh',
      'bi', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bw', 'by', 'bz',
      'ca', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co',
      'cr', 'cs', 'cu', 'cv', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do',
      'dz', 'ec', 'ee', 'eg', 'er', 'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm',
      'fo', 'fr', 'ga', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm',
      'gn', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk', 'hm', 'hn',
      'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'io', 'iq', 'ir', 'is',
      'it', 'je', 'jm', 'jo', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp',
      'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt',
      'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mk', 'ml', 'mm',
      'mn', 'mo', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'mv', 'mw', 'mx', 'my',
      'mz', 'na', 'nc', 'ne', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu',
      'nz', 'om', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr',
      'ps', 'pt', 'pw', 'py', 'qa', 're', 'ro', 'rs', 'ru', 'rw', 'sa', 'sb',
      'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sk', 'sl', 'sm', 'sn', 'so',
      'sr', 'st', 'su', 'sv', 'sy', 'sz', 'tc', 'td', 'tf', 'tg', 'th', 'tj',
      'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'tt', 'tv', 'tw', 'tz', 'ua',
      'ug', 'uk', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu',
      'wf', 'ws', 'ye', 'yt', 'za', 'zm', 'zw'
  );

  return (bool)in_array(strtolower($field),$list);
};

$config['ip'] = function($validate,&$input,$options=NULL) {
	/* *.*.*.*, 10.1.1.*, 10.*.*.*, etc... */
	$validate->set_message('%s is not valid ip.');

	$sections = explode('.',$input);
	$match = explode('.',$options);

	if (count($sections) != 4 || count($match) != 4) {
		return FALSE;
	}

	for ($idx=0;$idx<=3;$idx++) {
		if ($match[$idx] != '*' && $sections[$idx] != $match[$idx]) {
			return FALSE;
		}
	}

	return TRUE;
};

$config['json'] = function($validate,&$input,$options=NULL) {
	$validate->set_message('%s is not valid json');

	return (bool)(json_decode($input));
};

$config['ends_with'] = function($validate,&$input,$options=NULL) {
	$validate->set_message('%s must end with '.$options);

	return (bool)($options == substr($input,-strlen($options)));
};

$config['starts_with'] = function($validate,&$input,$options=NULL) {
	$this->set_message('%s must start with '.$options);

	return (bool)(substr($input,0,strlen($options)) == $options);
};

$config['contains'] = function($validate,&$input,$options=NULL) {
	$this->set_message('%s must contain '.$options);

	return (bool)(strpos($input,$options) !== false) ? TRUE : FALSE;
};
