<?php

return array(
	'format'	=> \Config::get('language'),
	'formats' => array(
		'fr' => array(2,',', ' '),
		'en' => array(2)
	),
	'percentage' => array(
		'trail'			=> null, // trailing string
		'precision' => 2,
		'mode'			=> 'up' // up or down
	),
	'to_format' => array(
		'precision' => 2
	),
	'to_human' => array(
		'precision' => 2,
		'unit'			=> 'number', // default language unit
		'to'				=> null, // forces base conversion
		'base'			=> array( // do not modify unless you know what you're doing
			'quadrillion' => 15,
			'trillion'		=> 12, 
			'billion'			=> 9,
			'million'			=> 6,
			'thousand'		=> 3,
			'unit'				=> 0,
			'centi'				=> -2,
			'mili'				=> -3,
			'micro'				=> -6,
			'nano'				=> -9,
			'pico'				=> -12,
			'femto'				=> -15
		)
	),
	'to_phone' => array(
		'country_code'=> false,
		'area_code'		=> false,
		'separator'		=> ' ',
		'format'			=> \Config::get('language'),
		'formats'			=> array(
			'en' => array(
				'display'				=> array(2, 3, 3),
				'country_code'	=> 1
			),
			'fr' => array(
				'display'				=> array(2, 2, 2, 2, 2),
				'country_code'	=> 33
			)
		)
	)
);