<?php
return array(
	'cycle' => array(
		'odd','even'
	),
	'pluralize' => array(
		'method'	=> 'size', // size or sum for array
		'count'		=> true, // return count or not
		'plural'	=> ''
	),
	'highlight' => array(
		'tag'		=> 'strong',
		'attrs' => array('class' => 'highlight'),
		'match' => 'word' // word or strong
	),
	'limit_words' => array(
		'length'=> 100,
		'trail' => '...'
	),
	'limit_chars' => array(
		'length'=> 100,
		'trail' => '...'
	),
	'censor' => array(
		'replace' => '#',
		'repeat'	=> true,
		'words'		=> array()
	),
	'sanitize' => array(
		'spec'		=> null,
		'config'	=> array(
			'safe'		=> 1,
			'balance'	=> 0,
			'comment'	=> 1
		)
	),
	'html_split' => array(
		'length'					=> 200,
		'trail'						=> '...',
		'excerpt_tag'			=> 'p',
		'excerpt_attrs'		=> array('class' => 'excerpt'),
		'readmore_tag'		=> 'p',
		'readmore_attrs'	=> array('class' => 'readmore'),
		'following_tag'		=> 'p',
		'following_attrs'	=> array('class' => 'following'),
	),
	'html_simple' => array(
		'tag'		=> 'p',
		'attrs' => array()
	),
	'auto_link' => array(
		'attrs'			=> array(),
		'safe_mail' => true,
	),
	'keywords' => array(
		'min_length' 	=> 3, // minimum words length
		'min_occur'		=> 3, // minimum words occurences in text
		'max'					=> 10, // maximum keywords numbers
		'separator'		=> ', ' // Ouput keywords separator
	)
);