<?php 

class Website_Helper_DocumentUrl extends Zend_View_Helper_Abstract {

	/**
	 * 
	 * Finds the path of a document in a (given) language, based on a document_id
	 * @param int $id
	 * @param string $langKey
	 * @param string $language
	 */
	public function documentUrl($data, $language=null) {
		if (is_null($language)) {
			$language = CURRENT_LANGUAGE;
		}

		if (Pimcore_API_Plugin_Broker::getInstance()->hasPlugin("SEInternationalisation_Plugin")) {
			if ($data instanceof Document) {
				$id = (int)$data->getId();
			} else {
				$id = (int)$data;
			}
			$document = Document::getById(SEInternationalisation_Document::getDocumentIdInOtherLanguage($id, $language));
			return $document->getFullPath();
			
		} else {
			if ($data instanceof Document) {
				$document = $data;
			} else {
				$id = (int)$data;
				$document = Document::getById($id);
			}

			return $document->getFullPath();
		}
	}
}