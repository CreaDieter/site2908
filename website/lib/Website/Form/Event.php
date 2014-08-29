<?php

class Website_Form_Event extends Website_Form_Abstract {

	public function __construct($addFileUpload = false) {
		parent::__construct();

		// add the form elements
		$this->addElement($this->getCustomFormElement('text', 'name', 'Name', true));
		$this->addElement($this->getCustomFormElement('email', 'email', 'E-mail address', true));
		$this->addElement($this->getCustomFormElement('text', 'phone', 'Phone', true));
		$this->addElement($this->getCustomFormElement('text', 'street', 'Straat + nr', true));
		$this->addElement($this->getCustomFormElement('text', 'postalcode', 'Postcode', true));
		$this->addElement($this->getCustomFormElement('text', 'city', 'Stad', true));

		// add the submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Inschrijven');
		$this->addElement($submit);
	}

}