<?php

namespace Helper;


/**
 * All functions supports both SQL Datetime, time string and timestamps.
 * Localization is by default related to app configuration.
 * 
 * @extends Helper
 */
class TimeHelper extends CoreHelper {
	
	protected static $config;
	
	private static
		$date = null,
		$datetime_pattern = '%Y-%m-%d %H:%M:%S';

	/**
	 * If you need to override LC_TIME locale at some point.
	 * 
	 * @access public
	 * @static
	 * @param mixed $locale (default: null)
	 * @return void
	 */
	public static function set_locale($locale = null)
	{
		if ($locale) setlocale(LC_TIME, $locale);
	}
	/**
	 * Return Timestamp from anything.
	 * If no argument, current timestamp is used. 
	 * 
	 * @access public
	 * @static
	 * @param mixed $time
	 * @return void
	 */
	public static function to_timestamp($time = null)
	{
		return self::get_time($time);
	}
	
	/**
	 * Return Datetime from anything.
	 * If no argument, current datetime is used. 
	 * 
	 * @access public
	 * @static
	 * @param mixed $timestamp
	 * @return void
	 */
	public static function to_datetime($time = null)
	{
		return strftime(self::$datetime_pattern, self::get_time($time));
	}
	
	/**
	 * if $format is not a valid key, we fallback on value passed, so you can
	 * to pass your own pattern strftime() formatted pattern.
	 * 
	 * @access public
	 * @static
	 * @param mixed $time (default: null)
	 * @param string $format (default: 'default')
	 * @param array $args (default: array())
	 * @return void
	 */
	public static function to_format($format = null, $time = null, $args = array())
	{
		self::extend($args, 'patterns');
		self::get_time($time);
		
		return strftime(isset($args[$format]) ? $args[$format] : $format, $time);
	}
	
	/**
	 * Seconds to formatted hms value.
	 * 
	 * @access public
	 * @static
	 * @param int $sec (default: 0): seconds value
	 * @param Array $args (default: array()). available args:
	 * 	- pad_hours (bool)
	 *	- separator (string): h - m - s sperator
	 *	- negative (bool): ouput negative sign for negative values
	 * @return void
	 */
	public static function to_hms($sec = 0, $args = array()) 
	{
		self::extend($args, __FUNCTION__);
		
    $hms = "";
		if ($sec < 0)
		{
			$sec = abs($sec); // avoid negative sign on each value
			$hms = ($args['negative'] === true) ? '-' : $hms;
		}
    $hours = intval(intval($sec) / 3600); 
    $hms	.= ($args['pad_hours']) 
			? str_pad($hours, 2, "0", STR_PAD_LEFT).$args['separator']
			: $hours.$args['separator'];
    $minutes = intval(($sec / 60) % 60); 
    $hms		.= str_pad($minutes, 2, "0", STR_PAD_LEFT).$args['separator'];
    $seconds = intval($sec % 60); 
    $hms		.= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    return $hms;
	}
	
	/**
	 * Return seconds interval beetween to dates.
	 * 
	 * @access public
	 * @static
	 * @param mixed $from_time
	 * @param mixed $to_time
	 * @param array $args (default: array()): Available args:
	 *	- to_hms (string): output seconds_to_hms()
	 *	- seconds_to_hms() arguments.
	 * @return void
	 */
	public static function diff($from, $to, $args = array())
	{
		self::extend($args, __FUNCTION__);
		self::get_time($from);
		self::get_time($to);
		
		$output = ($to - $from);
		return ($args['to_hms'] == true) ? self::seconds_to_hms($output, $args) : $output;
	}
	
	
	/**
	 * Display days numbers in given month.
	 * If no year is passed, we assume a full date…
	 * If no args, we use the current month.
	 * 
	 * @access public
	 * @static
	 * @param mixed $month
	 * @param mixed $year (default: null)
	 * @param array $args (default: array()):
	 *	- calendar: cal_days_in_month() calendar type
	 * @return void
	 */
	public static function days_in_month($month = null, $year = null, $args = array())
	{
		self::extend($args, __FUNCTION__);
		
		if (! $year AND strlen($month) != 2)
		{
			self::get_time($month);
			list($year, $month) = explode('-', date('Y-m', $month));
		}
		else if (! $year AND strlen($month) == 2)
		{
			$year = date('Y');
		}
		
		return cal_days_in_month($args['calendar'], $month, $year);
	}
	
	/**
	 * Output in words the approximative interval in time between $from and $args.
	 * 
	 * @access public
	 * @static
	 * @param mixed $from_time: distance from $time
	 * @param mixed $time (default: null): distance reference, now by default
	 * @param array $args (default: array()). Available arguments:
	 *	- prefix_text (string) (default:'there is'): prefix output.
	 *	- separator (string) (default: ', '): separation string for years, month… etc
	 *	- discard_* (bool) (default: false): don't output year, month..etc 
	 * @return void
	 */
		
	public static function diff_in_words($from = 'now', $to = 'now', $args = array())
	{
		\Lang::load('time', 'time');
		self::extend($args, __FUNCTION__);
		
		// instancitae Datetimes
		$from = new \DateTime($from); 
		$to		= new \DateTime($to); 
    // calculate interval
    $diff = $from->diff($to);
		//check for negative interval
    $prefix	= ($diff->invert === 1) ? __('time.diff_neg') : __('time.diff_pos');
    // pluralize string closure
    $doPlural = function($nb, $str){return ($nb > 1) ? __($str.'s') : __($str);}; // adds plurals 
    
    $format = array(); 
    
    if ($diff->y !== 0 AND ! $args['discard_year'])
			$format[] = "%y ".$doPlural($diff->y, "time.year"); 

    if ($diff->m !== 0 AND ! $args['discard_month'])
			$format[] = "%m ".$doPlural($diff->m, "time.month"); 

    if ($diff->d !== 0 AND ! $args['discard_day'])
			$format[] = "%d ".$doPlural($diff->d, "time.day"); 

    if ($diff->h !== 0 AND ! $args['discard_hour'])
			$format[] = "%h ".$doPlural($diff->h, "time.hour"); 

    if ($diff->i !== 0 AND ! $args['discard_minute'])
			$format[] = "%i ".$doPlural($diff->i, "time.minute"); 
    
    if ($diff->s !== 0)
    { 
			if ($args['discard_second'] AND ! count($format))
				return __('time.diff_ltm');
			
			else if (! $args['discard_second'])
				$format[] = "%s ".$doPlural($diff->s, "time.second");
    } 
    // We use the two biggest parts 
    if (count($format) > 1)
			$format = array_shift($format). __('time.diff_sep').array_shift($format); 
    
    else 
			$format = array_pop($format);

    return ((count($format) > 0) ? $prefix.' ' : null).$diff->format($format); 
	} 
	
	/**
	 * Convert any input in timestamp.
	 * 
	 * @access private
	 * @static
	 * @param mixed &$time
	 * @return void
	 */
	private static function get_time(&$time)
	{
		$time = $time ?: time();
		$time = (is_numeric($time)) ? $time : strtotime($time);
		return $time;
	}
	
}