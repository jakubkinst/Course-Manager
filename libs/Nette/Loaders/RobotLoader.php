<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004, 2011 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Loaders
 */



/**
 * Nette auto loader is responsible for loading classes and interfaces.
 *
 * @author     David Grudl
 */
class RobotLoader extends AutoLoader
{
	/** @var array */
	public $scanDirs;

	/** @var string  comma separated wildcards */
	public $ignoreDirs = '.*, *.old, *.bak, *.tmp, temp';

	/** @var string  comma separated wildcards */
	public $acceptFiles = '*.php, *.php5';

	/** @var bool */
	public $autoRebuild = TRUE;

	/** @var array of lowered-class => [file, mtime, class] or FALSE */
	private $list = array();

	/** @var array of file => mtime */
	private $files;

	/** @var bool */
	private $rebuilt = FALSE;

	/** @var ICacheStorage */
	private $cacheStorage;



	/**
	 */
	public function __construct()
	{
		if (!extension_loaded('tokenizer')) {
			throw new Exception("PHP extension Tokenizer is not loaded.");
		}
	}



	/**
	 * Register autoloader.
	 * @return void
	 */
	public function register()
	{
		$cache = $this->getCache();
		$key = $this->getKey();
		if (isset($cache[$key])) {
			$this->list = $cache[$key];
		} else {
			$this->rebuild();
		}

		if (isset($this->list[strtolower(__CLASS__)]) && class_exists('NetteLoader', FALSE)) {
			NetteLoader::getInstance()->unregister();
		}

		parent::register();
	}



	/**
	 * Handles autoloading of classes or interfaces.
	 * @param  string
	 * @return void
	 */
	public function tryLoad($type)
	{
		$type = ltrim(strtolower($type), '\\'); // PHP namespace bug #49143

		if (isset($this->list[$type][0]) && !is_file($this->list[$type][0])) {
			unset($this->list[$type]);
		}

		if (!isset($this->list[$type])) {
			$trace = debug_backtrace();
			$initiator = & $trace[2]['function'];
			if ($initiator === 'class_exists' || $initiator === 'interface_exists') {
				$this->list[$type] = FALSE;
				if ($this->autoRebuild && $this->rebuilt) {
					$this->getCache()->save($this->getKey(), $this->list, array(
						Cache::CONSTS => 'Framework::REVISION',
					));
				}
			}

			if ($this->autoRebuild && !$this->rebuilt) {
				$this->rebuild();
			}
		}

		if (isset($this->list[$type][0])) {
			LimitedScope::load($this->list[$type][0]);
			self::$count++;
		}
	}



	/**
	 * Rebuilds class list cache.
	 * @return void
	 */
	public function rebuild()
	{
		$this->getCache()->save($this->getKey(), callback($this, '_rebuildCallback'), array(
			Cache::CONSTS => 'Framework::REVISION',
		));
		$this->rebuilt = TRUE;
	}



	/**
	 * @internal
	 */
	public function _rebuildCallback()
	{
		foreach ($this->list as $pair) {
			if ($pair) $this->files[$pair[0]] = $pair[1];
		}
		foreach (array_unique($this->scanDirs) as $dir) {
			$this->scanDirectory($dir);
		}
		$this->files = NULL;
		return $this->list;
	}



	/**
	 * @return array of class => filename
	 */
	public function getIndexedClasses()
	{
		$res = array();
		foreach ($this->list as $class => $pair) {
			if ($pair) $res[$pair[2]] = $pair[0];
		}
		return $res;
	}



	/**
	 * Add directory (or directories) to list.
	 * @param  string|array
	 * @return void
	 * @throws DirectoryNotFoundException if path is not found
	 */
	public function addDirectory($path)
	{
		foreach ((array) $path as $val) {
			$real = realpath($val);
			if ($real === FALSE) {
				throw new DirectoryNotFoundException("Directory '$val' not found.");
			}
			$this->scanDirs[] = $real;
		}
	}



	/**
	 * Add class and file name to the list.
	 * @param  string
	 * @param  string
	 * @param  int
	 * @return void
	 */
	private function addClass($class, $file, $time)
	{
		$lClass = strtolower($class);
		if (isset($this->list[$lClass][0]) && ($file2 = $this->list[$lClass][0]) !== $file && is_file($file2)) {
			if ($this->files[$file2] !== filemtime($file2)) {
				$this->scanScript($file2);
				return $this->addClass($class, $file, $time);
			}
			$e = new InvalidStateException("Ambiguous class '$class' resolution; defined in $file and in " . $this->list[$lClass][0] . ".");
			if (PHP_VERSION_ID < 50300) {
				Debug::_exceptionHandler($e);
				exit;
			} else {
				throw $e;
			}
		}
		$this->list[$lClass] = array($file, $time, $class);
		$this->files[$file] = $time;
	}



	/**
	 * Scan a directory for PHP files, subdirectories and 'netterobots.txt' file.
	 * @param  string
	 * @return void
	 */
	private function scanDirectory($dir)
	{
		if (is_dir($dir)) {
			$disallow = array();
			$iterator = Finder::findFiles(String::split($this->acceptFiles, '#[,\s]+#'))
				->filter(create_function('$file', 'extract(NClosureFix::$vars['.NClosureFix::uses(array('disallow'=>&$disallow)).'], EXTR_REFS);
					return !isset($disallow[$file->getPathname()]);
				'))
				->from($dir)
				->exclude(String::split($this->ignoreDirs, '#[,\s]+#'))
				->filter($filter = create_function('$dir', 'extract(NClosureFix::$vars['.NClosureFix::uses(array('disallow'=>&$disallow)).'], EXTR_REFS);
					$path = $dir->getPathname();
					if (is_file("$path/netterobots.txt")) {
						foreach (file("$path/netterobots.txt") as $s) {
							if ($matches = String::match($s, \'#^disallow\\\\s*:\\\\s*(\\\\S+)#i\')) {
								$disallow[$path . str_replace(\'/\', DIRECTORY_SEPARATOR, rtrim(\'/\' . ltrim($matches[1], \'/\'), \'/\'))] = TRUE;
							}
						}
					}
					return !isset($disallow[$path]);
				'));
			$filter(new SplFileInfo($dir));
		} else {
			$iterator = new ArrayIterator(array(new SplFileInfo($dir)));
		}

		foreach ($iterator as $entry) {
			$path = $entry->getPathname();
			if (!isset($this->files[$path]) || $this->files[$path] !== $entry->getMTime()) {
				$this->scanScript($path);
			}
		}
	}



	/**
	 * Analyse PHP file.
	 * @param  string
	 * @return void
	 */
	private function scanScript($file)
	{
		$T_NAMESPACE = PHP_VERSION_ID < 50300 ? -1 : T_NAMESPACE;
		$T_NS_SEPARATOR = PHP_VERSION_ID < 50300 ? -1 : T_NS_SEPARATOR;

		$expected = FALSE;
		$namespace = '';
		$level = $minLevel = 0;
		$time = filemtime($file);
		$s = file_get_contents($file);

		foreach ($this->list as $class => $pair) {
			if ($pair && $pair[0] === $file) unset($this->list[$class]);
		}

		if ($matches = String::match($s, '#//nette'.'loader=(\S*)#')) {
			foreach (explode(',', $matches[1]) as $name) {
				$this->addClass($name, $file, $time);
			}
			return;
		}

		foreach (token_get_all($s) as $token)
		{
			if (is_array($token)) {
				switch ($token[0]) {
				case T_COMMENT:
				case T_DOC_COMMENT:
				case T_WHITESPACE:
					continue 2;

				case $T_NS_SEPARATOR:
				case T_STRING:
					if ($expected) {
						$name .= $token[1];
					}
					continue 2;

				case $T_NAMESPACE:
				case T_CLASS:
				case T_INTERFACE:
					$expected = $token[0];
					$name = '';
					continue 2;
				case T_CURLY_OPEN:
				case T_DOLLAR_OPEN_CURLY_BRACES:
					$level++;
				}
			}

			if ($expected) {
				switch ($expected) {
				case T_CLASS:
				case T_INTERFACE:
					if ($level === $minLevel) {
						$this->addClass($namespace . $name, $file, $time);
					}
					break;

				case $T_NAMESPACE:
					$namespace = $name ? $name . '\\' : '';
					$minLevel = $token === '{' ? 1 : 0;
				}

				$expected = NULL;
			}

			if ($token === '{') {
				$level++;
			} elseif ($token === '}') {
				$level--;
			}
		}
	}



	/********************* backend ****************d*g**/



	/**
	 * @param  ICacheStorage
	 * @return RobotLoader
	 */
	public function setCacheStorage(ICacheStorage $storage)
	{
		$this->cacheStorage = $storage;
		return $this;
	}



	/**
	 * @return ICacheStorage
	 */
	public function getCacheStorage()
	{
		return $this->cacheStorage;
	}



	/**
	 * @return Cache
	 */
	protected function getCache()
	{
		if (!$this->cacheStorage) {
			trigger_error('Missing cache storage.', E_USER_WARNING);
			$this->cacheStorage = new DummyStorage;
		}
		return new Cache($this->cacheStorage, 'Nette.RobotLoader');
	}



	/**
	 * @return string
	 */
	protected function getKey()
	{
		return "v2|$this->ignoreDirs|$this->acceptFiles|" . implode('|', $this->scanDirs);
	}

}
