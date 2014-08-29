<?php

/**
 * Class ContactController
 */
class SearchController extends Website_Controller_Action
{

	const ITEMS_PER_PAGE = 10;

	/**
	 * This is called for all actions in this controller
	 */
	public function init()
	{
		parent::init();
		$this->enableLayout();
		$this->setLayoutForSite();
	}


	public function searchAction()
	{
		$session = new Zend_Session_Namespace('search_1');
		$query = $this->getRequest()->getParam('zoek');

		$siteId = null;
		if (Site::isSiteRequest()) {
			$siteId = Site::getCurrentSite()->getId();
		}

		if (!$query && isset($session->results) && !is_null($session->results)) {
			$this->view->results = $session->results;
			$this->view->query = $session->query;

			// set paginator
			$paginator = Zend_Paginator::factory($this->view->results);
			$paginator->setCurrentPageNumber($this->_getParam('page', 1));
			$paginator->setItemCountPerPage(self::ITEMS_PER_PAGE);
			$this->view->paginator = $paginator;

//			$session->unsetAll();
		} else {
			// check if form was submitted and we are in a subsite
			if (($query = $this->getRequest()->getParam('zoek'))) {
				if (trim($query) == '') {
					$this->view->noSearch = true;
				} else {
					$service = new Website_Service_Search();
					$results = $service->getLuceneSearchResultByKeyword($query, $siteId, $this->language);
					$this->view->results = $this->buildSearchContent($results);
					$session->results = $this->buildSearchContent($results);
					$session->query = $query;
					$this->view->query = $session->query;

					// set paginator
					$paginator = Zend_Paginator::factory($this->view->results);
					$paginator->setCurrentPageNumber($this->_getParam('page', 1));
					$paginator->setItemCountPerPage(self::ITEMS_PER_PAGE);
					$this->view->paginator = $paginator;


					$this->redirect($this->view->documentUrl($this->config->search_document));

				}
			} else {
				$this->view->noSearch = true;
			}
		}
	}

	/**
	 * Format the search object so you don't have to put crazy logics in the view
	 *
	 * @param $results
	 * @return array
	 */
	private function buildSearchContent($results)
	{
		$formattedResult = array();
		/** @var Zend_Search_Lucene_Search_QueryHit $result */
		foreach ($results as $result) {
			$formattedEntry = array();
			// the field names of the queryhit
			$names = $result->getDocument()->getFieldNames();

			// determine the title
			if (in_array('title', $names)) {
				$formattedEntry['title'] = $result->title;
			} // regular object
			elseif (in_array(
				'document_title',
				$names
			)
			) {
				$formattedEntry['title'] = $result->document_title;
			} // document page
			elseif (in_array('name', $names)) {
				$formattedEntry['title'] = $result->name;
			} // salespoint

			// prefix for the title? (if object class)
			if (in_array('className', $names)) {
				$formattedEntry['title'] = $this->view->translate(
						$result->className
					) . ': ' . $formattedEntry['title'];
			}

			// set the content title
			if (in_array('firstWysiwig', $names)) {
				$formattedEntry['content'] = strip_tags($result->firstWysiwig);
			} elseif (in_array('content_text', $names)) {
				$formattedEntry['content'] = $result->content_text;
			} elseif (in_array('content', $names)) {
				$formattedEntry['content'] = $result->content;
			} elseif (in_array('address', $names)) {
				$formattedEntry['content'] = nl2br($result->address);
			}

			// get the url
			if ((in_array('document_title', $names))) {
				// document
				$document = Document_Page::getById($result->document_id);
				if ($document) {
					$formattedEntry['url'] = $document->getFullPath();
				}
			} elseif (in_array('className', $names)) {
				switch ($result->className) {
					case 'event':
						$formattedEntry['url'] = $this->view->url(
							array('language' => $this->language, 'id' => $result->id, 'key' => $result->key),
							'event_detail',
							true
						);
						break;
					case 'news' :
						$formattedEntry['url'] = $this->view->url(
							array('language' => $this->language, 'id' => $result->id, 'key' => $result->key),
							'news_detail',
							true
						);
						break;
					case 'vacancy' :
						$formattedEntry['url'] = $this->view->url(
							array('language' => $this->language, 'id' => $result->id, 'key' => $result->key),
							'vacancy_detail',
							true
						);
						break;
					default:
						break;
				}
			}

			$formattedResult[] = $formattedEntry;
		}
		return $formattedResult;
	}

}