<?php
class Website_Service_Object extends Website_Service_Abstract {


	/**
	 * Get all possible object class names
	 *
	 * @return array
	 */
	public function getAllObjectClassNames() {
		// select them from the database
		$db = Pimcore_Resource_Mysql::get();
		$select = $db->select()->from('classes', array('id','name'));
		$stmt = $db->query($select);
		$result = $stmt->fetchAll();

		// format as array($classId=>$className)
		$classes = array();
		foreach ($result as $r) {
			$classes[$r['id']] = $r['name'];
		}

		return $classes;
	}


	/**
	 * Get all fields of the object with given id
	 *
	 * @param $id
	 * @return array
	 */
	public function getFieldsByObjectClassId($id) {
		$fields = array();
		// the path to the class defenition
		$path = PIMCORE_WEBSITE_PATH . '/var/classes/definition_' . intval($id) . '.psf';
		if (is_file($path)) {
			// get the class defenitions (it's a serialized object that is stored in the file located at $path)
			$contents = unserialize(file_get_contents($path));
			// get all fields of the object
			$fields = $this->rSetFields($contents);
		}

		return $fields;
	}

	/**
	 * Recursive function to get the fields from an object class
	 *
	 * All elements in an object have the same parent class (Pimcore_Model_Abstract).
	 * But input elements (e.g. textarea, wysiwyg...) can't have children, all other elements can
	 * So... only add the elements that can't have childs to the $fields array, because they are the ones we need!
	 *
	 * @param       $object
	 * @param array $fields
	 * @return array
	 */
	private function rSetFields($object, $fields = array()) {
		if (method_exists($object,'getChilds')) {
			// it's a container... try the childs
			foreach ($object->getChilds() as $child) {
				$fields = $this->rSetFields($child,$fields);
			}
		} else {
			// it's an input element
			$fields[] = $object;
		}
		return $fields;
	}



}