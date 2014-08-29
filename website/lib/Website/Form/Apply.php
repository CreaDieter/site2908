<?php

class Website_Form_Apply extends Website_Form_Abstract {

	public function __construct($addFileUpload = false) {
		parent::__construct();

		// add the form elements
		$this->addElement($this->getCustomFormElement('text', 'name', 'Name', true));
		$this->addElement($this->getCustomFormElement('email', 'email', 'E-mail address', true));
		$this->addElement($this->getCustomFormElement('text', 'phone', 'Phone', true));
		$this->addElement($this->getCustomFormElement('date', 'birthdate', 'Geboortedatum', true));
		$this->addCvElement();
		$this->addElement($this->getCustomFormElement('textarea', 'motivation', 'Motivatie', true));

		// add the submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Solliciteer');
		$this->addElement($submit);
	}

	/**
	 * Add the element for the cv
	 *
	 * @throws Zend_Form_Exception
	 */
	private function addCvElement() {
		$el = new Website_Form_Element_File('cv');
		$el->setLabel('Cv');
		$el->setRequired(true);
		$el->addValidator('Size', true, '2MB');
		$el->setAllowedMimeTypes(
			array(
				'image/gif',
				'image/jpeg',
				'image/pjpeg',
				'image/png',
				'application/msword',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'application/pdf'
			),
			'Verkeerd bestandsformaat. Enkel .doc, .docx, .pdf, .jpg, .gif en .png toegelaten.'
		);
		$this->addElement($el);
	}

}