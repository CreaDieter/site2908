<?php


class SEBasic_Plugin  extends Pimcore_API_Plugin_Abstract implements Pimcore_API_Plugin_Interface {
    
	public static function install (){
		// we need a simple way to indicate that the plugin is installed, so we'll create a directory
		$path = self::getInstallPath();

		self::doInstall();

		if (!is_dir($path)) {
			mkdir($path);
		}

		if (self::isInstalled()) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function uninstall (){
		if (!self::isInstalled()) {
			return true;
		} else {
			return false;
		}
	}

	public static function needsReloadAfterInstall() {
		return true;
	}

	public static function isInstalled()
	{
		if (isset(Pimcore_Config::getWebsiteConfig()->site_title)) {
			return true;
		}

		return false;
	}

	public static function getInstallPath()
	{
		return PIMCORE_PLUGINS_PATH . "/SEBasic/install";
	}

	public static function doInstall() {
		// Add Website title placeholder
		$setting = new WebsiteSetting();
		$setting->setValues(array(
				'name'=>'site_title',
				'type'=>'text',
				'data'=>'Website Title'
			));
		$setting->save();

		// Reset first page
		$document = Document_Page::getById(1);
		$document->setController("default");
		$document->setAction('language-detection');
		$document->save();

		// Add SMTP settings
		$oldConfig = Pimcore_Config::getSystemConfig();
		$settings = $oldConfig->toArray();

		$settings['email']['method'] = 'smtp';
		$smtp = $settings['email']['smtp'];
		$smtp['host'] = 'smtpservices.studioemma.com';
		$smtp['auth']['method'] = 'login';
		$smtp['auth']['username'] = 'STEMTRANSTRUSTED';
		$smtp['auth']['password'] = 'nuzd54ht64cv454fr54a89a87q548l';
		$settings['email']['smtp'] = $smtp;
		$settings['email']['sender']['name'] = "Company name";
		$settings['email']['sender']['email'] = "info@company.com";
		$settings['email']['debug']['emailaddresses'] = "pieter@studioemma.eu";
		$settings['documents']['default_controller'] = 'content';
		$settings['general']['validLanguages'] = 'nl';

		$config = new Zend_Config($settings, true);
		$writer = new Zend_Config_Writer_Xml(array(
				"config" => $config,
				"filename" => PIMCORE_CONFIGURATION_SYSTEM
			));
		$writer->write();

		// Clear cache
		Pimcore_Model_Cache::clearAll();

		Zend_Registry::set("pimcore_config_system", $config);

		// Enable SEI18N plugin
		$pluginName = 'SEInternationalisation';
		Pimcore_ExtensionManager::enable('plugin',$pluginName);
		try {
			$config = Pimcore_ExtensionManager::getPluginConfig($pluginName);
			$className = $config["plugin"]["pluginClassName"];
			$pluginFilePath = 'plugins/' . $pluginName . '/lib/' . $pluginName . '/Plugin.php';
			if (file_exists($pluginFilePath)) {
				require_once $pluginFilePath;
			}
			$message = $className::install();
		} catch(Exception $e) {
			echo '<pre>';
			print_r($e);
			echo '</pre>';
			exit;
		}

		// Add predefined property
		$property = Property_Predefined::create();
		$property->setValues(array(
				'key'=>'doNotSyncProperties',
				'name'=>'I18N: Do not sync properties',
				'description'=>'Do not sync properties across documents in other languages',
				'data'=>1,
				'type'=>'bool',
				'ctype'=>'document',
				'inheritable'=>false

			));

		$property->save();
	}

}
