<?php

return array(
	'to_hms' => array(
		'separator' => ':',
		'pad_hours' => false,
		'negative'	=> false
	),
	'diff' => array(
		'to_hms' => false
	),
	'diff_in_words' => array(
	  'discard_year'	=> false,
	  'discard_month'	=> false,
	  'discard_week'	=> false,
	  'discard_day'		=> false,
	  'discard_hour'	=> false,
	  'discard_minute'=> false,
	  'discard_second'=> false
	),
	'days_in_month' => array(
		'calendar' => CAL_GREGORIAN
	),
	'patterns' => array(
		'local'		=> '%c',
		'mysql'		=> '%Y-%m-%d %H:%M:%S',
		'us'			=> '%m/%d/%Y',
		'us_short'=> '%m/%d',
		'us_named'=> '%B %d %Y',
		'us_full'	=> '%I:%M %p, %B %d %Y',
		'eu'			=> '%d/%m/%Y',
		'eu_short'=> '%d/%m',
		'eu_named'=> '%d %B %Y',
		'eu_full'	=> '%H:%M, %d %B %Y',
		'24h'			=> '%H:%M',
		'12h'			=> '%I:%M %p'
	)
);