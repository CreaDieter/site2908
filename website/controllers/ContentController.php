<?php

/**
 * Class ContentController
 */
class ContentController extends Website_Controller_Action
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
	 *    Build the homepage
	 */
	public function homepageAction()
	{
		if ($this->config->news_document) {
			// get the news page
			$newsService = new Website_Service_News();
			$this->view->news = $newsService->getLastNewsItems(3);
		}
	}

	/**
	 *    Build the default content page
	 */
	public function defaultAction()
	{

	}

	public function faqAction()
	{

	}

	public function imageGalleryAction()
	{
		$this->view->headScript()->appendFile('/js/blueimp-lightbox/js/jquery.blueimp-gallery.min.js');
		$this->view->headLink()->appendStylesheet('/js/blueimp-lightbox/css/blueimp-gallery.min.css');
	}

	public function youtubeGalleryAction()
	{

	}
}
