<?php

class Website_Form_Offer extends Website_Form_Abstract {

	public function __construct($addFileUpload = false) {
		parent::__construct();

		// add the form elements
		$this->addElement($this->getCustomFormElement('text', 'name', 'Name', true));
		$this->addElement($this->getCustomFormElement('email', 'email', 'E-mail address', true));
		$this->addElement($this->getCustomFormElement('text', 'phone', 'Phone', false));
		$this->addElement($this->getCustomFormElement('textarea', 'message', 'Message', true));

		// add the submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Offerteaanvraag indienen');
		$this->addElement($submit);
	}

	private function addNameElement() {

	}





}