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
 * Basic container builder.
 *
 * @author     David Grudl
 * @property-read array $definitions
 * @property-read array $dependencies
 * @package Nette\DI
 */
class NDIContainerBuilder extends NObject
{
	const CREATED_SERVICE = 'self',
		THIS_CONTAINER = 'container';

	/** @var array  %param% will be expanded */
	public $parameters = array();

	/** @var array of NDIServiceDefinition */
	private $definitions = array();

	/** @var array for auto-wiring */
	private $classes;

	/** @var array of file names */
	private $dependencies = array();



	/**
	 * Adds new service definition. The expressions %param% and @service will be expanded.
	 * @param  string
	 * @return NDIServiceDefinition
	 */
	public function addDefinition($name)
	{
		if (isset($this->definitions[$name])) {
			throw new InvalidStateException("Service '$name' has already been added.");
		}
		return $this->definitions[$name] = new NDIServiceDefinition;
	}



	/**
	 * Removes the specified service definition.
	 * @param  string
	 * @return void
	 */
	public function removeDefinition($name)
	{
		unset($this->definitions[$name]);
	}



	/**
	 * Gets the service definition.
	 * @param  string
	 * @return NDIServiceDefinition
	 */
	public function getDefinition($name)
	{
		if (!isset($this->definitions[$name])) {
			throw new NMissingServiceException("Service '$name' not found.");
		}
		return $this->definitions[$name];
	}



	/**
	 * Gets all service definitions.
	 * @return array
	 */
	public function getDefinitions()
	{
		return $this->definitions;
	}



	/**
	 * Does the service definition exist?
	 * @param  string
	 * @return bool
	 */
	public function hasDefinition($name)
	{
		return isset($this->definitions[$name]);
	}



	/********************* class resolving ****************d*g**/



	/**
	 * Resolves service name by type.
	 * @param  string  class or interface
	 * @return string  service name or NULL
	 * @throws NServiceCreationException
	 */
	public function getByType($class)
	{
		$lower = ltrim(strtolower($class), '\\');
		if (!isset($this->classes[$lower])) {
			return;

		} elseif (count($this->classes[$lower]) === 1) {
			return $this->classes[$lower][0];

		} else {
			throw new NServiceCreationException("Multiple services of type $class found: " . implode(', ', $this->classes[$lower]));
		}
	}



	/**
	 * Gets the service objects of the specified tag.
	 * @param  string
	 * @return array of [service name => tag attributes]
	 */
	public function findByTag($tag)
	{
		$found = array();
		foreach ($this->definitions as $name => $def) {
			if (isset($def->tags[$tag])) {
				$found[$name] = $def->tags[$tag];
			}
		}
		return $found;
	}



	/**
	 * Creates a list of arguments using autowiring.
	 * @return array
	 */
	public function autowireArguments($class, $method, array $arguments)
	{
		$rc = NClassReflection::from($class);
		if (!$rc->hasMethod($method)) {
			if (!NValidators::isList($arguments)) {
				throw new NServiceCreationException("Unable to pass specified arguments to $class::$method().");
			}
			return $arguments;
		}

		$rm = $rc->getMethod($method);
		if ($rm->isAbstract() || !$rm->isPublic()) {
			throw new NServiceCreationException("$rm is not callable.");
		}
		$this->addDependency($rm->getFileName());
		return NDIHelpers::autowireArguments($rm, $arguments, $this);
	}



	/**
	 * Generates $dependencies, $classes and expands and normalize class names.
	 * @return array
	 */
	public function prepareClassList()
	{
        // complete class-factory pairs; expand classes
		foreach ($this->definitions as $name => $def) {
			if ($def->class) {
				$def->class = $def->class === self::CREATED_SERVICE ? $name : $this->expand($def->class);
				if (!$def->factory) {
					$def->factory = new NDIStatement($def->class);
				}
			} elseif (!$def->factory) {
				throw new NServiceCreationException("Class and factory are missing in service '$name' definition.");
			}
			if ($def->factory && $def->factory->entity === self::CREATED_SERVICE) {
				$def->factory->entity = $name;
			}
		}

		// complete classes
		$this->classes = FALSE;
		foreach ($this->definitions as $name => $def) {
			$this->resolveClass($name);
		}

		//  build auto-wiring list
		$this->classes = array();
		foreach ($this->definitions as $name => $def) {
			if (!$def->class) {
				continue;
			}
			if (!class_exists($def->class) && !interface_exists($def->class)) {
				throw new InvalidStateException("Class $def->class has not been found.");
			}
			$def->class = NClassReflection::from($def->class)->getName();
			if ($def->autowired) {
				foreach (class_parents($def->class) + class_implements($def->class) + array($def->class) as $parent) {
					$this->classes[strtolower($parent)][] = $name;
				}
			}
		}

		foreach ($this->classes as $class => $foo) {
			$this->addDependency(NClassReflection::from($class)->getFileName());
		}
	}



	private function resolveClass($name, $recursive = array())
	{
		if (isset($recursive[$name])) {
			throw new InvalidArgumentException('Circular reference detected for services: ' . implode(', ', array_keys($recursive)) . '.');
		}
		$recursive[$name] = TRUE;

		$def = $this->definitions[$name];
		$factory = $this->normalizeEntity($this->expand($def->factory->entity));

		if ($def->class) {
			return $def->class;

		} elseif (is_array($factory)) { // method calling
			if ($service = $this->getServiceName($factory[0])) {
				if (NStrings::contains($service, '\\')) { // @Class
					throw new NServiceCreationException("Unable resolve class name for service '$name'.");
				}
				$factory[0] = $this->resolveClass($service, $recursive);
				if (!$factory[0]) {
					return;
				}
			}
			$factory = callback($factory);
			if (!$factory->isCallable()) {
				throw new InvalidStateException("Factory '$factory' is not callable.");
			}
			try {
				$reflection = $factory->toReflection();
				$def->class = preg_replace('#[|\s].*#', '', $reflection->getAnnotation('return'));
				if ($def->class && !class_exists($def->class) && $def->class[0] !== '\\' && $reflection instanceof ReflectionMethod) {
					}
			} catch (ReflectionException $e) {
			}

		} elseif ($service = $this->getServiceName($factory)) { // alias or factory
			if (NStrings::contains($service, '\\')) { // @Class
				$service = ltrim($service, '\\');
				$def->autowired = FALSE;
				return $def->class = $service;
			}
			if ($this->definitions[$service]->shared) {
				$def->autowired = FALSE;
			}
			return $def->class = $this->resolveClass($service, $recursive);

		} else {
			return $def->class = $factory; // class name
		}
	}



	/**
	 * Adds a file to the list of dependencies.
	 * @return NDIContainerBuilder  provides a fluent interface
	 */
	public function addDependency($file)
	{
		$this->dependencies[$file] = TRUE;
		return $this;
	}



	/**
	 * Returns the list of dependent files.
	 * @return array
	 */
	public function getDependencies()
	{
		unset($this->dependencies[FALSE]);
		return array_keys($this->dependencies);
	}



	/********************* code generator ****************d*g**/



	/**
	 * Generates PHP class.
	 * @return NPhpClassType
	 */
	public function generateClass($parentClass = 'NDIContainer')
	{
		unset($this->definitions[self::THIS_CONTAINER]);
		$this->addDefinition(self::THIS_CONTAINER)->setClass($parentClass);

		$this->prepareClassList();

		$class = new NPhpClassType('Container');
		$class->addExtend($parentClass);
		$class->addMethod('__construct')
			->addBody('parent::__construct(?);', array($this->expand($this->parameters)));

		$classes = $class->addProperty('classes', array());
		foreach ($this->classes as $name => $foo) {
			try {
				$classes->value[$name] = $this->sanitizeName($this->getByType($name));
			} catch (NServiceCreationException $e) {
				$classes->value[$name] = new NPhpLiteral('FALSE, //' . strstr($e->getMessage(), ':'));
			}
		}

		$meta = $class->addProperty('meta', array());
		foreach ($this->definitions as $name => $def) {
			foreach ($this->expand($def->tags) as $tag => $value) {
				$meta->value[$name][NDIContainer::TAGS][$tag] = $value;
			}
		}

		foreach ($this->definitions as $name => $def) {
			try {
				$type = ($tmp=$def->class) ? $tmp : 'object';
				$sanitized = $this->sanitizeName($name);
				if (!NPhpHelpers::isIdentifier($sanitized)) {
					throw new NServiceCreationException('Name contains invalid characters.');
				}
				if ($def->shared && $name === $sanitized) {
					$class->addDocument("@property $type \$$name");
				}
				$method = $class->addMethod(($def->shared ? 'createService' : 'create') . ucfirst($sanitized))
					->addDocument("@return $type")
					->setVisibility($def->shared || $def->internal ? 'protected' : 'public')
					->setBody($name === self::THIS_CONTAINER ? 'return $this;' : $this->generateService($name));

				foreach ($this->expand($def->parameters) as $k => $v) {
					$tmp = explode(' ', is_int($k) ? $v : $k);
					$param = is_int($k) ? $method->addParameter(end($tmp)) : $method->addParameter(end($tmp), $v);
					if (isset($tmp[1])) {
						$param->setTypeHint($tmp[0]);
					}
				}
			} catch (Exception $e) {
				throw new NServiceCreationException("Service '$name': " . $e->getMessage());
			}
		}

		return $class;
	}



	/**
	 * Generates body of service method.
	 * @return string
	 */
	private function generateService($name)
	{
		$def = $this->definitions[$name];
		$parameters = $this->parameters;
		foreach ($this->expand($def->parameters) as $k => $v) {
			$v = explode(' ', is_int($k) ? $v : $k);
			$parameters[end($v)] = new NPhpLiteral('$' . end($v));
		}

		$code = '$service = ' . $this->formatStatement(NDIHelpers::expand($def->factory, $parameters, TRUE)) . ";\n";

		if ($def->class && $def->class !== $def->factory->entity) {
			$code .= NPhpHelpers::formatArgs("if (!\$service instanceof $def->class) {\n"
				. "\tthrow new UnexpectedValueException(?);\n}\n",
				array("Unable to create service '$name', value returned by factory is not $def->class type.")
			);
		}

		foreach ((array) $def->setup as $setup) {
			$setup = NDIHelpers::expand($setup, $parameters, TRUE);
			if (is_string($setup->entity) && strpbrk($setup->entity, ':@?') === FALSE) { // auto-prepend @self
				$setup->entity = array("@$name", $setup->entity);
			}
			$code .= $this->formatStatement($setup, $name) . ";\n";
		}

		return $code .= 'return $service;';
	}



	/**
	 * Formats PHP code for class instantiating, function calling or property setting in PHP.
	 * @return string
	 * @internal
	 */
	public function formatStatement(NDIStatement $statement, $self = NULL)
	{
		$entity = $this->normalizeEntity($statement->entity);
		$arguments = (array) $statement->arguments;

		if (is_string($entity) && NStrings::contains($entity, '?')) { // PHP literal
			return $this->formatPhp($entity, $arguments, $self);

		} elseif ($service = $this->getServiceName($entity)) { // factory calling or service retrieving
			if ($this->definitions[$service]->shared) {
				if ($arguments) {
				throw new NServiceCreationException("Unable to call service '$entity'.");
			}
				return $this->formatPhp('$this->?', array($this->sanitizeName($service)));
			}
			$params = array();
			foreach ($this->definitions[$service]->parameters as $k => $v) {
				$params[] = preg_replace('#\w+$#', '\$$0', (is_int($k) ? $v : $k)) . (is_int($k) ? '' : ' = ' . NPhpHelpers::dump($v));
			}
			$rm = new ReflectionFunction(create_function(implode(', ', $params), ''));
			$arguments = NDIHelpers::autowireArguments($rm, $arguments, $this);
			return $this->formatPhp('$this->?(?*)', array('create' . ucfirst($service), $arguments), $self);

		} elseif ($entity === 'not') { // operator
			return $this->formatPhp('!?', array($arguments[0]));

		} elseif (is_string($entity)) { // class name
		    if ($constructor = NClassReflection::from($entity)->getConstructor()) {
				$this->addDependency($constructor->getFileName());
				$arguments = NDIHelpers::autowireArguments($constructor, $arguments, $this);
			} elseif ($arguments) {
				throw new NServiceCreationException("Unable to pass arguments, class $entity has no constructor.");
			}
			return $this->formatPhp("new $entity" . ($arguments ? '(?*)' : ''), array($arguments));

		} elseif (!NValidators::isList($entity) || count($entity) !== 2) {
			throw new InvalidStateException("Expected class, method or property, " . NPhpHelpers::dump($entity) . " given.");

		} elseif ($entity[0] === '') { // globalFunc
			return $this->formatPhp("$entity[1](?*)", array($arguments), $self);

		} elseif (NStrings::contains($entity[1], '$')) { // property setter
			if ($this->getServiceName($entity[0], $self)) {
				return $this->formatPhp('?->? = ?', array($entity[0], substr($entity[1], 1), $statement->arguments), $self);
			} else {
				return $this->formatPhp($entity[0] . '::$? = ?', array(substr($entity[1], 1), $statement->arguments), $self);
			}

		} elseif ($service = $this->getServiceName($entity[0], $self)) { // service method
			if ($this->definitions[$service]->class) {
				$arguments = $this->autowireArguments($this->definitions[$service]->class, $entity[1], $arguments);
			}
			return $this->formatPhp('?->?(?*)', array($entity[0], $entity[1], $arguments), $self);

		} else { // static method
			$arguments = $this->autowireArguments($entity[0], $entity[1], $arguments);
			return $this->formatPhp("$entity[0]::$entity[1](?*)", array($arguments), $self);
		}
	}



	/**
	 * Formats PHP statement.
	 * @return string
	 */
	private function formatPhp($statement, $args, $self = NULL)
	{
		$that = $this;
		array_walk_recursive($args, create_function('&$val', 'extract(NCFix::$vars['.NCFix::uses(array('self'=>$self,'that'=> $that)).'], EXTR_REFS);
			list($val) = $that->normalizeEntity(array($val));

			if ($val instanceof NDIStatement) {
				$val = new NPhpLiteral($that->formatStatement($val, $self));

			} elseif ($val === \'@\' . NDIContainerBuilder::THIS_CONTAINER) {
				$val = new NPhpLiteral(\'$this\');

			} elseif ($service = $that->getServiceName($val, $self)) {
				$val = $service === $self ? \'$service\' : $that->formatStatement(new NDIStatement($val));
				$val = new NPhpLiteral($val, $self);
				}
		'));
		return NPhpHelpers::formatArgs($statement, $args);
	}



	/**
	 * Expands %placeholders% in strings (recursive).
	 * @param  mixed
	 * @return mixed
	 */
	public function expand($value)
	{
		return NDIHelpers::expand($value, $this->parameters, TRUE);
	}



	private static function sanitizeName($name)
	{
		return strtr($name, '\\', '__');
	}



	/** @internal */
	public function normalizeEntity($entity)
	{
		if (is_string($entity) && NStrings::contains($entity, '::') && !NStrings::contains($entity, '?')) { // NClass::method -> [Class, method]
			$entity = explode('::', $entity);
		}

		if (is_array($entity) && $entity[0] instanceof NDIServiceDefinition) { // [ServiceDefinition, ...] -> [@serviceName, ...]
			$tmp = array_keys($this->definitions, $entity[0], TRUE);
			$entity[0] = "@$tmp[0]";

		} elseif ($entity instanceof NDIServiceDefinition) { // ServiceDefinition -> @serviceName
			$tmp = array_keys($this->definitions, $entity, TRUE);
			$entity = "@$tmp[0]";

		} elseif (is_array($entity) && $entity[0] === $this) { // [$this, ...] -> [@container, ...]
			$entity[0] = '@' . NDIContainerBuilder::THIS_CONTAINER;
		}
		return $entity; // Class, @service, [Class, member], [@service, member], [, globalFunc]
	}



	/**
	 * Converts @service or @Class -> service name and checks its existence.
	 * @param  mixed
	 * @return string  of FALSE, if argument is not service name
	 */
	public function getServiceName($arg, $self = NULL)
	{
		if (!is_string($arg) || !preg_match('#^@[\w\\\\]+$#', $arg)) {
			return FALSE;
		}
		$service = substr($arg, 1);
		if ($service === self::CREATED_SERVICE) {
			$service = $self;
		}
		if (NStrings::contains($service, '\\')) {
			if ($this->classes === FALSE) { // may be disabled by prepareClassList
				return $service;
			}
			$res = $this->getByType($service);
			if (!$res) {
				throw new NServiceCreationException("Reference to missing service of type $service.");
			}
			return $res;
		}
		if (!isset($this->definitions[$service])) {
			throw new NServiceCreationException("Reference to missing service '$service'.");
		}
		return $service;
	}

}
