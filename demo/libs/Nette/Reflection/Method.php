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
 * Reports information about a method.
 *
 * @author     David Grudl
 * @property-read array $defaultParameters
 * @property-read NClassReflection $declaringClass
 * @property-read NMethodReflection $prototype
 * @property-read NExtensionReflection $extension
 * @property-read array $parameters
 * @property-read array $annotations
 * @property-read string $description
 * @property-read bool $public
 * @property-read bool $private
 * @property-read bool $protected
 * @property-read bool $abstract
 * @property-read bool $final
 * @property-read bool $static
 * @property-read bool $constructor
 * @property-read bool $destructor
 * @property-read int $modifiers
 * @property-write bool $accessible
 * @property-read bool $closure
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
 * @property-read int $startLine
 * @property-read array $staticVariables
 * @package Nette\Reflection
 */
class NMethodReflection extends ReflectionMethod
{

	/**
	 * @param  string|object
	 * @param  string
	 * @return NMethodReflection
	 */
	public static function from($class, $method)
	{
		return new self(is_object($class) ? get_class($class) : $class, $method);
	}



	/**
	 * @return array
	 */
	public function getDefaultParameters()
	{
		return self::buildDefaultParameters(parent::getParameters());
	}



	/**
	 * Invokes method using named parameters.
	 * @param  object
	 * @param  array
	 * @return mixed
	 */
	public function invokeNamedArgs($object, $args)
	{
		return $this->invokeArgs($object, self::combineArgs($this->getDefaultParameters(), $args));
	}



	/**
	 * @return NCallback
	 */
	public function toCallback()
	{
		return new NCallback(parent::getDeclaringClass()->getName(), $this->getName());
	}



	public function __toString()
	{
		return 'Method ' . parent::getDeclaringClass()->getName() . '::' . $this->getName() . '()';
	}



	/********************* Reflection layer ****************d*g**/



	/**
	 * @return NClassReflection
	 */
	public function getDeclaringClass()
	{
		return new NClassReflection(parent::getDeclaringClass()->getName());
	}



	/**
	 * @return NMethodReflection
	 */
	public function getPrototype()
	{
		$prototype = parent::getPrototype();
		return new NMethodReflection($prototype->getDeclaringClass()->getName(), $prototype->getName());
	}



	/**
	 * @return NExtensionReflection
	 */
	public function getExtension()
	{
		return ($name = $this->getExtensionName()) ? new NExtensionReflection($name) : NULL;
	}



	public function getParameters()
	{
		$me = array(parent::getDeclaringClass()->getName(), $this->getName());
		foreach ($res = parent::getParameters() as $key => $val) {
			$res[$key] = new NParameterReflection($me, $val->getName());
		}
		return $res;
	}



	/********************* NAnnotations support ****************d*g**/



	/**
	 * Has method specified annotation?
	 * @param  string
	 * @return bool
	 */
	public function hasAnnotation($name)
	{
		$res = NAnnotationsParser::getAll($this);
		return !empty($res[$name]);
	}



	/**
	 * Returns an annotation value.
	 * @param  string
	 * @return IAnnotation
	 */
	public function getAnnotation($name)
	{
		$res = NAnnotationsParser::getAll($this);
		return isset($res[$name]) ? end($res[$name]) : NULL;
	}



	/**
	 * Returns all annotations.
	 * @return array
	 */
	public function getAnnotations()
	{
		return NAnnotationsParser::getAll($this);
	}



	/**
	 * Returns value of annotation 'description'.
	 * @return string
	 */
	public function getDescription()
	{
		return $this->getAnnotation('description');
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



	/********************* helpers ****************d*g**/



	/** @internal */
	public static function buildDefaultParameters($params)
	{
		$res = array();
		foreach ($params as $param) {
			$res[$param->getName()] = $param->isDefaultValueAvailable()
				? $param->getDefaultValue()
				: NULL;

			if ($param->isArray()) {
				settype($res[$param->getName()], 'array');
			}
		}
		return $res;
	}



	/** @internal */
	public static function combineArgs($params, $args)
	{
		$res = array();
		$i = 0;
		foreach ($params as $name => $def) {
			if (isset($args[$name])) { // NULL treats as none value
				$val = $args[$name];
				if ($def !== NULL) {
					settype($val, gettype($def));
				}
				$res[$i++] = $val;
			} else {
				$res[$i++] = $def;
			}
		}
		return $res;
	}

}
