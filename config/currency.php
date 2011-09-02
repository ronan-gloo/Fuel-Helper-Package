<?php

return array(
	'base_rate'	=> 'EUR', // base from where rates are calculated 
	'precision' => 6, // default float precision
	'service'		=> 'http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml',
	'identifier'=> 'currency_helper',  // cache identifier
	'expiration'=> 3600, // cache expiration in seconds
	'convert'		=> array(
		'precision' => 2,
		'code'			=> false,
		'from'			=> 'EUR',
		'to'				=> 'USD'
	),
	'get' => array(
		'sort' => 'ksort'
	),
	'select_menu' => array(
		'field'			=> 'currency', // select name
		'currency'	=> null, // override base currency if needed
		'pattern'		=> '%l (%c)', // where %c is currency code and %l is localized string.
		'sort'			=> 'asort', // php sort function
		'attrs'			=> array() // htm attributes for select menu
	),
	'format' => array(
		'precision'	=> 2,
		'symbol'		=> true,
		'locale'		=> null
	)
);