<?php
class Website_Navigation_Main_Helper extends Zend_View_Helper_Abstract
{
    public static $_controller;

    public static function getController()
    {
        if (!self::$_controller) {
            self::$_controller = new Pimcore_View_Helper_PimcoreNavigation_Controller();
        }

        return self::$_controller;
    }

    public function pimcoreNavigation()
    {
        return self::getController();
    }

}