<?php

return array(
	'format'	=> 'en', // number formatting
	'formats' => array( // number formats
		'en' => null, // default number_format
		'fr' => array(',', ' ') // precision is passed by functions…
	), 
	'temperature' => array(
		'precision' => 1,
		'unit'			=> true,
		'from'			=> 'celcius',
		'to'			 	=> 'celcius',
		'units' 		=> array(
			"kelvin" => array(
				"unit" => "K",
				"add"  => -273.15,
				"mult" => 1
			),
			"celcius" => array(
				"unit" => "°C",
				"add"  => 0,
				"mult" => 1
			),
			"fahrenheit" => array(
				"unit" => "°F",
				"add"  => -32,
				"mult" => (5/9)
			),
			"rankine" => array(
				"unit" => "R",
				"add"  => -491.67,
				"mult" => (5/9)
			),
			"newton" => array(
				"unit" => "°N",
				"add"  => -491.67,
				"mult" => (100/33)
			),
			"réaumur" => array(
				"unit" => "°Ré",
				"add"  => 0,
				"mult" => (5/4),
			),
			"rømer" => array(
				"unit" => "°Rø",
				"add"  => -7.5,
				"mult" => (40/21)
			)
		)
	),
	'weight' => array(
		'precision' => 5,
		'unit'			=> true,
		'from'			=> 'gram',
		'to'			 	=> 'gram',
		'units' => array(
			'milligram'	=> 1000,
			'centigram' => 100,
			'gram'			=> 1,
			'kilogram'	=> 0.001,
			'ton'				=> 0.000001,
			'once'			=> (1/28.3495231648),
			'stone'			=> (1/6350.2931799100),
			'pound'			=> (1/453.5923703803)
		)
	),
	'length' => array(
		'precision' => 2,
		'unit'			=> true,
		'from'			=> 'meter',
		'to'			 	=> 'meter',
		'units' => array(
			'millimeter'	=> 1000,
			'centimeter'	=> 100,
			'meter'				=> 1,
			'kilometer'		=> 0.001,
			'inch'				=> 39.3700787,
			'feet'				=> 3.2808399,
			'yard'				=> 1.0936133,
			'mile'				=> 0.00062137119,
			'nmile'				=> 0.0005399568 // nautic mile
			
		)
	),
	'speed' => array(
		'precision' => 2,
		'unit'			=> true,
		'from'			=> 'kmh',
		'to'			 	=> 'kmh',
		'units' => array(
			'kmh'	=> 1,
			'mph' => 0.62137119,
			'knot'=> 0.5399568
		)
	)
);