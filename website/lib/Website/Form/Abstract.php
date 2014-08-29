<?php

class Website_Form_Abstract extends Zend_Form {

	public function __construct() {
		parent::__construct();
	}


	/**
	 * These are the custom form elements that we have created (see Elements folder)
	 *
	 * @var array
	 */
	private $allowed_Elements = array(
		'email',
		'password',
		'select',
		'text',
		'textarea',
		'url',
		'checkbox',
		'radio',
		'date'
	);

	/**
	 * Add one of the custom form elements from Website_Form_Element
	 * This function creates one, sets basic properties and returns the element
	 *
	 * @param      $type
	 * @param      $name
	 * @param      $label
	 * @param bool $required
	 * @return Zend_Form_Element
	 * @throws Exception
	 */
	protected function getCustomFormElement($type, $name, $label, $required = false){
		if (!in_array(strtolower($type),$this->allowed_Elements)) throw new Exception ("Element with the type $type does not exist");

		$classname = 'Website_Form_Element_' . ucfirst(strtolower($type));
		$element = new $classname($name);
		$this->addDefaultValuesToElement($element,$label,$required);
		return $element;
	}

	/**
	 * Set the label and required-property for a given element
	 *
	 * @param Zend_Form_Element $element
	 * @param                   $label
	 * @param                   $required
	 */
	protected function addDefaultValuesToElement(Zend_Form_Element &$element,$label,$required) {
		$element->setLabel($label);
		$element->setRequired($required);
	}

	/**
	 * Get the filled in elements of a form as a string
	 *
	 * @return string
	 */
	public function getFormContent() {
		$params = $this->getValues();
		$mailContent = '';
		foreach ($params as $field => $value) {
			$el = $this->getElement($field);
			if ($el) {
				$mailContent .= '<strong>' . $el->getLabel() . ':</strong> ' . $value . '<br>';
			}
		}
		return $mailContent;
	}

}