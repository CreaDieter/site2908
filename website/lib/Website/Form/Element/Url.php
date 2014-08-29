<?php
class Website_Form_Element_Url extends Website_Form_Element_Text {

	public $helper = 'urlInput';

	public function __construct($spec, $options = null) {
		// add helper path
		$view = $this->getView();
		$view->addHelperPath(PIMCORE_WEBSITE_PATH.'/lib/Website/Form/Helper', 'Website_Form_Helper');

		// the magic
		parent::__construct($spec, $options);
		$this->setAttrib('type','url');

		// already add the validator
		$this->addValidator(new Website_Form_Validate_ValidUrl());
	}


}