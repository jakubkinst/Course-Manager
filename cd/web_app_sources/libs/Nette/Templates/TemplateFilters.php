<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004, 2011 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Templates
 */



/**
 * Standard template compile-time filters shipped with Nette Framework (http://nette.org)
 *
 * @author     David Grudl
 */
final class TemplateFilters
{

	/**
	 * Static class - cannot be instantiated.
	 */
	final public function __construct()
	{
		throw new LogicException("Cannot instantiate static class " . get_class($this));
	}



	/********************* Filter removePhp ****************d*g**/



	/**
	 * Filters out PHP code.
	 * @param  string
	 * @return string
	 */
	public static function removePhp($s)
	{
		return String::replace($s, '#\x01@php:p\d+@\x02#', '<?php ?>'); // Template hides PHP code in these snippets
	}



	/********************* Filter relativeLinks ****************d*g**/



	/**
	 * Filter relativeLinks: prepends root to relative links.
	 * @param  string
	 * @return string
	 */
	public static function relativeLinks($s)
	{
		return String::replace(
			$s,
			'#(src|href|action)\s*=\s*(["\'])(?![a-z]+:|[\x01/\\#])#', // \x01 is PHP snippet
			'$1=$2<?php echo \\$basePath ?>/'
		);
	}



	/********************* Filter netteLinks ****************d*g**/



	/**
	 * Filter netteLinks: translates links "nette:...".
	 *   nette:destination?arg
	 * @param  string
	 * @return string
	 */
	public static function netteLinks($s)
	{
		return String::replace($s, '#(src|href|action)\s*=\s*(["\'])(nette:.*?)([\#"\'])#',	callback(create_function('$m', '
				list(, $attr, $quote, $uri, $fragment) = $m;
				$parts = parse_url($uri);
				if (isset($parts[\'scheme\']) && $parts[\'scheme\'] === \'nette\') {
					return $attr . \'=\' . $quote . \'<?php echo $template->escape($control->\'
						. "link(\'"
						. (isset($parts[\'path\']) ? $parts[\'path\'] : \'this!\')
						. (isset($parts[\'query\']) ? \'?\' . $parts[\'query\'] : \'\')
						. \'\\\'))?>\'
						. $fragment;
				} else {
					return $m[0];
				}
			')));
	}



	/********************* Filter texyElements ****************d*g**/



	/** @var Texy */
	public static $texy;



	/**
	 * Process <texy>...</texy> elements.
	 * @param  string
	 * @return string
	 */
	public static function texyElements($s)
	{
		return String::replace($s, '#<texy([^>]*)>(.*?)</texy>#s', callback(create_function('$m', '
				list(, $mAttrs, $mContent) = $m;
				// parse attributes
				$attrs = array();
				if ($mAttrs) {
					foreach (String::matchAll($mAttrs, \'#([a-z0-9:-]+)\\s*(?:=\\s*(\\\'[^\\\']*\\\'|"[^"]*"|[^\\\'"\\s]+))?()#isu\') as $m) {
						$key = strtolower($m[1]);
						$val = $m[2];
						if ($val == NULL) $attrs[$key] = TRUE;
						elseif ($val{0} === \'\\\'\' || $val{0} === \'"\') $attrs[$key] = html_entity_decode(substr($val, 1, -1), ENT_QUOTES, \'UTF-8\');
						else $attrs[$key] = html_entity_decode($val, ENT_QUOTES, \'UTF-8\');
					}
				}
				return TemplateFilters::$texy->process($m[2]);
			')));
	}

}
