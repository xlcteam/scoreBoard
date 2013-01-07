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
 * Reports information about a function.
 *
 * @author     David Grudl
 * @property-read array $defaultParameters
 * @property-read bool $closure
 * @property-read NExtensionReflection $extension
 * @property-read array $parameters
 * @property-read bool $disabled
 * @property-read bool $deprecated
 * @property-read bool $internal
 * @property-read bool $userDefined
 * @property-read string $docComment
 * @property-read int $endLine
 * @property-read string $extensionName
 * @property-read string $fileName
 * @property-read string $name
 * @property-read string $namespaceName
 * @property-read int $numberOfParameters
 * @property-read int $numberOfRequiredParameters
 * @property-read string $shortName
 * @property-read intr $startLine
 * @property-read array $staticVariables
 * @package Nette\Reflection
 */
class NFunctionReflection extends ReflectionFunction
{
	/** @var string|Closure */
	private $value;


	public function __construct($name)
	{
		parent::__construct($this->value = $name);
	}



	/**
	 * @return array
	 */
	public function getDefaultParameters()
	{
		return NMethodReflection::buildDefaultParameters(parent::getParameters());
	}



	/**
	 * Invokes function using named parameters.
	 * @param  array
	 * @return mixed
	 */
	public function invokeNamedArgs($args)
	{
		return $this->invokeArgs(NMethodReflection::combineArgs($this->getDefaultParameters(), $args));
	}



	/**
	 * @return NCallback
	 */
	public function toCallback()
	{
		return new NCallback($this->value);
	}



	public function __toString()
	{
		return 'Function ' . $this->getName() . '()';
	}



	public function getClosure()
	{
		return $this->isClosure() ? $this->value : NULL;
	}



	/********************* Reflection layer ****************d*g**/



	/**
	 * @return NExtensionReflection
	 */
	public function getExtension()
	{
		return ($name = $this->getExtensionName()) ? new NExtensionReflection($name) : NULL;
	}



	public function getParameters()
	{
		foreach ($res = parent::getParameters() as $key => $val) {
			$res[$key] = new NParameterReflection($this->value, $val->getName());
		}
		return $res;
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
