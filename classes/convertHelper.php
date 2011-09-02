<?php

namespace Helper;


/**
 * Some convertion methods.
 * Support for temperature, Weight, Speed, Length.
 * 
 * @extends MainHelper
 * @description
 * Formats are used by php mumber_format() function:
 *	"format" (string): Template name from 'formats',
 *	"formats" (Array): Set callable format Array((dec_point), (thousands_sep)).
 * used by number_format() function.
 */
class ConvertHelper extends CoreHelper {
	
	protected static $config;
	
	/**
	 * Convert temperatures.
	 * Based on http://www.phpsnaps.com/snaps/view/temperature-converter/
	 * 
	 * @access public
	 * @static
	 * @param int $value (default: 1): Number to convert
	 * @param array $args (default: array()):
	 *	"precision" (int): decimals,
	 *	"unit" (bool): display 'to' unit,
	 *	"from" (string): unit base,
	 *	"to" (string): unit destination
	 * @return String
	 */
	public static function temperature($value = 1, $args = array())
	{
		self::extend($args, __FUNCTION__);
		
		// clean code reading
		list($from, $to) = array($args['units'][$args['from']], $args['units'][$args['to']]);
		
		if ($value AND $from != $to)
		{
			$celcius	 = ((float)$value + $from["add"]) * $from["mult"];
			$value		 = self::format_number(($celcius * (1 / $to["mult"])) - $to["add"], $args['precision']);
		}
		// append unit str if needed
		$value .= ($args['unit'] === true) ? ' '.$to['unit'] : '';

		return $value;
	}
	
	
	/**
	 * Convert weight.
	 *
	 * @access public
	 * @static
	 * @param int $value (default: 1): Number to convert
	 * @param array $args (default: array()):
	 *	"precision" (int): decimals,
	 *	"unit" (bool): display 'to' unit,
	 *	"from" (string): unit base,
	 *	"to" (string): unit destination
	 * @return String
	 */
	public static function weight($value = 1, $args = array())
	{
		return self::generic_calculator('weight', $value, $args);
	}
	
	/**
	 * Convert Speed.
	 * 
	 * @access public
	 * @static
	 * @param int $value (default: 1): Number to convert
	 * @param array $args (default: array()):
	 *	"precision" (int): decimals,
	 *	"unit" (bool): display 'to' unit,
	 *	"from" (string): unit base,
	 *	"to" (string): unit destination
	 * @return String
	 */
	public static function speed($value = 1, $args = array())
	{
		return self::generic_calculator('speed', $value, $args);
	}
	
	
	/**
	 * Convert Lenght.
	 * 
	 * @access public
	 * @static
	 * @param int $value (default: 1): Number to convert
	 * @param array $args (default: array()):
	 *	"precision" (int): decimals,
	 *	"unit" (bool): display 'to' unit,
	 *	"from" (string): unit base,
	 *	"to" (string): unit destination
	 * @return String
	 */
	public static function length($value = 1, $args = array())
	{
		return self::generic_calculator('length', $value, $args);
	}
	
	/**
	 * generic_calculator function.
	 * 
	 * @access private
	 * @static
	 * @param mixed $func
	 * @param mixed $value
	 * @param mixed $args
	 */
	protected static function generic_calculator($func, $value, $args)
	{
		self::extend($args, $func);
		
		list($from, $to) = array($args['units'][$args['from']], $args['units'][$args['to']]);
		$value = self::format_number((($value / $from) * $to), $args['precision']);
		
		if (($args['unit'] === true))
		{
			$lang	  = self::get_lang('convert');
			$value .= " ".(($value > 1) ? \Inflector::pluralize($lang[$args['to']]) : $lang[$args['to']]);
		}
		return $value;
	}
}
