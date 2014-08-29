<?php

class SEBasic_Service_Classes
{
	public function getClassnames()
	{
		$path = PIMCORE_PLUGINS_PATH . '/SEBasic/exports/';
		$files = array_diff(scandir($path), array('.', '..', '.git', '.dummy'));


		// Get Existing classes
		$classesList = new Object_Class_List();
		$classesList->setOrderKey("name");
		$classesList->setOrder("asc");
		$classes = $classesList->load();

		$classNames = array();

		foreach ($classes as $class) {
			$classNames[] = $class->getName();
		}


		$classes = array();
		foreach ($files as $file) {
			$className = str_replace(array('class_', '_export.json'), '', $file);

			if (!in_array($className, $classNames)) {
				$classes[] = $className;
			}
		}

		return $classes;
	}

	public function addClass($className)
	{
		$result = false;

		if ($className) {
			$filePath = PIMCORE_PLUGINS_PATH . '/SEBasic/exports/class_' . $className . '_export.json';

			if (file_exists($filePath)) {
				$user = Pimcore_Tool_Admin::getCurrentUser();
				$userId = 1;
				if($user) {
					$userId = $user->getId();
				}

				$class = Object_Class::create(
					array(
						'name' => $this->correctClassname($className),
						'userOwner' => $userId
					)
				);

				$json = file_get_contents($filePath);

				$result = Object_Class_Service::importClassDefinitionFromJson($class, $json);
				$class->save();
			}
		}

		return $result;
	}

	protected function correctClassname($name)
	{
		$tmpFilename = $name;
		$validChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$filenameParts = array();

		for ($i = 0; $i < strlen($tmpFilename); $i++) {
			if (strpos($validChars, $tmpFilename[$i]) !== false) {
				$filenameParts[] = $tmpFilename[$i];
			}
		}

		return implode("", $filenameParts);
	}

}