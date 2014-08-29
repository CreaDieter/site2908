<?php
class Website_Helper_Home extends Zend_View_Helper_Abstract
{

	public function home($language=null) {
		if (is_null($language)) {
			$language = CURRENT_LANGUAGE;
		}


		// check if this in the cache
		$cacheService = Website_Service_Cache::getInstance();
		$cacheData = $cacheService->load('SEHOMES');

		if (isset($cacheData[$language])) {
			return $cacheData[$language];
		}

		if (Site::isSiteRequest()) {
			$path = '/' . Site::getCurrentSite()->getRootDocument()->getKey() . '/';
		} else {
			$path = '/';
		}
		$folder = Document::getByPath($path . $language);

		if ($folder) {
			if ($folder instanceof Document_Page) {
				$cacheData[$language] = $folder->getFullPath();
				$cacheService->write("SEHOMES",$cacheData,'month','SEHOMES');

		 	    return $folder->getFullPath();
			}else {
				$document = $this->findFirstDocumentByFolderId($folder->getId());
				if ($document) {
					$cacheData[$language] = $document->getFullPath();
					$cacheService->write("SEHOMES",$cacheData,'month','SEHOMES');
					return $document->getFullPath();
				}
			}
		}
		return '/';
	}


	protected function findFirstDocumentByFolderId($folder_id) {
		$list = new Document_List();
		$list->setCondition("parentId = ?", (int)$folder_id);
		$list->setOrderKey("index");
		$list->setOrder("asc");
		$list->setLimit(1);
		$childsList = $list->load();

		return reset($childsList);
	}
}