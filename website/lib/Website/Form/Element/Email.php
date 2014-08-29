<?php
class Website_Form_Element_Email extends Website_Form_Element_Text {

	public $helper = 'email';

	public function __construct($spec, $options = null) {
		// add helper path
		$view = $this->getView();
		$view->addHelperPath(PIMCORE_WEBSITE_PATH.'/lib/Website/Form/Helper', 'Website_Form_Helper');

		// the magic
		parent::__construct($spec, $options);
		$this->setAttrib('type','email');

		// already add the validator
		$this->addValidator('EmailAddress');
	}


}