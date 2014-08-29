<?php

/**
 * Class DefaultController
 */
class DefaultController extends Website_Controller_Action
{

	/**
	 * Detect the language to use for the site
	 *
	 * @throws Exception
	 */
	public function languageDetectionAction()
	{
		// Get the browser language
		$locale = new Zend_Locale();
		$browserLanguage = $locale->getLanguage();

		$languages = (array)Pimcore_Tool::getValidLanguages();

		// Check if the browser language is a valid frontend language
		if (in_array($browserLanguage, $languages)) {
			$language = $browserLanguage;
		} else {
			// If it is not, take the first frontend language as default
			$language = reset($languages);
		}

		// Get the folder of the current language (in the current site)
		$currentSitePath = $this->document->getRealFullPath();
		$languageDocument = Document::getByPath($currentSitePath .  '/' . $language);
		if ($languageDocument) {
			$document = $this->findFirstDocumentByDocumentId($languageDocument->getId());
			if ($document) {
				$this->redirect($document->getPath() . $document->getKey());
			} else {
				throw new Exception('No document found in your browser language');
			}
		} else {
			throw new Exception('No language folder found that matches your browser language');
		}
	}

	/**
	 * Find the first active document for a given folder
	 *
	 * @param $folder_id
	 * @return mixed
	 */
	protected function findFirstDocumentByDocumentId($folder_id)
	{
		$list = new Document_List();
		$list->setCondition("parentId = ?", (int)$folder_id);
		$list->setOrderKey("index");
		$list->setOrder("asc");
		$list->setLimit(1);
		$childsList = $list->load();

		return reset($childsList);
	}

	public function defaultAction() {
		$this->view->sebasicPluginInstalled = Pimcore_API_Plugin_Broker::getInstance()->hasPlugin('SEBasic_Plugin');
	}

	public function goToFirstChildAction()
	{
		if ($this->document->hasChilds()) {
			$children = $this->document->getChilds();
			$firstChild = reset($children);
			$this->redirect($firstChild->getFullPath());
		} else {
			throw new Exception('No children found');
		}
	}
}
