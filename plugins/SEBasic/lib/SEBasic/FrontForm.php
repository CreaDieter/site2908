<?php

class SEBasic_FrontForm extends Zend_Form {

    public static function getByName($name) {

		// Uncomment this to speed up forms
//    	$locale = Zend_Registry::get("Zend_Locale");
//    	$translate = new SEBasic_Translate();
//    	Zend_Registry::set("Zend_Translate", $translate);

		if (is_file(PIMCORE_WEBSITE_PATH . '/config/forms/'.$name.'.xml')) {
			// Get the XML config file for the form
			$formConfig = new Zend_Config_Xml(PIMCORE_WEBSITE_PATH . '/config/forms/'. $name .'.xml');

			// Create the form
			$form = new SEBasic_FrontForm();
			$form->addPrefixPath('Website_Form', 'Website/Form/');

			$form->setConfig($formConfig);
		} else {
			throw new Exception('Could not find the requested xml file!');
		}
		
		return $form;
		
    }
}