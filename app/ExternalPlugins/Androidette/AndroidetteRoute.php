<?php

/**
 * Description of AndroidetteRoute
 *
 * @author Jakub Kinst
 */
class AndroidetteRoute extends Route{
	public function __construct($metadata = array()) {
		parent::__construct('<presenter>/<action>/?mobile=1', $metadata);
	}
}

?>
