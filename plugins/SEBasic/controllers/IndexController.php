<?php


/**
 * Class SEBasic_IndexController
 */
class SEBasic_IndexController extends Pimcore_Controller_Action_Admin
{

	public function testAction()
	{
		$oldConfig = Pimcore_Config::getSystemConfig();
		$oldConfig = $oldConfig->toArray();
		$oldConfig['documents']['default_controller'] = 'content';
		echo '<pre>';
		print_r($oldConfig);
		echo '</pre>';
		exit;
	}

	/**
	 *
	 */
	public function getClassListAction()
	{
		$service = new SEBasic_Service_Classes();
		$classes = $service->getClassnames();

		$this->_helper->json(
			array(
				"success" => 'true',
				'classes' => $classes
			),
			false
		);
	}

	/**
	 *
	 */
	public function addClassesAction()
	{
		$result = false;
		$service = new SEBasic_Service_Classes();

		if ($this->_getParam('data', false)) {
			$classes = json_decode($this->_getParam('data'));

			foreach ($classes as $name => $active) {
				if ($active == 1) {
					$result = $service->addClass($name);
				}
			}
		}

		$this->_helper->json(
			array(
				"success" => $result
			),
			false
		);
	}


	/**
	 * @return array
	 */
	protected function getAvailableLanguages()
	{
		return Pimcore_Tool::getValidLanguages();
	}

	/**
	 * @return mixed
	 */
	protected function getFirstAvailableLanguage()
	{
		return reset($this->getAvailableLanguages());
	}

	/**
	 * @return Document
	 */
	protected function getFirstLanguageDocument()
	{
		return Document::getByPath('/' . $this->getFirstAvailableLanguage());
	}

	/**
	 * Add a new item to the website config
	 *
	 * @param $name
	 * @param $type
	 * @param $data
	 */
	protected function addWebsiteSetting($name, $type, $data)
	{
		$setting = new WebsiteSetting();
		$setting->setValues(
			array(
				'name' => $name,
				'type' => $type,
				'data' => $data
			)
		);
		$setting->save();
	}

	/**
	 * Create a new email document
	 *
	 * @param $key
	 * @param $parent
	 * @param string $content
	 * @param string $subject
	 * @throws Exception
	 * @return Document_Email
	 */
	protected function createEmail($key, $parent, $content = '', $subject = '')
	{
		$email = new Document_Email();
		$email->setParent($parent);
		$email->setParentId($parent->getId());
		$email->setKey($key);
		$email->setController("email");
		$email->setAction("default");
		$email->setPath($parent->getFullPath() . '/');
		$email->save();
		$email->setSubject($subject);
		$email->setRawElement(
			'email_content',
			'Wysiwyg',
			nl2br($content)
		);
		$email->save();

		return $email;
	}

	/**
	 * Create a new document page
	 *
	 * @param        $key
	 * @param        $controller
	 * @param        $action
	 * @param        $navigation_name
	 * @param        $parent
	 * @param string $title
	 * @param bool $navigation_exclude
	 * @throws Exception
	 * @return Document_Page
	 */
	protected function createDocument(
		$key,
		$controller,
		$action,
		$navigation_name,
		$parent,
		$title = '',
		$navigation_exclude = false
	) {
		$document = new Document_Page();
		$document->setParent($parent);
		$document->setParentId($parent->getId());
		$document->setKey($key);
		$document->setController($controller);
		$document->setAction($action);
		if ($title != '') {
			$document->setTitle($title);
		}
		if ($navigation_exclude) {
			$document->setProperty("navigation_exclude", 'bool', true, false, false);
		}
		if ($navigation_name != '') {
			$document->setProperty("navigation_name", 'text', $navigation_name, false, false);
		}
		$document->setPath($parent->getFullPath() . '/');
		$document->save();

		return $document;
	}

	/**
	 *
	 */
	public function addLanguageAction()
	{
		$result = false;
		$msg = "";
		if ($this->_getParam('data', false)) {
			$data = json_decode($this->_getParam('data'));
			$languageToAdd = $data->language;

			$service = new SEBasic_Service_Document();
			$result = $service->addLanguage($languageToAdd);
		}

		if (!$result) {
			$msg = "This language could not be added";
		}

		$this->_helper->json(
			array(
				"success" => $result,
				"msg" => $msg
			),
			false
		);
	}

	/**
	 * @throws Exception
	 */
	public function addContactPageAction()
	{
		// Add documents in all languages
		$firstLanguageDocument = $this->getFirstLanguageDocument();

		$document = $this->createDocument('contact', 'contact', 'default', 'Contact', $firstLanguageDocument);

		$folder = $this->createOnderdelenFolder();

		// create the email
		$email = $this->createEmail(
			'contact',
			$folder,
			'Beste,
			%Text(name); heeft u gecontacteerd via de website %Text(website);.
			Dit waren de gegevens:
			Naam:
			%Text(name);
			Bedrijf:
			%Text(company);
			E-mail:
			%Text(email);
			Telefoon:
			%Text(phone);
			Onderwerp:
			%Text(subject);
			Bericht:
			%Text(message);',
			'Bericht via website contactformulier'
		);

		// Add Document to website config
		$this->addWebsiteSetting('email_contact', 'document', $email->getId());

		$this->_helper->json(
			array(
				"success" => true,
			),
			false
		);
	}

	/**
	 * @throws Exception
	 */
	public function addDisclaimerPageAction()
	{
		$firstLanguageDocument = $this->getFirstLanguageDocument();

		// Create document
		$document = $this->createDocument(
			'disclaimer',
			'content',
			'default',
			'',
			$firstLanguageDocument,
			'Disclaimer',
			true
		);

		// Add Document to website config
		$this->addWebsiteSetting('disclaimer_document', 'document', $document->getId());

		$this->_helper->json(
			array(
				"success" => true,
			),
			false
		);
	}

	/**
	 * @throws Exception
	 */
	public function addHomePageAction()
	{
		$firstLanguageDocument = $this->getFirstLanguageDocument();

		// Create document
		$document = $this->createDocument('home', 'content', 'homepage', 'Home', $firstLanguageDocument, 'Home');

		// set some default data
		$document->setRawElement('title', 'input', 'Welkom op deze website');
		$document->setRawElement('subtitle', 'input', 'Deze website is nog in ontwikkeling');
		$document->setRawElement('content_text', 'input', 'De effectieve inhoud van de homepagina komt hier.');
		$document->save();

		$this->_helper->json(
			array(
				"success" => true,
			),
			false
		);
	}


	public function addUserAction()
	{
		$this->disableViewAutoRender();

		$service = new SEBasic_Service_User();
		$result = $service->createBackofficeUser($this->getAllParams());

		if (is_string($result)) {
			die($result);
		}

		$this->_helper->json(
			array(
				"success" => true,
			),
			false
		);
		exit;
	}


	/**
	 * @throws Exception
	 */
	public function addNewsPageAction()
	{
		$firstLanguageDocument = $this->getFirstLanguageDocument();

		$document = $this->createDocument(
			'nieuws',
			'news',
			'overview',
			'Nieuws',
			$firstLanguageDocument,
			'Nieuwsoverzicht'
		);

		// Add class
		$service = new SEBasic_Service_Classes();
		$service->addClass('news');

		// Add Static route
		$route = new Staticroute();
		$route->setValues(
			array(
				'name' => 'news_detail',
				'pattern' => '/\/(.*)\/news\/(.*)\/(.*)/',
				'reverse' => '/%language/news/%id/%key',
				'controller' => 'news',
				'action' => 'show',
				'variables' => 'language,id,key',
				'defaults' => 'nl,1,nokey'
			)
		);
		$route->save();

		// Add Document to website config
		$this->addWebsiteSetting('news_document', 'document', $document->getId());

		// Create object folder
		$folder = $this->createObjectFolder('nieuws');

		// create demo data
		$item = new Object_News();
		foreach ($this->getAvailableLanguages() as $language) {
			$item->setTitle("Demo newsarticle in $language", $language);
			$item->setContent("Demo content in $language: " . $this->getDemoContent(), $language);
		}
		$item->setDate(new Pimcore_Date());
		$item->setParent($folder);
		$item->setPublished(true);
		$item->setKey('demo-news-article');
		$item->save();

		// newsletter was enabled -> enable the modules
		if (Pimcore_ExtensionManager::isEnabled('plugin', 'SENewsletter')) {
			SENewsletter_Plugin::enableBricksByObjectName('news');
		}

		$this->_helper->json(
			array(
				"success" => true,
			),
			false
		);
	}

	protected function createObjectFolder($name)
	{
		$folder = new Object_Folder();
		$folder->setParentId(1);
		$folder->setKey($name);
		$folder->save();

		return $folder;
	}

	protected function getDemoContent()
	{
		return file_get_contents(PIMCORE_PLUGINS_PATH . 'SEBasic/data/demo_content.txt');
	}

	/**
	 * @throws Exception
	 */
	public function addVacancyPageAction()
	{
		$firstLanguageDocument = $this->getFirstLanguageDocument();

		$document = $this->createDocument(
			'vacatures',
			'vacancy',
			'overview',
			'Vacatures',
			$firstLanguageDocument,
			'Vacatures'
		);

		// Add class
		$service = new SEBasic_Service_Classes();
		$service->addClass('vacancy');

		// Add Static route
		$route = new Staticroute();
		$route->setValues(
			array(
				'name' => 'vacancy_detail',
				'pattern' => '/\/(.*)\/vacancy\/(.*)\/(.*)/',
				'reverse' => '/%language/vacancy/%id/%key',
				'controller' => 'vacancy',
				'action' => 'show',
				'variables' => 'language,id,key',
				'defaults' => 'nl,1,nokey'
			)
		);
		$route->save();

		// Add Document to website config
		$this->addWebsiteSetting('vacancy_document', 'document', $document->getId());

		// onderdelen folder
		$folder = $this->createOnderdelenFolder();

		// create email to send to the admin
		$email = $this->createEmail(
			'vacancy_admin',
			$folder,
			'Beste,
			%Text(name); heeft gesolliciteerd voor de vacature %Text(vacancyTitle);.
			Dit waren de gegevens:
			%Text(mailContent);

			Eventuele bestanden kunnen gevonden worden in bijlage.'
		);

		// Add Document to website config
		$this->addWebsiteSetting('email_vacancy_admin', 'document', $email->getId());

		$email = $this->createEmail(
			'vacancy_user',
			$folder,
			'Beste %Text(name);,
			Bedankt om te solliciteren voor de vacature %Text(vacancy);.
			Dit waren uw gegevens:
			%Text(mailContent);'
		);

		// Add Document to website config
		$this->addWebsiteSetting('email_vacancy_user', 'document', $email->getId());

		// Create object folder
		$folder = $this->createObjectFolder('vacatures');

		// Create demo object
		$item = new Object_Vacancy();
		$item->setParent($folder);
		$item->setKey('demo-vacature');
		$item->setPublished(true);
		foreach ($this->getAvailableLanguages() as $language) {
			$item->setTitle("Demo vacancy in $language", $language);
			$item->setContent("Demo content in $language: " . $this->getDemoContent(), $language);
		}
		$item->setDate(new Pimcore_Date());
		$item->save();

		// newsletter was enabled -> enable the modules
		if (Pimcore_ExtensionManager::isEnabled('plugin', 'SENewsletter')) {
			SENewsletter_Plugin::enableBricksByObjectName('vacancy');
		}

		$this->_helper->json(
			array(
				"success" => true,
			),
			false
		);
	}

	/**
	 * @throws Exception
	 */
	public function addEventsPageAction()
	{
		$firstLanguageDocument = $this->getFirstLanguageDocument();

		$document = $this->createDocument('events', 'event', 'overview', 'Eventes', $firstLanguageDocument, 'Events');

		// Add class
		$service = new SEBasic_Service_Classes();
		$service->addClass('event');

		// Add Static route
		$route = new Staticroute();
		$route->setValues(
			array(
				'name' => 'event_detail',
				'pattern' => '/\/(.*)\/event\/(.*)\/(.*)/',
				'reverse' => '/%language/event/%id/%key',
				'controller' => 'event',
				'action' => 'show',
				'variables' => 'language,id,key',
				'defaults' => 'nl,1,nokey'
			)
		);
		$route->save();

		// Add Document to website config
		$this->addWebsiteSetting('event_document', 'document', $document->getId());

		// onderdelen folder
		$folder = $this->createOnderdelenFolder();

		// create email to send to the admin
		$email = $this->createEmail(
			'event_admin',
			$folder,
			'Beste,
			%Text(name); heeft zich ingeschreven voor het evenement %Text(eventTitle);

			Dit waren de gegevens:
			%Text(mailContent);'
		);

		// Add Document to website config
		$this->addWebsiteSetting('email_event_admin', 'document', $email->getId());

		// create email to send to the user
		$email = $this->createEmail(
			'event_user',
			$folder,
			'Beste %Text(name);,
			u bent ingeschreven voor het evenement %Text(eventTitle);

			Dit zijn uw gegevens:
			%Text(mailContent);'
		);

		// Add Document to website config
		$this->addWebsiteSetting('email_event_user', 'document', $email->getId());

		// Create object folder
		$folder = $this->createObjectFolder('evenementen');

		// Add demo data
		$item = new Object_Event();
		$item->setParent($folder);
		$item->setKey('demo-event');
		$item->setPublished(true);
		foreach ($this->getAvailableLanguages() as $language) {
			$item->setTitle("Demo event in $language", $language);
			$item->setContent("Demo content in $language: " . $this->getDemoContent(), $language);
		}
		$item->setDate(new Pimcore_Date());
		$item->save();


		// newsletter was enabled -> enable the modules
		if (Pimcore_ExtensionManager::isEnabled('plugin', 'SENewsletter')) {
			SENewsletter_Plugin::enableBricksByObjectName('event');
		}

		$this->_helper->json(
			array(
				"success" => true,
			),
			false
		);
	}

	/**
	 * Add the search module/page
	 *
	 * @throws Exception
	 */
	public function addSearchPageAction()
	{
		$firstLanguageDocument = $this->getFirstLanguageDocument();

		// Create document in tree for search
		$document = $this->createDocument('search', 'search', 'search', '', $firstLanguageDocument, 'Zoeken', true);

		// Add Document to website config
		$this->addWebsiteSetting('search_document', 'document', $document->getId());

		$this->_helper->json(
			array(
				"success" => true,
			),
			false
		);
	}

	/**
	 * @return Document|Document_Folder
	 * @throws Exception
	 */
	protected function createOnderdelenFolder()
	{
		$folder = Document::getByPath('/' . $this->getFirstAvailableLanguage() . '/_onderdelen/');

		if (!$folder) {
			$firstLanguageDocument = $this->getFirstLanguageDocument();

			$folder = new Document_Folder();
			$folder->setParent($firstLanguageDocument);
			$folder->setParentId($firstLanguageDocument->getId());
			$folder->setKey('_onderdelen');
			$folder->save();
		}

		return $folder;
	}

	public function getConfigFormAction()
	{
		$systemConfig = Pimcore_Config::getSystemConfig();
		$websiteConfig = Pimcore_Config::getWebsiteConfig();

		$formItems = array();

		$formItems[] = $this->generateFormItem('site_title', $websiteConfig->site_title, 'Website title');
		$formItems[] = $this->generateFormItem('email_name', $systemConfig->email->sender->name, 'Email Sender Name');
		$formItems[] = $this->generateFormItem(
			'email_email',
			$systemConfig->email->sender->email,
			'Email Sender Email'
		);
		$formItems[] = $this->generateFormItemComboBox(
			'cache',
			array('MysqlTable', 'Redis', 'Memcached'),
			$this->getActiveCacheBackendClassName(),
			'Cache Backend'
		);

		$this->_helper->json(
			array(
				"success" => true,
				"items" => $formItems
			),
			false
		);
	}

	protected function getActiveCacheBackendClassName()
	{
		return str_replace('Pimcore_Cache_Backend_', '', get_class(Pimcore_Model_Cache::getInstance()->getBackend()));
	}

	protected function generateFormItem($name, $value, $fieldLabel, $type = 'textfield')
	{
		return array(
			'id' => 'config_' . $name,
			'xtype' => $type,
			'value' => $value,
			'checked' => $value,
			'fieldLabel' => $fieldLabel,
			'name' => $name,
			'width' => 250
		);
	}

	protected function generateFormItemComboBox($name, $values, $selectedValue, $fieldLabel)
	{
		if (!is_array($values)) {
			$values = array(
				$values
			);
		}
		return array(
			'xtype' => 'combo',
			'id' => 'config_' . $name,
			'name' => $name,
			'fieldLabel' => $fieldLabel,
			'value' => $selectedValue,
			'store' => $values,
			'typeAhead' => true,
			'editable' => false,
			'triggerAction' => 'all'
		);
	}

	public function addConfigAction()
	{
		if ($this->_getParam('data', false)) {
			$data = json_decode($this->_getParam('data'));

			$oldConfig = Pimcore_Config::getSystemConfig();
			$systemSettings = $oldConfig->toArray();
			$systemSettings['email']['sender']['name'] = $data->email_name;
			$systemSettings['email']['sender']['email'] = $data->email_email;
			$config = new Zend_Config($systemSettings, true);
			$writer = new Zend_Config_Writer_Xml(
				array(
					"config" => $config,
					"filename" => PIMCORE_CONFIGURATION_SYSTEM
				)
			);
			$writer->write();

			$websiteSetting = WebsiteSetting::getByName('site_title');
			$websiteSetting->setData($data->site_title);
			$websiteSetting->setType('text');
			$websiteSetting->save();

			// Install the correct cache backend
			if ($this->getActiveCacheBackendClassName() != $data->cache) {
				switch ($data->cache) {
					case 'Memcached':
						@unlink(PIMCORE_CONFIGURATION_DIRECTORY . '/cache.xml');
						copy(
							PIMCORE_PLUGINS_PATH . '/SEBasic/data/cacheConfig/cache.xml.memcached',
							PIMCORE_CONFIGURATION_DIRECTORY . '/cache.xml'
						);
						break;
					case 'MysqlTable':
						@unlink(PIMCORE_CONFIGURATION_DIRECTORY . '/cache.xml');
						break;
					case 'Redis':
						@unlink(PIMCORE_CONFIGURATION_DIRECTORY . '/cache.xml');
						copy(
							PIMCORE_PLUGINS_PATH . '/SEBasic/data/cacheConfig/cache.xml.redis',
							PIMCORE_CONFIGURATION_DIRECTORY . '/cache.xml'
						);

						$dbindex = 99;
						try {
							// Determine the next free database for redis on this server
							@exec('redis-next-key.sh', $output);

							if (is_array($output) && is_numeric(reset($output))) {
								$dbindex = reset($output);
							}
						} catch(Exception $e) {}

						file_put_contents(
							PIMCORE_CONFIGURATION_DIRECTORY . '/cache.xml',
							str_replace(
								'%DBINDEX%',
								$dbindex,
								file_get_contents(PIMCORE_CONFIGURATION_DIRECTORY . '/cache.xml')
							)
						);
						break;
				}
			}

			// Clear cache
			Pimcore_Model_Cache::clearAll();

			$this->_helper->json(
				array(
					"success" => true,
				),
				false
			);
		}
	}

}
