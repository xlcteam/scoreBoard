<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Reflection
 */



/**
 * Reports information about a method's parameter.
 *
 * @author     David Grudl
 * @property-read NClassReflection $class
 * @property-read string $className
 * @property-read NClassReflection $declaringClass
 * @property-read NMethodReflection $declaringFunction
 * @property-read string $name
 * @property-read bool $passedByReference
 * @property-read bool $array
 * @property-read int $position
 * @property-read bool $optional
 * @property-read bool $defaultValueAvailable
 * @property-read mixed $defaultValue
 * @package Nette\Reflection
 */
class NParameterReflection extends ReflectionParameter
{
	/** @var mixed */
	private $function;


	public function __construct($function, $parameter)
	{
		parent::__construct($this->function = $function, $parameter);
	}



	/**
	 * @return NClassReflection
	 */
	public function getClass()
	{
		return ($ref = parent::getClass()) ? new NClassReflection($ref->getName()) : NULL;
	}



	/**
	 * @return string
	 */
	public function getClassName()
	{
		return ($tmp = NStrings::match($this, '#>\s+([a-z0-9_\\\\]+)#i')) ? $tmp[1] : NULL;
	}



	/**
	 * @return NClassReflection
	 */
	public function getDeclaringClass()
	{
		return ($ref = parent::getDeclaringClass()) ? new NClassReflection($ref->getName()) : NULL;
	}



	/**
	 * @return NMethodReflection | FunctionReflection
	 */
	public function getDeclaringFunction()
	{
		return is_array($this->function)
			? new NMethodReflection($this->function[0], $this->function[1])
			: new NFunctionReflection($this->function);
	}



	public function __toString()
	{
		return 'Parameter $' . parent::getName() . ' in ' . $this->getDeclaringFunction();
	}



	/********************* NObject behaviour ****************d*g**/



	/**
	 * @return NClassReflection
	 */
	public function getReflection()
	{
		return new NClassReflection($this);
	}



	public function __call($name, $args)
	{
		return NObjectMixin::call($this, $name, $args);
	}



	public function &__get($name)
	{
		return NObjectMixin::get($this, $name);
	}



	public function __set($name, $value)
	{
		return NObjectMixin::set($this, $name, $value);
	}



	public function __isset($name)
	{
		return NObjectMixin::has($this, $name);
	}



	public function __unset($name)
	{
		NObjectMixin::remove($this, $name);
	}

}
