<?php

/**
 * Class NewsController
 */
class NewsController extends Website_Controller_Action
{
	/**
	 * This is called for all actions in this controller
	 */
	public function init()
	{
		parent::init();
		$this->enableLayout();
		$this->setLayoutForSite();
	}

	/**
	 *    Generate a list of news objects for this subsite, in this language
	 */
	public function overviewAction()
	{

		$subSiteKey = '*';
		if (Site::isSiteRequest()) {
			$subSiteKey = Site::getCurrentSite()->getRootDocument()->getKey();
		}
		$language = $this->language;

		// Retrieve items from object list
		$newsList = new Object_News_List();
		$newsList->setOrderKey("date");
		$newsList->setOrder("desc");
		$newsList->setLocale($language);
		if (Site::isSiteRequest()) {
			$newsList->setCondition('subsites LIKE ? AND title != ""', '%' . $subSiteKey . '%');
		}

		$paginator = Zend_Paginator::factory($newsList);
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$paginator->setItemCountPerPage(10);
		$this->view->paginator = $paginator;
	}

	/**
	 *    Show the news detail page
	 */
	public function showAction()
	{
		$newsId = $this->getParam('id', null);

		if ($newsId) {
			$newsItem = Object_News::getById($newsId);
			if ($newsItem) {
				$this->view->item = $newsItem;
			} else {
				throw new Exception('No news item found with this ID: ' . $newsId);
			}
		} else {
			throw new Exception('No news item found with this ID: ' . $newsId);
		}
	}
}