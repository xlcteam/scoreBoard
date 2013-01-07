<?php

/**
 * My Application bootstrap file.
 */



// Load Nette Framework
require LIBS_DIR . '/Nette/loader.php';


// Enable Nette Debugger for error visualisation & logging
NDebugger::$logDirectory = dirname(__FILE__) . '/../log';
NDebugger::$strictMode = TRUE;
NDebugger::enable();


// Configure application
$configurator = new NConfigurator;
$configurator->setTempDirectory(dirname(__FILE__) . '/../temp');

// Enable RobotLoader - this will load all classes automatically
$configurator->createRobotLoader()
	->addDirectory(APP_DIR)
	->addDirectory(LIBS_DIR)
	->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(dirname(__FILE__) . '/config/config.neon');
$container = $configurator->createContainer();

// Opens already started session
if ($container->session->exists()) {
	$container->session->start();
}

// Setup router
$router = $container->router;
$router[] = new NRoute('index.php', 'Dashboard:default', NRoute::ONE_WAY);
$router[] = new NRoute('<presenter>/<action>[/<id>]', 'Dashboard:default');


// Configure and run the application!
$application = $container->application;
//$application->catchExceptions = TRUE;
$application->errorPresenter = 'Error';
$application->run();
