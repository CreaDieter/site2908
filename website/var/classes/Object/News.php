<?php 

class Object_News extends Object_Concrete {

public $o_classId = 1;
public $o_className = "news";
public $localizedfields;
public $date;
public $subsites;


/**
* @param array $values
* @return Object_News
*/
public static function create($values = array()) {
	$object = new self();
	$object->setValues($values);
	return $object;
}

/**
* @return array
*/
public function getLocalizedfields () {
	$preValue = $this->preGetValue("localizedfields"); 
	if($preValue !== null && !Pimcore::inAdmin()) { return $preValue;}
	$data = $this->getClass()->getFieldDefinition("localizedfields")->preGetData($this);
	 return $data;
}

/**
* @return string
*/
public function getTitle ($language = null) {
	$data = $this->getLocalizedfields()->getLocalizedValue("title", $language);
	$preValue = $this->preGetValue("title"); 
	if($preValue !== null && !Pimcore::inAdmin()) { return $preValue;}
	 return $data;
}

/**
* @return string
*/
public function getContent ($language = null) {
	$data = $this->getLocalizedfields()->getLocalizedValue("content", $language);
	$preValue = $this->preGetValue("content"); 
	if($preValue !== null && !Pimcore::inAdmin()) { return $preValue;}
	 return $data;
}

/**
* @param array $localizedfields
* @return void
*/
public function setLocalizedfields ($localizedfields) {
	$this->localizedfields = $localizedfields;
	return $this;
}

/**
* @param string $title
* @return void
*/
public function setTitle ($title, $language = null) {
	$this->getLocalizedfields()->setLocalizedValue("title", $title, $language);
	return $this;
}

/**
* @param string $content
* @return void
*/
public function setContent ($content, $language = null) {
	$this->getLocalizedfields()->setLocalizedValue("content", $content, $language);
	return $this;
}

/**
* @return Pimcore_Date
*/
public function getDate () {
	$preValue = $this->preGetValue("date"); 
	if($preValue !== null && !Pimcore::inAdmin()) { return $preValue;}
	$data = $this->date;
	 return $data;
}

/**
* @param Pimcore_Date $date
* @return void
*/
public function setDate ($date) {
	$this->date = $date;
	return $this;
}

/**
* @return array
*/
public function getSubsites () {
	$preValue = $this->preGetValue("subsites"); 
	if($preValue !== null && !Pimcore::inAdmin()) { return $preValue;}
	$data = $this->subsites;
	 return $data;
}

/**
* @param array $subsites
* @return void
*/
public function setSubsites ($subsites) {
	$this->subsites = $subsites;
	return $this;
}

protected static $_relationFields = array (
);

public $lazyLoadedFields = NULL;

}

