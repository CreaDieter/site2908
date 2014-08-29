<?php

class Website_Service_News extends Website_Service_Abstract
{

	/**
	 * Get the overview of all products (sorted by category & sorted by sector)
	 *
	 * @param int $count
	 * @throws Exception
	 * @throws Zend_Exception
	 * @internal param $document
	 * @return array
	 */
	public function getLastNewsItems($count = 3)
	{
		if (is_numeric($count)) {
			$subSiteKey = '*';
			if (Site::isSiteRequest()) {
				$subSiteKey = Site::getCurrentSite()->getRootDocument()->getKey();
			}

			// Retrieve items from object list
			$newsList = new Object_News_List();
			$newsList->setOrderKey("date");
			$newsList->setOrder("desc");
			$newsList->setLocale(Zend_Registry::get('Zend_Locale'));
			if (Site::isSiteRequest()) {
				$newsList->setCondition('subsites LIKE ? AND title != ""', '%' . $subSiteKey . '%');
			}
			$newsList->setLimit($count);

			return $newsList->load();
		}

		return array();
	}
}