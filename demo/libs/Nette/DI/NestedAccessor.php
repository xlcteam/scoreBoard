<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\DI
 */



/**
 * The container accessor.
 *
 * @author     David Grudl
 * @internal
 * @package Nette\DI
 */
class NDINestedAccessor extends NObject
{
	/** @var array */
	public $parameters;

	/** @var NDIContainer */
	private $container;

	/** @var string */
	private $namespace;



	public function __construct(NDIContainer $container, $namespace)
	{
		$this->container = $container;
		$this->namespace = $namespace . '_';
		$this->parameters = & $container->parameters[$namespace];
	}



	/**
	 * @return object
	 */
	public function &__get($name)
	{
		$service = $this->container->getService($this->namespace . $name);
		return $service;
	}



	/**
	 * @return void
	 */
	public function __set($name, $service)
	{
		throw new NotSupportedException;
	}



	/**
	 * @return bool
	 */
	public function __isset($name)
	{
		return $this->container->hasService($this->namespace . $name);
	}



	/**
	 * @return void
	 */
	public function __unset($name)
	{
		throw new NotSupportedException;
	}

}
