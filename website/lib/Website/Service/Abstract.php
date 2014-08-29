<?php

class Website_Service_Abstract {
    private static $instance = null;

	public function getAbsoluteUrl() {
		return "http://".$_SERVER['HTTP_HOST'];		// to do: check indien http of https ...
	}

}