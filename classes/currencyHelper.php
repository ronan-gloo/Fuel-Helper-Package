<?php

namespace Helper;


/**
 * Provide methods to works with currencies
 *
 * @description:
 * When called, this Helper automatically check for rates.
 * You can override fetch_data() method to set your own $data array.
 * Currency rates are by default grabbed
 * from http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml.
 * You can specify to cache xml in config file.
 * 
 * @extends MainHelper
 */
class CurrencyHelper extends CoreHelper {
	
	protected static $config;
	
	/**
	 * Associative array for currency rates.
	 * Key: Currency Code,
	 * Value: CurrencyRate
	 * 
	 * @var mixed
	 * @access private
	 * @static
	 */
	protected static $data = null;
		
	/**
	 * List all available currency, and their values.
	 * 
	 * @access public
	 * @static
	 * @param mixed $currency (default: null): Currency code, allow you to recalculate rates from passed code.
	 * @param array $args (default: array()):
	 *	"sort" (string, default 'ksort') : php function name to sort currencies array.
	 * @return Array
	 */
	public static function get($currency = null, $args = array())
	{
		if (is_null(self::$data)) self::fetch_data();
		
		self::extend($args, __FUNCTION__);
		if ($currency) self::rebase($currency);
		
		$args['sort'](self::$data);
		
		return self::$data;
	}
	
	/**
	 * Format number to locale currency.
	 * 
	 * @access public
	 * @static
	 * @param mixed $num: number to format
	 * @param array $args (default: array()):
	 *	"locale" (string, default = global app locale): set custom locale for money_format(),
	 *	"precision" (int, default = 2): float precision,
	 *	"symbol" (bool, default = true): use currency symbol instead of 3 letters code.
	 * @return String
	 */
	public static function format($num, $args = array())
	{
		self::extend($args, __FUNCTION__);

		// Change locale for output
		if ($args['locale'])
		{
			setlocale(LC_MONETARY, $args['locale']);
		}
		
		// number to currency conversion
		$str = money_format('%.'.$args['precision'].'i', $num);

		// Ouput symbol instead of code
		if ($args['symbol'] === true)
		{
			$tra = self::get_lang('currency');
			$str = preg_replace_callback("/[A-Z]{3}/", function($match) use($tra) {
				return $tra[$match[0]][1];
			}, $str);
		}
		
		return $str;
	}

	/**
	 * Convert Currency to other currency....
	 * 
	 * @access public
	 * @static
	 * @param int $value (default: 1)
	 * @param array $args (default: array()):
	 * 	- "from" (string): Currency code reference,
	 *	"to" (string): Currency code to convert,
	 *	"precision" (int, default = 6): float precision
	 * @return Float
	 */
	public static function convert($value = 1, $args = array())
	{
		if (is_null(self::$data)) self::fetch_data();
		
		self::extend($args, __FUNCTION__);

		$num = round((self::$data[$args['to']] * $value) / self::$data[$args['from']], $args['precision']);
		
		return ($args['code'] === true) ? $num.' '.$args['to'] : $num;
	}
		
		
	/**
	 * Display a HTML select menu from currencies.
	 * 
	 * 
	 * @access public
	 * @static
	 * @param mixed $field (default: null): name attribute for <select>. Default in configured in config file.
	 * @param array $args (default: array()):
	 *	"currency" (string, default = 'EUR'): Base currency to calculate rates,
	 *	"pattern" (string, default = '%l (%c)'): <option> text, where %c is currency code and %l is localized string,
	 *	"sort" (string, default = 'asort'): php function name to sort select,
	 *	"attrs" (array, default = array()): HTML atributes
	 * @return String
	 */
	public static function select_menu($field = null, $args = array())
	{
		if (is_null(self::$data)) self::fetch_data();
				
		self::extend($args, __FUNCTION__);
		
		if (! $field) $field = $args['field'];
		if ($args['currency']) self::rebase($currency, $args);
		// load lang for currency list
		$localized	= self::get_lang('currency');
		$select			= array();
		// value should match configured pattern
		foreach (self::$data as $key => $val)
		{
			$select[(string)$val] = str_replace(
				array('%c', '%l'),
				array($key, $localized[$key][0]),
				$args['pattern']
			);
		}
		// sort array for select
		if ($args['sort']) $args['sort']($select);
		// return select menu
		return \Form::select($args['field'], null, $select, $args['attrs']);
	}
	
		
	/**
	 * Fetch data from convertion service.
	 * Results are cached if expiration is set in config file.
	 * 
	 * @access protected
	 * @static
	 * @return Bool
	 */
	protected static function fetch_data()
	{
		try
		{
			self::$data = \Cache::get(self::$config['identifier']);
		}
		catch (\CacheNotFoundException $e)
		{
			// load xml
			$data = @simplexml_load_file(self::$config['service']);
			
			if ($data)
			{
				// Build an associative array from simplexml object
				$nodes	= $data->children()->children()->children();
				foreach ($nodes as $node)
				{
					self::$data[(string)$node->attributes()->currency] = (float)$node->attributes()->rate;
				}
				// add EUR as base rate
				self::$data['EUR'] = 1;
				
				// recalculate values if needed
				if (self::$config['base_rate'] != 'EUR')
				{
					self::rebase(self::$config['base_rate']);
				}
				// use cache if specified
				if (self::$config['expiration'] > 0)
				{
					\Cache::set(self::$config['identifier'], self::$data, self::$config['expiration']);
				}
			}
		}
		return (self::$data) ? true : false;
	}

	/**
	 * Rebase from specific currency.
	 * if $currency is not a valid value, nothing is done.
	 * Precision is set in config file.
	 * 
	 * @access protected
	 * @static
	 * @param string $currency (default: 'EUR'): Base currency for rates
	 * @return Array
	 */
	protected static function rebase($currency = null)
	{
		if (array_key_exists($currency, self::$data) AND self::$data[$currency] !== 1)
		{
			$ratio = (1 / self::$data[$currency]);
			foreach (self::$data as &$value)
			{
				$value = round(($value * $ratio), self::$config['precision']);
			}
		}
		return self::$data;
	}
		
}