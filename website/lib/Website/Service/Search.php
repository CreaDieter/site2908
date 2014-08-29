<?php

class Website_Service_Search {

	private $_subsite;

	/* @var array allowed_alementTypes - the allowed elementtypes to index */
	private $allowed_elementTypes = array('Document_Tag_Wysiwyg', 'Document_Tag_Input');

	/**
	 * Function that creates a lucene index for the whole website
	 * (for all pages and subpages)
	 */
	public function createLuceneIndex($language, $subsiteID = null) {
		/** @var string $luceneDirectory The directory where the index is stored at the end */
		$luceneDirectory = $this->getLuceneDirectory($language, $subsiteID);
		/** @var string $luceneDirectory_tmp The temp directory where the index is build. The index is moved to $luceneDirectory when it's ready */
		$luceneDirectory_tmp = $luceneDirectory . '_tmp';
		// remove the old tmp index
		$this->rrmdir($luceneDirectory_tmp);
		/** @var Zend_Search_Lucene_Interface $index */
		$index = Zend_Search_Lucene::create($luceneDirectory_tmp, true);
		// get the subsite
		$subsite = (!is_null($subsiteID) && is_numeric($subsiteID)) ? $this->getSubsiteById($subsiteID) : null;


		/* index the objects */
		$config = $this->getObjectsLuceneConfig(true);
		// set variables
		$variables = array();
		if ($subsite instanceof Site) { // set subsitekey if we are in a subsite
			$variables['subsiteKey'] = $subsite->getRootDocument()->getKey();
		}
		$this->indexObjectList($config, $index, $language, $variables);

		/* index the documents */
		if ($subsite instanceof Site) {
			$this->indexDocumentsBySubsite($subsite, $language, $index);
		} else {
			$this->indexDocumentsByRootId(1, $language, $index);
		}

		// optimize the index
		$index->optimize();

		// tmp index is ready, now move it to the real index
		$this->rrmdir($luceneDirectory);
		exec("mv $luceneDirectory_tmp $luceneDirectory");
	}



	/**
	 * Get the lucene directory
	 *
	 * @param $language
	 * @param $subsiteID
	 * @throws Exception
	 * @internal param $subsite
	 * @return string
	 */
	private function getLuceneDirectory($language, $subsiteID = null) {
		if (is_null($subsiteID) || !is_numeric($subsiteID)) {
			// get the key for the complete project
			$key = 'websiteAll';
		} else {
			// get the key for the subsite
			$subsite = $this->getSubsiteById($subsiteID);
			$key = $subsite->getRootDocument()->getKey();
		}

		// return the path
		return PIMCORE_DOCUMENT_ROOT . '/lucene/' . $language . '/' . $key;
	}

	/**
	 * Get a subsite by the given is
	 *
	 * @param int  $subsiteID The id of the subsite
	 * @param bool $force     re-set the subsite, even if it has been set before
	 * @throws Exception
	 * @return Site
	 */
	private function getSubsiteById($subsiteID, $force = false) {
		if ($force || !$this->_subsite instanceof Site) {
			try {
				$this->_subsite = Site::getById($subsiteID);
			} catch (Exception $e) {
				throw new Exception("No subsite found with the id $subsiteID");
			}
		}

		return $this->_subsite;
	}

	/**
	 * Recursive remove a directory
	 *
	 * @param $dir
	 * @internal param $dirPath
	 */
	private function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir . "/" . $object) == "dir") {
						rrmdir($dir . "/" . $object);
					} else {
						unlink(
							$dir . "/" . $object
						);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

	/**
	 * This function get the config for objects
	 * this config is located in website/config/lucene/objects.xml
	 *
	 * @param bool $array return as array?
	 * @return array|Zend_Config_Xml
	 * @throws Exception
	 */
	private function getObjectsLuceneConfig($array = false) {
		$path = PIMCORE_WEBSITE_PATH . '/config/lucene/objects.xml';
		// damn, file does not exist
		if (!is_file($path)) {
			$objectsConfig =  $this->generateDefaultObjectConfig();
		} else {
			$objectsConfig = new Zend_Config_Xml($path);
		}

		// we want an array
		if ($array) $objectsConfig = $objectsConfig->toArray();

		return $objectsConfig;
	}

	/**
	 * Index the objects of this website
	 *
	 * !! Beware, the magic of this function will be explained below.
	 * !! Do not read further if you're not a php magician
	 *
	 * The keys of the array are the names of the object class that you want indexed (so you can filter on the classes)
	 * The value of an array item is another array with the fields of the object that you want to be indexed
	 * Now the magic is in the values of this array.
	 * These values can be
	 *
	 * a/ a string
	 *        just index this field as text
	 *
	 * b/ an array (this is where it gets interesting)
	 *        This array can have some keys and values. let's talk about the keys:
	 *        b.1/ field
	 *                The fieldname to index (this is required)
	 *        b.2/ value
	 *                The value that the given field has to have so the object is indexed
	 *                ! But... This value can also be an array, so you can give multiple possible values
	 *        b.3/ type
	 *                The type for Zend_Search_Lucene (e.g. text, unIndexed...)
	 *
	 * !!! You can find an example in website/config/lucene/objects.example.xml !!!
	 *
	 * @param array                        $classesAndFields the classes and their fields to be indexed
	 * @param Zend_Search_Lucene_Interface $index
	 * @param string                       $language
	 * @param array                        $variables
	 * @throws Exception
	 */
	protected function indexObjectList(
		array $classesAndFields,
		Zend_Search_Lucene_Interface &$index,
		$language = 'nl',
		$variables = array()
	) {
		// these are the possible values for a Zend_Search_Lucene_Field
		$possibleFieldValues = array(
			'keyword' => 'keyword',
			'binary' => 'binary',
			'text' => 'text',
			'unindexed' => 'unIndexed',
			'unstored' => 'unStored'
		);

		// re-format the variables ( key should be %key% )
		array_walk(
			$variables,
			function ($item, $key) use (&$result) {
				$result["%$key%"] = $item;
			},
			$result
		);
		$variables = is_null($result) ? array() : $result;

		foreach ($classesAndFields as $className => $fields) {
			$listName = 'Object_' . ucfirst(strtolower($className)) . '_List';
			if (class_exists($listName)) {
				// bummer, no fields given
				if (!isset($fields['fields'])) {
					throw new Exception("No fields given for $className");
				}
				$fields = $fields['fields'];

				/** @var Object_List_Concrete $list The list for the objects */
				$list = new $listName();
				/** @var array $indexFields The fields that we need to index */
				$indexFields = array();
				/** @var array $conditionArray The conditions for this array */
				$conditionArray = array();

				foreach ($fields as $fieldName => $options) {
					// default type is text
					$type = 'text';

					// options given for this field
					if (is_array($options)) {
						// there was a field type given?
						if (isset($options['type']) && isset($possibleFieldValues[strtolower($options['type'])])) {
							$type = $possibleFieldValues[strtolower($options['type'])];
						}

						// conditions given for this field?
						if (isset($options['values']['val'])) {
							$conditions = $options['values']['val'];
							if (is_array($conditions)) {
								// variables? set the value
								array_walk(
									$conditions,
									function (&$val) use ($variables) {
										if (array_key_exists($val, $variables)) {
											$val = $variables[$val];
										}
									}
								);
								$conditionArray[] = "$fieldName in (" . $list->quote($conditions) . ")";
							} else {
								// it's a variable -> set the value
								if (array_key_exists($conditions, $variables)) {
									$conditions = $variables[$conditions];
								}
								// add condition to list
								$conditionArray[] = "$fieldName = " . $list->quote($conditions);
							}
						}
					}

					// add the field to the array
					$indexFields[$fieldName] = $type;
				}

				// now check if we still need to add the key and the id and the className
				if (!isset($indexFields['key'])) {
					$indexFields['key'] = 'unIndexed';
				}
				if (!isset($indexFields['id'])) {
					$indexFields['id'] = 'unIndexed';
				}
				if (!isset($indexFields['className'])) {
					$indexFields['className'] = 'unIndexed';
				}


				// there were some conditions -> add it to the object list
				if (count($conditionArray) > 0) {
					$list->setCondition(implode(' AND ', $conditionArray));
				}

				// set the locale to the given language
				$list->setLocale($language);

				// get the objects
				$objects = $list->load();

				// now that we have the matiching objects, add them to the index
				foreach ($objects as $object) {
					$doc = new Zend_Search_Lucene_Document();
					// now get each given field and add it to the document
					foreach ($indexFields as $indexField => $fieldType) {
						if (method_exists($object, "get$indexField")) {

							if (is_string($object->{"get$indexField"}())) {
								// add the field to the lucene document
								$doc->addField(
									Zend_Search_Lucene_Field::$fieldType(
										$indexField,
										strip_tags($object->{"get$indexField"}())
									)
								);
								//Zend_Debug::dump(strip_tags($object->{"get$indexField"}()));
							}
						}
					}
					$index->addDocument($doc);
				}
			}
		}

		$index->commit();
	}

	/**
	 * Index the documents for a given language and given subsite
	 *
	 * @param Site                         $subsite
	 * @param                              $language
	 * @param Zend_Search_Lucene_Interface $index
	 * @throws Exception
	 * @internal param \Document_Page $subsiteRootDocument
	 */
	private function indexDocumentsBySubsite(Site $subsite, $language, Zend_Search_Lucene_Interface &$index) {
		$rootPath = $subsite->getRootPath();
		$rootKey = $subsite->getRootDocument()->getKey();


		//get the language folder
		try {
			$langFolder = Document_Page::getByPath("$rootPath/$language");
			if (is_null($langFolder)) {
				throw new Exception('Language folder not found');
			}
		} catch (Exception $e) {
			throw new Exception("the language $language does not exist for $rootKey");
		}

		// the root id
		$parentId = $langFolder->getId();

		// document list with all the languages
		$list = new Document_List();
		$list->setCondition("parentId = ?", $parentId);
		$documents = $list->load();

		// index the pages to the index
		foreach ($documents as $document) {
			$this->indexSubPages($document, $index);
		}

		$index->commit();
	}

	/**
	 * recursive function that indexes a page
	 * and also indexes its subpages
	 *
	 * @param $doc
	 * @param $index
	 */
	private function indexSubPages($doc, $index) {
		// check if it's a Document_Page (so no folder or such)
		if ($doc instanceof Document_Page) {
			// new zsl-doc
			$luceneDoc = new Zend_Search_Lucene_Document();

			// add the page index and the title
			$luceneDoc->addField(Zend_Search_Lucene_Field::unIndexed('document_id', $doc->getId()));
			$luceneDoc->addField(Zend_Search_Lucene_Field::text('document_title', $doc->getTitle()));

			// check for wysiwyg
			$wysiwygFound = false;

			// loop through all allowed elements of the document
			$docElements = $doc->getElements();
			foreach ($docElements as $docElement) {
				if (in_array(get_class($docElement), $this->allowed_elementTypes)) {
					// field for the first wysiwyg
					if ($docElement instanceof Document_Tag_Wysiwyg && !$wysiwygFound && $docElement->getValue() != ''
					) {
						$luceneDoc->addField(
							Zend_Search_Lucene_Field::unIndexed('firstWysiwig', $docElement->getValue())
						);
						$wysiwygFound = true;
					}

					// add the element to the zsl-doc
					$luceneDoc->addField(
						Zend_Search_Lucene_Field::text($docElement->getName(), $docElement->getValue())
					);
				}
			}

			// no wysiwyg found
			if (!$wysiwygFound) {
				$luceneDoc->addField(Zend_Search_Lucene_Field::unIndexed('firstWysiwig', ''));
			}

			// add the zsl-doc to the index
			$index->addDocument($luceneDoc);

			// **************************** //

			// get a list of the subpages
			$subList = new Document_List();
			$subList->setCondition("parentId = ?", $doc->getId());
			$subDocuments = $subList->load();

			// check if it has subpages
			if (sizeof($subDocuments > 0)) {
				// loop through subdocuments
				foreach ($subDocuments as $subDocument) {
					// index the subpage
					$this->indexSubPages($subDocument, $index);
				}
			}
		}
	}

	/**
	 * Index the documents by the given root id and the language
	 *
	 * @param                              $rootID
	 * @param                              $language
	 * @param Zend_Search_Lucene_Interface $index
	 */
	private function indexDocumentsByRootId($rootID, $language, Zend_Search_Lucene_Interface &$index) {
		// get the language root document (e.g. the /nl folder)
		$list = new Document_List();
		$list->setCondition("parentId = " . $list->quote($rootID) . " and `key` = " . $list->quote($language));
		$documents = $list->load();

		// loop through all documents (hope it's only one... :) )
		foreach ($documents as $document) {
			if ($document instanceof Document_Page) {
				// get a list of the subpages
				$subList = new Document_List();
				$subList->setCondition("parentId = ?", $document->getId());
				$subDocuments = $subList->load();

				// loop through subdocuments
				foreach ($subDocuments as $subDocument) {
					// index the subpage
					$this->indexSubPages($subDocument, $index);
				}
			}
		}

		// commit the index
		$index->commit();
	}

	/**
	 * Get the zend search lucene search result by keyword and language
	 *
	 *
	 * @param        $keyword
	 * @param        $subsiteID
	 * @param string $lang
	 * @return array
	 */
	public function getLuceneSearchResultByKeyword($keyword, $subsiteID = null, $lang = 'nl') {
		// open the lucene directory
		try {
			$index = Zend_Search_Lucene::open($this->getLuceneDirectory($lang, $subsiteID));
		} catch (Exception $e) {
			return array();
		}

		// there are 0 characters needed before the first wildcard (default is 3)
		Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);

		// do the magic
		$results = $index->find('*' . $keyword . '*');

		// return the results
		return $results;
	}


	/**
	 * Generate a default config for the objects
	 * Get all possible objects and add their fields to the config array
	 *
	 * @return array
	 */
	private function generateDefaultObjectConfig() {
		// we only allow these types of fields
		$allowed_types = [
			'input',
			'wysiwyg',
			'date',
			'multiselect',
			'checkbox',
			'select',
			'textarea'
		];

		$defaultConfig = array();
		$service = new Website_Service_Object();

		// get al classes
		$classnames = $service->getAllObjectClassNames();
		// get the fields for each class and set the config
		foreach ($classnames as $id => $name) {
			$fieldConfig = array();
			$fields = $service->getFieldsByObjectClassId($id);
			foreach ($fields as $field) {
				if (in_array($field->fieldtype,$allowed_types)) {
					$fieldConfig[] = $field->name;
				}
			}
			$defaultConfig[$name]['fields'] = $fieldConfig;
		}

		// create a zend config from the array
		return new Zend_Config($defaultConfig);
	}

}