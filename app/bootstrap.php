<?php

/**
 * CourseManager bootstrap file.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 */
// Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require LIBS_DIR . '/Nette/loader.php';
require LIBS_DIR . '/dibi/dibi.php';




// Enable Debug for error visualisation & logging
Debug::$strictMode = TRUE;
Debug::enable();

// Load configuration from config.neon file
Environment::loadConfig();

// Setup DB Connection
dibi::connect(Environment::getConfig('database'));

// Configure application
$application = Environment::getApplication();
$application->errorPresenter = 'Error';
$application->catchExceptions = FALSE;

/**
 * Mailer Configuration
 * SMTP host, username, password settings
 */
Environment::setVariable('mailer', Environment::getConfig('mailer'));


// Setup router
$router = $application->getRouter();
$router[] = new Route('index.php', 'Courselist:homepage', Route::ONE_WAY);
$router[] = new Route('[<lang [a-z]{2}>]', 'Courselist:homepage');
// Androidette Router
$router[] = new AndroidetteRoute('courselist:homepage');
$router[] = new Route('[<lang [a-z]{2}>/]<presenter>/<action>[/<cid [0-9]+>]', array(
	    'action' => 'homepage'
	));


// Run the application!
$application->run();
