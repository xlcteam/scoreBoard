<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Config\Extensions
 */



/**
 * Core Nette Framework services.
 *
 * @author     David Grudl
 * @package Nette\Config\Extensions
 */
class NNetteExtension extends NConfigCompilerExtension
{

	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig();

		// cache
		$container->addDefinition('cacheJournal')
			->setClass('NFileJournal', array('%tempDir%'));

		$container->addDefinition('cacheStorage')
			->setClass('NFileStorage', array('%tempDir%/cache'));

		$container->addDefinition('templateCacheStorage')
			->setClass('NPhpFileStorage', array('%tempDir%/cache'))
			->setAutowired(FALSE);

		// http
		$container->addDefinition('httpRequestFactory')
			->setClass('NHttpRequestFactory')
			->addSetup('setEncoding', array('UTF-8'))
			->setInternal(TRUE)
			->setShared(FALSE);

		$container->addDefinition('httpRequest')
			->setClass('NHttpRequest')
			->setFactory('@\NHttpRequestFactory::createHttpRequest');

		$container->addDefinition('httpResponse')
			->setClass('NHttpResponse');

		$container->addDefinition('httpContext')
			->setClass('NHttpContext');

		$session = $container->addDefinition('session')
			->setClass('NSession');

		if (isset($config['session']['expiration'])) {
			$session->addSetup('setExpiration', array($config['session']['expiration']));
			unset($config['session']['expiration']);
		}
		if (!empty($config['session'])) {
			NValidators::assertField($config, 'session', 'array');
			$session->addSetup('setOptions', array($config['session']));
		}

		$container->addDefinition('user')
			->setClass('NUser');

		// application
		$application = $container->addDefinition('application')
			->setClass('NApplication')
			->addSetup('$catchExceptions', '%productionMode%');

		if (empty($config['productionMode'])) {
			$application->addSetup('NRoutingDebugger::initialize'); // enable routing debugger
		}

		$container->addDefinition('router')
			->setClass('NRouteList');

		$container->addDefinition('presenterFactory')
			->setClass('NPresenterFactory', array(
				isset($container->parameters['appDir']) ? $container->parameters['appDir'] : NULL
			));

		// mailer
		if (empty($config['mailer']['smtp'])) {
			$container->addDefinition('mailer')
				->setClass('NSendmailMailer');
		} else {
			NValidators::assertField($config, 'mailer', 'array');
			$container->addDefinition('mailer')
				->setClass('NSmtpMailer', array($config['mailer']));
		}
	}



	public function afterCompile(NPhpClassType $class)
	{
		$initialize = $class->methods['initialize'];
		$container = $this->getContainerBuilder();

		if (isset($container->parameters['tempDir'])) {
			$initialize->addBody($this->checkTempDir($container->expand('%tempDir%/cache')));
		}
		foreach ($container->findByTag('run') as $name => $foo) {
			$initialize->addBody('$this->getService(?);', array($name));
		}
	}



	private function checkTempDir($dir)
	{
		// checks whether directory is writable
		$uniq = uniqid('_', TRUE);
		if (!@mkdir("$dir/$uniq", 0777)) { // @ - is escalated to exception
			throw new InvalidStateException("Unable to write to directory '$dir'. Make this directory writable.");
		}

		// tests subdirectory mode
		$useDirs = @file_put_contents("$dir/$uniq/_", '') !== FALSE; // @ - error is expected
		@unlink("$dir/$uniq/_");
		@rmdir("$dir/$uniq"); // @ - directory may not already exist

		return 'NFileStorage::$useDirectories = ' . ($useDirs ? 'TRUE' : 'FALSE') . ";\n";
	}

}
