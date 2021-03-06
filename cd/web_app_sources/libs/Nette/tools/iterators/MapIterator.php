<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004, 2011 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette
 */



/**
 * Applies the callback to the elements of the inner iterator.
 *
 * @author     David Grudl
 */
class MapIterator extends IteratorIterator
{
	/** @var callback */
	private $callback;


	/**
	 * Constructs a filter around another iterator.
	 * @param
	 * @param  callback
	 */
	function __construct(Traversable $iterator, $callback)
	{
		parent::__construct($iterator);
		$this->callback = $callback;
	}



	function current()
	{
		return call_user_func($this->callback, parent::current(), parent::key());
	}

}
