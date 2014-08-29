<?php
class Website_Helper_DocumentLink extends Zend_View_Helper_Abstract
{
	/**
	 * This function will create a <a href> element with url and title of a given page
	 *
	 * @param $data Document or Document ID
	 * @param null $language
	 * @return string
	 */
	public function documentLink($data, $language=null) {
		if (is_null($language)) {
			$language = CURRENT_LANGUAGE;
		}

		if ($data instanceof Document) {
			$id = (int)$data->getId();
		} elseif(is_numeric($data)) {
			$id = (int)$data;
		}

		try {
			$otherLangId = SEInternationalisation_Document::getDocumentIdInOtherLanguage($id, $language);
		}catch (Exception $e) {
			return null;
		}

		if ($otherLangId) {
			$document = Document::getById($otherLangId);

			$link = "<a href='".$document->getFullPath() . "'";
			$link .= " title='" . $document->getProperty('navigation_title') . "'>";
			$link .= $document->getProperty('navigation_name');
			$link .= "</a>";

			return $link;
		}
		return null;
	}
}