<?php

/**
 * My Application bootstrap file.
 */



// Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require LIBS_DIR . '/Nette/loader.php';


// Enable Debug for error visualisation & logging
Debug::$strictMode = TRUE;
Debug::enable();


// Load configuration from config.neon file
Environment::loadConfig();


// Configure application
$application = Environment::getApplication();
$application->errorPresenter = 'Error';
//$application->catchExceptions = TRUE;


// Setup router
{
	$router = $application->getRouter();

	$router[] = new Route('index.php', 'Course:homepage', Route::ONE_WAY);

	$router[] = new Route('<presenter>/<action>[/<id>]', 'Course:homepage');
};


// Run the application!
$application->run();
