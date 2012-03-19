<?php

/**
 * Description of AndroidetteRoute
 *
 * @author Jakub Kinst
 */
class AndroidetteRoute extends \Nette\Application\Routers\Route{
	public function __construct($metadata = array()) {
		parent::__construct('<presenter>/<action>/?mobile=1', $metadata ,\Nette\Application\Routers\Route::ONE_WAY);
	}
}

?>
