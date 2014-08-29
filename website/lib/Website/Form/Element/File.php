<?php

class Website_Form_Element_File extends Zend_Form_Element_File {


	/**
	 * Set required flag
	 *
	 * @param bool $r
	 * @return void|Zend_Form_Element
	 */
	public function setRequired($r = true) {
		// keep the parent functionality
		$return = parent::setRequired($r);

		// add (of remove) the html5 required-parameter
		if ($r) {
			$this->setAttrib('required', 'required');
		} else {
			$this->setAttrib('required', 'null');
		}

		return $return;
	}

	/**
	 * Set the allowed mimetypes for the element
	 *
	 * @param array  $mimetypes
	 * @param string $errorMessage
	 */
	public function setAllowedMimeTypes(array $mimetypes, $errorMessage = '') {
		if (count($mimetypes) == 0) return;

		// set the html5 spec
		$this->setAttrib('accept', implode(',', $mimetypes));

		// set the php validator
		$this->addValidator('MimeType', true, $mimetypes);
		// override default error message if one is given
		if ($errorMessage != '') $this->getValidator('MimeType')->setMessage($errorMessage);
	}

}