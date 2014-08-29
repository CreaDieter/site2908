<?php 

class Website_Helper_Url extends Pimcore_View_Helper_Url {
	
	/**
	 * Studio Emma check on language for custom route name
	 * @see Pimcore_View_Helper_Url::url()
	 */
	public function url($urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
    	
    	if ($urlOptions instanceof Website_Model_Abstract) {
    		$name = $urlOptions->getRouteName();
    		$options = $urlOptions->getRouteParams();
			$options["language"] = CURRENT_LANGUAGE;
    		return parent::url($options, $name, $reset, $encode);
    	} else {
	    	if (empty($urlOptions) && empty($name) && empty($language)) {
	    		return $_SERVER['REQUEST_URI'];
	    	}
			/*$languages = (array) Pimcore_Tool::getValidLanguages();
			
	    	if ($language && in_array($language, $languages)) {
				$lang = $language;
			} else {
				$lang = CURRENT_LANGUAGE;	
			}*/
			
			/*if (Staticroute::getByName($lang . '_' . $name)) {
				$newName = $lang . '_' . $name;
			} elseif (Staticroute::getByName($name)) {
				$newName = $name;
			}
			
			if (is_numeric($urlOptions) || is_string($urlOptions)) {
				$urlOptions = array($urlOptions);
			}
			
			if (CURRENT_LANGUAGE) {
				$langPrefix = '/' . CURRENT_LANGUAGE;
			}*/
			if (!isset($urlOptions["language"])) {
				$urlOptions["language"] = CURRENT_LANGUAGE;
			}
			
	        return parent::url($urlOptions, $name, $reset, $encode);
    	}
    }    
}