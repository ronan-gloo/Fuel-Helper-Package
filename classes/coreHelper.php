<?php

namespace Helper;

class HelperException extends \Fuel_Exception {}

/**
 * Shared functions.
 * Core dependencies:
 * 	- Arr
 *	- Inflector
 *	- Config 
 */
class CoreHelper {
	
	protected static $config;
	private static 
		$called_classes = array(),
		$called_class;
	
	/**
	 * Import config rules into $config property.
	 * Config file should be named as Class.
	 * 
	 * @access public
	 * @return void
	 */
	public function _init()
	{
		self::$called_class = str_replace('Helper', '', \Inflector::denamespace(get_called_class()));
		
		if (self::$called_class != __CLASS__)
		{
			self::$called_classes[] = self::$called_class;
			static::$config = \Config::load(self::$called_class, true);
		}
	}
	
	/**
	 * Set configuration on the fly
	 * 
	 * @access public
	 * @static
	 * @param string $key (default: ''): key that correspond to the function
	 * @param array $config (default: array()): config array to merge
	 * @return void
	 */
	public static function set_config($key = '', $config = array())
	{
		static::$config[$key] = $config + static::$config[$key];
	}
	
	/**
	 * Get a key or a the whole configuration for called class.
	 * Search powered by \Fuel\Core\Arr::element();
	 * 
	 * @access public
	 * @static
	 * @param string $key (default: '')
	 * @return void
	 */
	public static function get_config($key = '')
	{
		if (! $key) return static::$config;
		
		return \Arr::element(static::$config, $key);
	}
	
	
	/**
	 * Merge params with default config..
	 * 
	 * @access protected
	 * @static
	 * @param mixed &$args
	 * @param mixed $key
	 * @param bool $as_object (default: false)
	 * @return void
	 */
	protected static function extend(&$args, $key, $as_object = false)
	{
		if (! is_array($args))
		{
			throw new HelperException(self::$called_class.'::'.$key.'() arguments must be an array');	 
		}
		$args = $args + static::$config[$key];
		
		if ($as_object === true) $args = (object)$args;
	}
	
	/**
	 * Set global template key for children class.
	 * 
	 * @access protected
	 * @static
	 * @return void
	 */
	protected static function set_template($key ='', $value = '')
	{
		foreach (static::$config as &$val)
		{
			if (isset($val[$key])) $val[$key] = $value;
		}
	}
	
	/**
	 * Output with precision.
	 * If precision = -1, nothing is done.
	 * 
	 * @access protected
	 * @static
	 * @param mixed $value
	 * @param mixed $precision
	 * @return void
	 */
	protected static function format_number($value, $precision)
	{
		$precision = (int)$precision;

		if ($precision > -1)
		{
			$value = round($value, $precision);
		}
		if (array_key_exists('format', static::$config))
		{
			$format = static::$config['formats'][static::$config['format']];
			if (is_array($format) AND count($format) == 2)
			{
				$value = array_merge(array($value, $precision), $format);
			}
			else
			{
				$value = array($value, $precision);
			}
			$value = call_user_func_array('number_format', $value);
		}
		
		return $value;
	}
	
	/**
	 * Internal function to retrieve language array.
	 * 
	 * @access protected
	 * @static
	 * @return Array
	 */
	protected static function get_lang($lang, $key = null)
	{
		if (__($lang) == $lang)
		{
			\Lang::load($lang, $lang);
		}
		if (trim($key)) $lang = $lang.'.'.$key;
		
	 	return __($lang);
	}


}