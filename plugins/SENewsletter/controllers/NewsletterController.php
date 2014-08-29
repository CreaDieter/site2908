<?php

/**
 * Class ContactController
 */
class SENewsletter_NewsletterController extends Website_Controller_Action {

	/**
	 * This is called for all actions in this controller
	 */
	public function init() {
		parent::init();
		$this->view->baseUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
	}

	/**
	 * Display the newsletter
	 *
	 * @throws Exception
	 */
	public function defaultAction() {
		// prevent using this when plugin isn't enabled in backoffice
		if (!SENewsletter_Plugin::isInstalled()) {
			throw new Exception('Newsletter module is not enabled');
		}
		$this->enableLayout();
		$this->setLayoutForSite();

		$this->setLayout('newsletter');
	}

	/**
	 * Get the url of an object
	 * this is the default format, using the id and the key
	 *
	 * @param $object
	 * @param $staticroute
	 * @return string
	 */
	private function getUrl($object, $staticroute) {
		try {
			$url = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->view->url(
					array('language' => $this->language, 'id' => $object->getId(), 'key' => $object->getKey()),
					$staticroute,
					true
				);
		} catch (Exception $e) {
			$url = '';
		}
		return $url;
	}

	/**
	 * Set the website language if it is given as a param
	 * we need this
	 */
	protected function setLanguage() {
		if ($this->_getParam('language') && Pimcore_Tool::isValidLanguage($this->_getParam('language'))) {
			$locale = new Zend_Locale($this->_getParam('language'));

			Zend_Registry::set("Zend_Locale", $locale);

			// Set current language based on the locale
			$this->view->language = $locale->getLanguage();
			$this->language = $locale->getLanguage();
		}
	}

	/**
	 * Get the object of that was send with the renderlet and assign it to the view
	 *
	 * @param $object
	 * @param $staticRoute
	 */
	protected function assignObjectToView($object,$staticRoute) {
		if ($this->_getParam('id') && $this->_getParam("type") == 'object' && $obj = $object::getById($this->_getParam('id'))) {
			$this->view->url = $this->getUrl($obj, $staticRoute);
			$this->view->hasImage = (bool)method_exists($obj,'getImage');
			$this->view->element = $obj;
		} elseif ($this->getParam('editmode') == true) {
			die("Not a " . strtolower(str_replace('_',' ',$object)));
		}
	}

	/**
	 * Get the vacancy detail
	 */
	public function vacancyDetailAction() {
		$this->assignObjectToView('Object_Vacancy','vacancy_detail');
	}

	/**
	 * Get the news detail
	 */
	public function newsDetailAction() {
		$this->assignObjectToView('Object_news','news_detail');
	}

	/**
	 * Get the event detail
	 */
	public function eventDetailAction() {
		$this->assignObjectToView('Object_event','event_detail');
	}

	/**
	 * Get the vacancy teaser
	 */
	public function vacancyTeaserAction() {
		$this->assignObjectToView('Object_Vacancy','vacancy_detail');
	}

	/**
	 * Get the teaser detail
	 */
	public function newsTeaserAction() {
		$this->assignObjectToView('Object_news','news_detail');
	}

	/**
	 * Get the event teaser
	 */
	public function eventTeaserAction() {
		$this->assignObjectToView('Object_event','event_detail');
	}

	/**
	 * Get the vacancy detail
	 */
	public function vacancyTitleAction() {
		$this->assignObjectToView('Object_Vacancy','vacancy_detail');
	}
	/**
	 * Get the news title
	 */
	public function newsTitleAction() {
		$this->assignObjectToView('Object_news','news_detail');
	}

	/**
	 * Get the event title
	 */
	public function eventTitleAction() {
		$this->assignObjectToView('Object_event','event_detail');
	}
}