<?php

/**
 * Class Website_Controller_Action
 */
class Website_Controller_Action extends Pimcore_Controller_Action_Frontend
{

	/**
	 * @var
	 */
	protected $language;
	/**
	 * @var null
	 */
	private $_flashMessenger = null;

	/**
	 *
	 */
	public function init()
	{

		parent::init();

		// Try and fetch the current locale
		try {
			if (!$this->document) throw new Exception('no document set'); // needed for use of renderlets

			$locale = new Zend_Locale($this->document->getProperty("language"));
		} catch (Exception $e) {
			$defaultLanguage = reset(Pimcore_Tool::getValidLanguages());
			$locale = new Zend_Locale($defaultLanguage);
		}
		Zend_Registry::set("Zend_Locale", $locale);

		// Set current language based on the locale
		$this->view->language = $locale->getLanguage();
		$this->language = $locale->getLanguage();
		define("CURRENT_LANGUAGE", $this->language);

		// Add view helpers
		$this->view->addHelperPath(PIMCORE_PLUGINS_PATH . '/SEBasic/views/helpers/', 'SEBasic_Helper');
		$this->view->addHelperPath(PIMCORE_WEBSITE_PATH . '/views/helpers/', 'Website_Helper');

		if ($this->document) {
			$this->setSiteInBackoffice();

			// Set document title
			$this->setDocumentTitle();

			// Build navigation
			$this->buildNavigation();

			// Set document meta data (SEO)
			$this->setDocumentMeta();

			// Load javascript
			$this->loadJsForAction();
		}

		// init website config
		$config = Website_Config::getWebsiteConfig();
		$this->config = $config;
		$this->view->config = $config;

		// check for debug mode
		$sysConf = Pimcore_Config::getSystemConfig();
		$this->debug = $this->view->debug = (bool) $sysConf->general->debug;

		// Set flash messager
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->flashMessages = $this->_flashMessenger->getMessages();
	}

	/**
	 * Set the subsite when in backoffice
	 */
	protected function setSiteInBackoffice()
	{
		if (Zend_Registry::isRegistered("pimcore_site")) {
			return;
		}
		// get the subsites
		$list = new Site_List();
		$sites = $list->load();
		$sitesArr = array();
		foreach ($sites as $site) {
			$sitesArr[$site->rootId] = $site;
		}

		$document = $this->document;
		while ($document->getParentId() > 0) {
			$document = $document->getParent();
			if (array_key_exists($document->id, $sitesArr)) {
				$site = $sitesArr[$document->id];
				Zend_Registry::set('pimcore_site', $site);
				$layoutName = 'layout_' . $site->getRootDocument()->getKey();
				$this->setLayout($layoutName);
				break;
			}
		}
	}

	/**
	 * Sets the document title + site title in the header <title> tags
	 */
	public function setDocumentTitle()
	{
		// Initialise the title
		$title = array();

		// If document is a page or a Hardlink
		if ($this->document instanceof Document_Page || $this->document instanceof Document_Hardlink) {
			// And, if title is set
			if ($this->document->getTitle()) {
				// Add page title
				$title[] = $this->document->getTitle();
			} elseif ($this->document->hasProperty("navigation_name")) {
				// Add page navigation name as title
				$title[] = $this->document->getProperty("navigation_name");
			}
		}


		// Set site title
		$title[] = $this->config->site_title;

		// Set actual title
		$this->view->headTitle(implode(' | ', $title), Zend_View_Helper_Placeholder_Container_Abstract::SET);
	}

	/**
	 * Builds the navigation trees
	 */
	public function buildNavigation()
	{
		if (Site::isSiteRequest()) {
			$site = Site::getCurrentSite();
			$rootDoc = $site->getRootDocument();
			$getRootDocumentPath = $rootDoc->getRealFullPath();
			$language = $this->language;

			$navRootPath = $getRootDocumentPath . '/' . $language;
			$navStartNode = Document::getByPath($navRootPath);
		} else {
			$navStartNode = Document::getByPath('/' . $this->language);
		}

		// Build navigation
		$navigation = $this->view->pimcoreNavigation()->getNavigation($this->document, $navStartNode);
		// Parse to view
		$this->view->navigation($navigation);
	}

	/**
	 * Sets the document Meta data (SEO)
	 */
	public function setDocumentMeta()
	{
		// If document is set, and the property is filled in, set it in the head
		if ($this->document instanceof Document_Page) {
			if ($this->document->getDescription()) {
				$this->view->headMeta()->appendName(
					'description',
					$this->document->getDescription()
				);
			}
			if ($this->document->getKeywords()) {
				$this->view->headMeta()->appendName(
					'keywords',
					$this->document->getKeywords()
				);
			}
		}
	}

	/**
	 *
	 */
	public function loadJsForAction()
	{
		$controllerName = $this->getRequest()->getParam('controller');

		$jsUrl = "/js/storme/classes/" . $controllerName . ".js";
		$jsFilePath = PIMCORE_WEBSITE_PATH . "/static" . $jsUrl;


		if (file_exists($jsFilePath)) {
			$this->view->headScript()->appendFile($jsUrl, "text/javascript");
		}
	}


	/**
	 * @param $msg
	 */
	public function addFlashMessage($msg)
	{
		$this->_flashMessenger->addMessage($msg);
	}

	/**
	 * Defines the layout for the current subsite
	 */
	protected function setLayoutForSite()
	{
		if (Site::isSiteRequest()) {
			$siteKey = Site::getCurrentSite()->getRootDocument()->getKey();
			$layoutName = 'layout_' . $siteKey;
		} else {
			$layoutName = 'layout';
		}

		$this->setLayout($layoutName);
	}
}
