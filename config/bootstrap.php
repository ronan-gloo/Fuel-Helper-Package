<?php

// where the bootstrap look for classes extending
$helper_bt['override_folder'] = APPPATH.'classes/helper/';

// Classes to register in autoloader and theirs alias
$helper_bt['classes'] = array(
	'CoreHelper'		=> 'Helper',
	'TextHelper'		=> 'Text',
	'NumberHelper'	=> 'Number',
	'TimeHelper'		=> 'Time',
	'CurrencyHelper'=> 'Currency',
	'ConvertHelper'	=> 'Convert'
);
$helper_bt['convert_to_procedural']	= false;
$helper_bt['procedural_blacklist']	= array(
	'helper'	=> array('_init'),
	'time' 		=> array('_init'),
	'text'		=> array('_init'),
	'number'	=> array('_init'),
	'convert'	=> array('_init')
);