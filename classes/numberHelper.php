<?php

namespace Helper;


/**
 * Work with numeric data
 *
 * @extends CoreHelper
 */
class NumberHelper extends CoreHelper {
	
	protected static $config;
	
	protected static
		$locale = null,
		$units = null;
	
	
	/**
	 * If you need to override LC_MONETARY locale at some point…
	 * 
	 * @access private
	 * @static
	 * @param string $locale (default: '')
	 * @return void
	 */
	protected static function set_locale($locale = null)
	{
		if ($locale) setlocale(LC_NUMERIC, $locale);
	}
	
	
	/**
	 * Alias to set_template from .
	 * 
	 * @access public
	 * @static
	 * @param string $value (default: '')
	 * @return void
	 */
	public static function set_formats($value = '')
	{
		return parent::set_template('format', $value);
	}	
	
	/**
	 * Calculate the percentage of a number
	 * 
	 * @access public
	 * @static
	 * @param int $num (default: 1): amount to calculate
	 * @param int $base (default: 100): total reference
	 * @param array $args (default: array()):
	 *	"mode" (string: up / down): round method,
	 *	"precision" (int): float precision,
	 *	"trail" (string): append chars (like "%")
	 * @return float or string
	 */
	public static function percentage($num = 1, $base = null, $args = array())
	{
		self::extend($args, __FUNCTION__);
		
		if (! $base OR ! is_numeric($base))
		{
			$base = 100;
		}
		
		switch ($args['mode'])
		{
			case 'up':
			$mode = PHP_ROUND_HALF_UP;
			break;
			
			case 'down':
			$mode = PHP_ROUND_HALF_DOWN;
			break;
		}
		$num = round(($num / (float)$base) * 100, $args['precision'], $mode);
		
		return ($args['trail']) ? $num.$args['trail'] : $num;
	}
	
	
	/**
	 * Convert number tou human readeable format with specified unit..
	 * 
	 * @access public
	 * @static
	 * @param mixed $num: numeric value
	 * @param array $args (default: array())
	 *	"precision" (int): float precision,
	 *	"unit" (string): language unit (they are configured in language file),
	 *	"to" (string): Force convertion to specific unit
	 * @return String
	 */
	public static function to_human($num, $args = array())
	{
		self::extend($args, __FUNCTION__);
		self::get_lang('number');
		// unit is forced
		if (array_key_exists($args['to'], $args['base']))
		{
			$num = ($num / pow(10, $args['base'][$args['to']]));
			$key = $args['to'];
		}
		// check correct unit. strlen() comparaison is neccessary < base unit value
		else
		{
			foreach ($args['base'] as $key => $val)
			{
				if ($exp = pow(10, $val) AND $num >= $exp AND strlen($exp) >= strlen($num))
				{
					$num = ($num / $exp);
					break;
				}
			}
		}
		return round($num, $args['precision']).' '.__('number.'.$args['unit'].'.'.$key);
	}
		
	/**
	 * Convert number to phone readable format
	 * 
	 * @todo: Define what to do if number length don't match steps length
	 * @access public
	 * @static
	 * @param mixed $num: numeric value
	 * @param array $args (default: array()):
	 *	"format" (string): select a pattern from the config file
	 *	"country_code" (bool): display country code
	 *	"area_code" (bool): format first numeric group to area code
	 * @return String
	 */
	public static function to_phone($num, $args = array())
	{
		self::extend($args, __FUNCTION__);
		
		// first, remove all undesired chars
		$num				= preg_replace("/[^0-9]/","", $num);		
		$format			= $args['formats'][$args['format']];
		$formatted	= ($args['country_code'] == true) 
			? '+'.$format['country_code'].$args['separator']
			: '';
		
		//check if number length is equal to format steps
		//if (strlen($num) !== array_sum($format['display'])) {}
		
		$index = 0;
		foreach ($format['display'] as $step)
		{
			if ($index === 0 AND $args['area_code'] === true)
			{
				$formatted .= '('.substr($num, $index, $step).')'.$args['separator'];
			}
			else
			{
				$formatted .= substr($num, $index, $step).$args['separator'];
			}
			$index += $step;
		}
		return rtrim($formatted, $args['separator']);
	}
	
	
	/**
	 * Convert Byte to human readable size format.
	 * Size expressions are located in language file.
	 * 
	 * @access public
	 * @static
	 * @param mixed $num: bytes to pass
	 * @param int $precision (default: 2): float precision
	 * @return String
	 */
	public static function to_size($num, $precision = 2)
	{
		self::get_lang('number');
		
		if ($num >= 1000000000000)
		{
			$num = round($num / 1099511627776, $precision);
			$unit = __('number.tb');
		}
		elseif ($num >= 1000000000)
		{
			$num = round($num / 1073741824, $precision);
			$unit = __('number.gb');
		}
		elseif ($num >= 1000000)
		{
			$num = round($num / 1048576, $precision);
			$unit = __('number.mb');
		}
		elseif ($num >= 1000)
		{
			$num = round($num / 1024, $precision);
			$unit = __('number.kb');
		}
		else
		{
			$unit = __('number.b');
			return number_format($num).' '.$unit;
		}
		return self::format_number($num, $precision).' '.$unit;
	}
	
	
	/**
	 * Format delimiters in specific format template(related to config by default).
	 * 
	 * @access public
	 * @static
	 * @param mixed $num
	 * @param array $args (default: array()): respect order ! decimals, separator, thousands
	 * @return Number
	 */
	public static function to_format($num, $format = null, $args = array())
	{
		$format = $format ?: self::$config['format'];
		$args		= $args + (array)self::$config['formats'][$format];

		return call_user_func_array('number_format', array_merge((array)$num, $args));
	}
}