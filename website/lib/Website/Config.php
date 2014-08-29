<?php

class Website_Config
{
	/**
	 * @static
	 * @return mixed|Zend_Config
	 */
	public static function getWebsiteConfig()
	{

		$cacheKey = "website_config";

		if (Site::isSiteRequest()) {
			$siteId = Site::getCurrentSite()->getId();
			$cacheKey = $cacheKey . "_site_" . $siteId;
		} else {
			$siteId = 0;
		}

		$currentSiteId = $siteId;

		if (!$config = Pimcore_Model_Cache::load($cacheKey)) {
			$settingsArray = array();
			$cacheTags = array("website_config", "system", "config", "output");

			$list = new WebsiteSetting_List();
			$list = $list->load();

			foreach ($list as $item) {
				$key = $item->getName();
				$siteId = $item->getSiteId();

				if (!$siteId && $siteId > 0) {
					continue;
				}

				if ($siteId && $siteId > 0 && $siteId != $siteId) {
					continue;
				}

				$s = null;

				if ($siteId && $siteId != $currentSiteId) {
					continue;
				}

				switch ($item->getType()) {
					case "document":
					case "asset":
					case "object":
						$s = Element_Service::getElementById($item->getType(), $item->getData());
						break;
					case "bool":
						$s = (bool)$item->getData();
						break;
					case "text":
						$s = (string)$item->getData();
						break;

				}

				if ($s instanceof Element_Interface) {
					$cacheTags = $s->getCacheTags($cacheTags);
				}

				if (isset($s)) {
					$settingsArray[$key] = $s;
				}
			}

			$config = new Zend_Config($settingsArray, true);

			Pimcore_Model_Cache::save($config, $cacheKey, $cacheTags, null, 998);
		}

		Pimcore_Config::setWebsiteConfig($config);

		return $config;
	}
}