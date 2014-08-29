<?php


/**
 * Class SENewsletter_Plugin
 */
class SENewsletter_Plugin extends Pimcore_API_Plugin_Abstract implements Pimcore_API_Plugin_Interface {

	/**
	 * The website config entries for the enews plugin
	 *
	 * @var array
	 */
	protected static $_enews_config = ['username','password','api_key','url','from_email','from_name'];

	/**
	 * General function to create a new website setting
	 *
	 * @param $name
	 * @param $type
	 * @param $data
	 */
	protected static function addWebsiteSetting($name, $type, $data)
	{
		if (!WebsiteSetting::getByName($name)) {
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
	}

	/**
	 * Install the plugin
	 *
	 * @return bool
	 */
	public static function install() {
		$areas = array();
		// get the areas folders
		$pluginAreasFolder = PIMCORE_PLUGINS_PATH . '/SENewsletter/views/areas';
		$generalAreasPath = PIMCORE_WEBSITE_PATH . '/var/areas';
		if (is_dir($pluginAreasFolder) && is_dir($generalAreasPath)) {
			$areas = scandir($pluginAreasFolder);
			foreach ($areas as $area) {
				if ($area != '.' && $area != '..' && is_dir($pluginAreasFolder . '/' . $area)) {
					// the directory already exists?
					if (is_dir($generalAreasPath . '/' . $area)) {
						rename($generalAreasPath . '/' . $area, $generalAreasPath . '/' . $area . '_old' . time());
					}
					mkdir($generalAreasPath . '/' . $area);
					// copy the area from the plugin folder to the var/areas folder
					self::rCopyDirectory($pluginAreasFolder . '/' . $area, $generalAreasPath . '/' . $area);
				}
			}
		}

		// enable the needed bricks
		$configs = Pimcore_ExtensionManager::getBrickConfigs();
		error_reporting(E_ERROR); // only display errors (because warning when checking if class exists)
		foreach ($configs as $config) {
			if (in_array($config->id, $areas)) {
				$kaboom = explode('-', $config->id);
				if (count($kaboom) == 1 || $kaboom[0] == 'newsletter') {
					// no module (e.g. news, vacancy...) -> always enable
					Pimcore_ExtensionManager::enable('brick', $config->id);
				} else {
					$pluginName = reset($kaboom);
					// the object class exists -> enable
					if (class_exists('Object_' . ucfirst(strtolower($pluginName)))) {
						Pimcore_ExtensionManager::enable('brick', $config->id);
					}
				}
			}
		}

		// add website settings
		for ($i = 0; $i < count(self::$_enews_config); $i++) {
			self::addWebsiteSetting('enews_' . self::$_enews_config[$i],'text','');
		}

		// Add Static route
		$route = new Staticroute();
		$route->setValues(
			array(
				'name' => 'newsletter',
				'pattern' => '/\/(.*)\/newsletter\/(.*)/',
				'reverse' => '/%language/news/%id/%key',
				'module' => 'SENewsletter',
				'controller' => 'newsletter',
				'action' => '%action',
				'variables' => 'language,action',
				'defaults' => 'nl'
			)
		);
		$route->save();

		// prevent multiple definitions
		if (!self::docTypeExists()) {
			// add email doc type
			$docType = new Document_DocType();
			$docType->setValues(
				array(
					'name' => 'nieuwsbrief',
					'module' => 'SENewsletter',
					'controller' => 'newsletter',
					'action' => 'default',
					'type' => 'email'
				)
			);
			$docType->save();
		}

		return true;
	}

	/**
	 * Recursively copy a directory
	 *
	 * @param $source
	 * @param $dest
	 * @return bool
	 */
	private static function rCopyDirectory($source, $dest) {
		$sourceHandle = opendir($source);

		if (!$sourceHandle) {
			echo 'failed to copy directory: failed to open source ' . $source;
			return false;
		}

		while ($file = readdir($sourceHandle)) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			if (is_dir($source . '/' . $file)) {
				if (!file_exists($dest . '/' . $file)) {
					mkdir($dest . '/' . $file, 755);
				}
				self::rCopyDirectory($source . '/' . $file, $dest . '/' . $file);
			} else {
				copy($source . '/' . $file, $dest . '/' . $file);
			}
		}

		return true;
	}

	/**
	 * Check if the newsletter document type exists
	 *
	 * @return bool
	 */
	private static function docTypeExists() {
		return (self::getDocType() instanceof Document_DocType);
	}

	/**
	 * Get the newsletter document type
	 *
	 * @return mixed
	 */
	private static function getDocType() {
		$list = new Document_DocType_List();
		$list->setCondition('name = ?', 'nieuwsbrief');
		$list = $list->load();
		return reset($list);
	}

	/**
	 * Uninstall the plugin
	 *
	 * @return bool
	 */
	public static function uninstall() {
		$areas = array();
		$configs = Pimcore_ExtensionManager::getBrickConfigs();
		// get the ares folders
		$pluginAreasFolder = PIMCORE_PLUGINS_PATH . '/SENewsletter/views/areas';
		$generalAreasPath = PIMCORE_WEBSITE_PATH . '/var/areas';
		if (is_dir($pluginAreasFolder) && is_dir($generalAreasPath)) {
			$areas = scandir($pluginAreasFolder);
			foreach ($areas as $area) {
				if ($area != '.' && $area != '..' && is_dir($pluginAreasFolder . '/' . $area)) {
					// remove all directories with the same name
					if (is_dir($generalAreasPath . '/' . $area)) {
						self::rDeleteDirectory($generalAreasPath . '/' . $area);
					}
				}
			}
		}

		// disable all bricks from this plugin
		foreach ($configs as $config) {
			if (in_array($config->id, $areas)) {
				Pimcore_ExtensionManager::disable('brick', $config->id);
			}
		}

		// delete website settings
		for ($i = 0; $i < count(self::$_enews_config); $i++) {
			$setting = WebsiteSetting::getByName('enews_' . self::$_enews_config[$i]);
			if ($setting) {
				$setting->delete();
			}
		}

		// delete the static route
		$route = Staticroute::getByName('newsletter');
		$route->delete();

		// delete email doc type
		$list = new Document_DocType_List();
		$list->setCondition('name = ?', 'nieuwsbrief');
		$list = $list->load();
		foreach ($list as $l) {
			$l->delete();
		}

		return true;
	}

	/**
	 * Enable the bricks (areas) by a given object name
	 *
	 * @param $name
	 * @return bool
	 */
	public static function enableBricksByObjectName($name) {
		$pluginAreasFolder = PIMCORE_PLUGINS_PATH . '/SENewsletter/views/areas';
		$configs = Pimcore_ExtensionManager::getBrickConfigs();

		$brickIds = array();

		// get all bricks from this module
		if (is_dir($pluginAreasFolder)) {
			$areas = scandir($pluginAreasFolder);
			foreach ($areas as $area) {
				if ($area == '.' || $area == '..' || !is_dir($pluginAreasFolder . '/' . $area)) {
					continue;
				}
				if(strpos($area, strtolower($name)) === 0) {
					$brickIds[] = $area;
				}
			}
		}

		// there were bricks with this id -> enable them
		if (count($brickIds) > 0) {
			foreach ($configs as $config) {
				if (in_array($config->id,$brickIds)) {
					Pimcore_ExtensionManager::enable('brick',$config->id);
				}
			}
		}

		return true;
	}

	/**
	 * Recursively remove a directory
	 *
	 * @param $source
	 * @return bool
	 */
	private static function rDeleteDirectory($source) {
		$dir_handle = opendir($source);

		if (!$dir_handle) {
			return false;
		}

		while ($file = readdir($dir_handle)) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			if (!is_dir($source . '/' . $file)) {
				unlink($source . '/' . $file);
			} else {
				self::rDeleteDirectory($source . '/' . $file);
			}
		}

		closedir($dir_handle);

		if (is_dir($source)) {
			rmdir($source);
		}

		return true;
	}

	/**
	 * Check if the plugin is installed
	 *
	 * @return bool
	 */
	public static function isInstalled() {
		try {
			$route = Staticroute::getByName('newsletter');
		} catch (Exception $e) {
			$route = null;
		}

		if ($route && self::docTypeExists()) {
			return true;
		}

		// implement your own logic here
		return false;
	}

	/**
	 * init function
	 */
	public function init() {}
}
