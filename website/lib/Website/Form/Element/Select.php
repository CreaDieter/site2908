<?php
class Website_Form_Element_Select extends Zend_Form_Element_Select {


	/**
	 * Set required flag
	 *
	 * @param bool $r
	 * @throws Zend_Form_Exception
	 * @return void|Zend_Form_Element
	 */
	public function setRequired($r = true) {
		// keep the parent functionality
		$return = parent::setRequired($r);

		// add (of remove) the html5 required-parameter
		if ($r) $this->setAttrib('required', 'required');
		else $this->setAttrib('required', 'null');

		return $return;
	}
}