<?php

class Website_Form_Decorator_Date extends Zend_Form_Decorator_Abstract {
	public function render($content) {
		$element = $this->getElement();
		if (!$element instanceof Website_Form_Element_Date) {
			// only want to render Date elements
			return $content;
		}

		$view = $element->getView();
		if (!$view instanceof Zend_View_Interface) {
			// using view helpers, so do nothing if no view present
			return $content;
		}

		$day = $element->getDay();
		$month = $element->getMonth();
		$year = $element->getYear();
		$name = $element->getFullyQualifiedName();

		$params = array(
			'multiline' => false
		);

		if ($element->isRequired()) {
			$params['required'] = 'required';
		}

		// the days
		$days = array();
		for ($i = 1; $i <= 31; $i++) {
			$days[$i] = $i;
		}
		// the months
		$locale = new Zend_Locale();
		$months = $locale->getTranslationList('months')['format']['abbreviated'];
		// the years
		$years = array();
		for ($i = date('Y'); $i >= date('Y') - 100; $i--) {
			$years[$i] = $i;
		}

		$markup = $view->formSelect($name . '[day]', $day, $params, $days)
			. ' / ' . $view->formSelect($name . '[month]', $month, $params, $months)
			. ' / ' . $view->formSelect($name . '[year]', $year, $params, $years);

		switch ($this->getPlacement()) {
			case self::PREPEND:
				return $markup . $this->getSeparator() . $content;
			case self::APPEND:
			default:
				return $content . $this->getSeparator() . $markup;
		}
	}
}