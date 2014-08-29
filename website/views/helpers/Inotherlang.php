<?php

/**
 * Class Website_Helper_Inotherlang
 */
class Website_Helper_Inotherlang extends Zend_View_Helper_Abstract
{

	/**
	 * @param $document
	 * @param null $language
	 * @return Document|null
	 */
	public function inotherlang($document, $language = null)
	{
		if (is_null($language)) {
			$language = CURRENT_LANGUAGE;
		}

		if ($document instanceof Document) {
			$id = $document->getId();
		} elseif (is_numeric($document)) {
			$id = $document;
		} else {
			$id = 0;
		}

		try {
			$otherLangId = SEInternationalisation_Document::getDocumentIdInOtherLanguage($id, $language);
		} catch (Exception $e) {
			return null;
		}

		if ($otherLangId) {
			return Document::getById($otherLangId);
		} else {
			return null;
		}

	}
}