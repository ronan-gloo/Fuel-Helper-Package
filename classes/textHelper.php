<?php

namespace Helper;


/**
 * Core Dependencies:
 * - Html class
 * - Config class
 * - Inflector
 * - Lang
 *
 * @extends CoreHelper
 */
class TextHelper extends CoreHelper {

	protected static $config;


	/**
	 * Set to True by auto_link method, when calling
	 * auto_link_urls() and auto_link_emails()
	 *
	 * (default value: false)
	 *
	 * @var bool
	 * @access protected
	 * @static
	 */
	protected static $is_extended = false;

	/**
	 * Works as the core Iflector::cycle, but with configurable
	 * function args.
	 *
	 * @example
	 * $cycle = Text::cycle('odd', 'even');
	 *
	 * @access public
	 * @static
	 * @return String
	 */
	public static function cycle()
	{
		$args = func_get_args() ?: self::$config['cycle'];

		return function ($next = true) use ($args)
		{
			static $i = 0;
			return $args[($next ? $i++ : $i) % count($args)];
		};
	}


	/**
	 * Auto pluralize words.
	 *
	 * @access public
	 * @static
	 * @param int $count (default: 0): base number. can be an array
	 * @param String $singular: singular string
	 * @param Array $args (optionnal)
	 * "method" ('size' / 'sum'): how to count array values
	 * "count" (true / false) : output count with string
	 * "plural": force string pluralization.
	 * @return String
	 */
	public static function pluralize($count = 0, $singular, $args = array())
	{
		self::extend($args, __FUNCTION__);

		if (is_array($count))
		{
			switch ($args['method'])
			{
			case 'size':
				$count = sizeof($count);
				break;

			case 'sum':
				$count = array_sum($count);
				break;
			}
		}
		if ($count > 1)
		{
			$plural = $args['plural'] ?: \Inflector::pluralize($singular);
			return ($args['count'] === false) ? $plural : $count.' '.$plural;
		}
		return $singular;
	}


	/**
	 * Hilight words in a text.
	 *
	 * @access public
	 * @static
	 * @param mixed $text: the text to search in
	 * @param mixed $words (String or Array): words to highlight
	 * @param array $args :
	 * "match" (string): 'word' or 'string',
	 * "tag" (string): HTML tag name,
	 * "attrs" (array) HTML attributes
	 * @return String
	 */
	public static function highlight($str, $words = array(), $args = array())
	{
		self::extend($args, __FUNCTION__);

		switch ($args['match'])
		{
		case 'word':
			$pattern = '/\b(%s)\b/i';
			break;
		case 'string':
			$pattern = '#(%s)#';
			break;
		}
		foreach ((array)$words as $word)
		{
			$str = preg_replace(sprintf($pattern, $word), html_tag($args['tag'], $args['attrs'], $word), $str);
		}
		return $str;
	}


	/**
	 * Censor words and replace them if needed.
	 *
	 * @access public
	 * @static
	 * @param string $str (default: ""): String to search for censored words
	 * @param string $replace (default: ""): replacement
	 * @param array $censored (default: array()) censored words
	 * @return String
	 */
	public static function censor($str = "", $replace = "", $censored = array())
	{
		$locale  = self::get_lang('text', 'censor');
		$conf   = self::$config['censor'];
		$censored = $censored + $conf['words'];
		$replace = $replace ?: $locale['replace'];

		$repstr = function($badword) use ($replace, $conf)
		{
			return ($conf['repeat'] === false) ? $replace : str_repeat($replace, strlen($badword));
		};

		foreach ($censored as $badword)
		{
			$str = preg_replace("/\b$badword\b/i", "\\1{$repstr($badword)}\\3", $str);
		}

		return $str;
	}


	/**
	 * An alias to htmLawed().
	 * Check http://www.bioinformatics.org/phplabware/internal_utilities/htmLawed/htmLawed_README.htm#s2.2
	 * for sanitizer configuration
	 *
	 * @access public
	 * @static
	 * @param string $str
	 * @param array $args (default: array())
	 * @return void
	 */
	public static function sanitize($str, $args = array())
	{
		self::extend($args, __FUNCTION__);

		if (! function_exists('htmLawed'))
		{
			import('htmlawed/htmlawed', 'vendor');
		}

		return htmLawed($str, $args['config'], $args['spec']);
	}

	/**
	 * Limits a text to a given number of words.
	 * (Inspired by Kohana Framewok)
	 *
	 * @access public
	 * @static
	 * @param string $str: phrase to limit words of
	 * @param array $args (default: array()):
	 *  "length": number of words to keep,
	 *	"trail": ommission string
	 * @return  string
	 */
	public static function limit_words($str, $args = array())
	{
		self::extend($args, __FUNCTION__);

		if (trim($str) === '') return $str;

		preg_match('/^\s*+(?:\S++\s*+){1,'.(int)$args['length'].'}/u', $str, $matches);

		// Only attach the end character if the matched string is shorter
		// than the starting string.
		return rtrim($matches[0]).((strlen($matches[0]) === strlen($str)) ? '' : $args['trail']);
	}


	/**
	 * Limit a text to a given characters.
	 * Alias of \Str:truncate(), with configurable parameters
	 *
	 * @access public
	 * @static
	 * @param string $str: phrase to limit characters of
	 * @param array $args (default: array()):
	 *  "length": number of characters to keep;
	 *	"trail": ommission string
	 * @return  string
	 */
	public static function limit_chars($string, $args = array())
	{
		self::extend($args, __FUNCTION__);

		return \Str::truncate($string, $args['length'], $args['trail']);
	}

	/**
	 * Split text and wrap it into 3 html part: excerpt,
	 * read more, and text (whitout excerpt).
	 * Usefull for Javascript based excerpts...
	 *
	 * @access public
	 * @static
	 * @param mixed $str
	 * @param array $args (default: array())
	 * @return String
	 */
	public static function html_split($str, $args = array())
	{
		self::extend($args, __FUNCTION__);
		self::get_lang('text');

		if (strlen(strip_tags($str)) > $args['length'])
		{
			$args['trail'] = false;
			$excerpt = self::limit_words($str, $args);
			$output  = html_tag($args['excerpt_tag'], $args['excerpt_attrs'], $excerpt);
			$output  .= html_tag($args['following_tag'], $args['following_attrs'], str_replace($excerpt, '', $str));
			$output  .= html_tag($args['readmore_tag'], $args['readmore_attrs'], __('text.readmore'));
		}
		return $output;
	}


	/**
	 * Convert plain text to basic <p> and <br /> HTML.
	 *
	 * @access public
	 * @param mixed $text
	 * @param array $args (default: array()):
	 *  "tag" (string) customize wrapper tag,
	 * "attrs" (array): customize wrapper attrs, see html_tag() function.
	 * @return String
	 */
	public static function html_simple($str, $args = array())
	{
		self::extend($args, __FUNCTION__);

		$str = preg_replace('#(<br\s*?/?>\s*?){2,}#', '</p>'."\n".'<p>', nl2br($str, true));

		return html_tag($args['tag'], $args['attrs'], $str);
	}



	/**
	 * Auto link both emails and urls.
	 *
	 * @access public
	 * @static
	 * @param mixed $str
	 * @param array $args (default: array())
	 * "attrs" (array): html attributes,
	 * "safe_mail" (bool): rely to safe_mail for core Html:mail_to_safe()
	 * @return String
	 */
	public static function auto_link($str, $args = array())
	{
		self::extend($args, __FUNCTION__);

		self::$is_extended = true;
		$output = self::auto_link_emails(self::auto_link_urls($str, $args), $args);
		self::$is_extended = false;

		return $output;
	}


	/**
	 * Auto link Emails found in $str....
	 * (Inspired by cakephp framework)
	 *
	 * @access public
	 * @static
	 * @param mixed $str
	 * @param array $args (default: array())
	 * "attrs" (array): html attributes,
	 * "safe_mail" (bool): rely to safe_mail for core Html:mail_to_safe()
	 * @return String
	 */
	public static function auto_link_emails($str, $args = array())
	{
		if (self::$is_extended === false) self::extend($args, 'auto_link');

		// safe mail or not ?
		$mail_call = ($args['safe_mail'] === true) ? 'mail_to_safe' : 'mail_to';
		$pattern  = '[a-z0-9!#$%&\'*+\/=?^_`{|}~-]';

		return preg_replace_callback(
			'/('.$pattern.'+(?:\.'.$pattern.'+)*@[a-z0-9-]+(?:\.[a-z0-9-]+)+)/i',
			function($matches) use($args, $mail_call) {
				return \Html::$mail_call($matches[0], $matches[0], '', $args['attrs']);
			}, $str);
	}


	/**
	 * Auto link Urls found in $str….
	 * (Inspired by cakephp framework).
	 *
	 * @access public
	 * @static
	 * @param mixed $str: str to parse
	 * @param array $args (default: array()):
	 * "'attrs" (array): HTML attributes
	 * @return String
	 */
	public static function auto_link_urls($str, $args = array())
	{
		if (self::$is_extended === false) self::extend($args, 'auto_link');
		// replace urls
		$to_link = function($prefix, $href) use($args) {
			return \Html::anchor($prefix.$href, $href, $args['attrs']);
		};
		$str = preg_replace_callback(
			'#(?<!href="|">)((?:https?|ftp|nntp)://[^\s<>()]+)#i',
			function ($matches) use($to_link) {return $to_link('', $matches[0]);},
			$str);
		// replace raw urls
		return preg_replace_callback(
			'#(?<!href="|">)(?<!http://|https://|ftp://|nntp://)(www\.[^\n\%\ <]+[^<\n\%\,\.\ <])(?<!\))#i',
			function ($matches) use($to_link) {return $to_link('http://', $matches[0]);},
			$str);
	}

	/**
	 * extract and output keywords from text.
	 * Kewords are sorted by relevance (descending)
	 * Words and punctuations exclusions are located in language file.
	 *
	 * @access public
	 * @static
	 * @param mixed $str
	 * @param array $args. (default: array()):
	 * "min_occur" (int): Minimum of word occurrences in $str to consider,
	 * "min_length" (int): Minimum words length to consider,
	 * "max" (int): Maximum of keywords to consider,
	 * "separator" (string): Ouput keywords separator
	 * @return String
	 */
	public static function keywords($str, $args = array())
	{
		self::extend($args, __FUNCTION__);

		$commons   		= self::get_lang('text', 'keywords');
		$replacements	= array('', '', ' ');
		$patterns			= array(
			'/\b\w{0,'.($args['min_length'] - 1).'}\b/', // less than $args['min_length']
			"#(".implode(' | ', $commons['words']).")#i", // ignored common words
			'/\s\s+/' // extra whitespaces
		);
		// replace punctuations with whitespaces and apply patterns
		$str = preg_replace($patterns, $replacements, str_replace($commons['punct'], ' ', $str));
		// prepare keywords if $str not empty
		if ($str)
		{
			// trim and lower case str, create an array and add occurences count as values
			$words = array_count_values(explode(" ", strtolower(trim($str))));
			// sort the words highest count > lowest count.
			arsort($words);
			// slice array to feet max keywords count
			$words = array_slice($words, 0, $args['max'], true);
			// filter words of < 'min_occur' length
			$words = array_filter($words, function($count) use($args){
					return ($count < $args['min_occur']) ? false : true;
				});
			// return imploded keywords array, according to 'separator' pattern
			$str = implode($args['separator'], array_keys($words));
		}
		return $str;
	}

}