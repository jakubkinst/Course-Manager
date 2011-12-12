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
$application->catchExceptions = TRUE;

/**
 * Mailer Configuration
 * SMTP host, username, password settings
 */
Environment::setVariable('mailer', new SmtpMailer(array(
	    'host' => 'smtp.gmail.com',
	    'username' => 'cm@kinst.cz',
	    'password' => 'hj',
	    'secure' => 'ssl',
	)));


// Setup router
$router = $application->getRouter();
$router[] = new Route('index.php', 'Courselist:homepage', Route::ONE_WAY);

$router[] = new Route('[<lang [a-z]{2}>]', 'Courselist:homepage');
$router[] = new Route('<cid [0-9]+>', array(
	    'presenter' => 'Course',
	    'action' => 'Homepage',
	    'cid' => array(
		Route::VALUE => null,
		Route::FILTER_IN => 'inFunction',
		Route::FILTER_OUT => 'outFunction'
	    )
	));

/**
 * Converts url string to id
 * @param string $name
 * @return int 
 */
function inFunction($name) {
    return substr($name, 0, strpos($name, '-'));
}

/**
 * Converts id to string
 * @param int $id
 * @return string 
 */
function outFunction($id) {
    $course = CourseModel::getCourse($id);
    return $id . '-' . String::webalize($course['name']);
}

$router[] = new Route('[<lang [a-z]{2}>/]<presenter>/<action>[/<cid [0-9]+>]', array(
	    'action' => 'Homepage'
	));


// Run the application!
$application->run();
