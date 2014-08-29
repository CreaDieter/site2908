<?php
class Website_Form_Element_Text extends Zend_Form_Element_Text {


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
		if ($r) $this->setAttrib('required', 'required');
		else $this->setAttrib('required', 'null');

		return $return;
	}

	/**
	 * Set the html5 placeholder parameter
	 *
	 * @param $label
	 */
	public function setPlaceholder($label) {
		$this->setAttrib('placeholder', $label);
	}

}