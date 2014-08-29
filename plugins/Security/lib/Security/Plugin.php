<?php


class Security_Plugin  extends Pimcore_API_Plugin_Abstract implements Pimcore_API_Plugin_Interface {

    public function init() {
        // register your events here
    }

    public function handleDocument ($event) {
        // do something
        $document = $event->getTarget();
    }

	public static function install (){
        // implement your own logic here
        return true;
	}
	
	public static function uninstall (){
        // implement your own logic here
        return true;
	}

	public static function isInstalled () {
        // implement your own logic here
        return true;
	}


}
