<?php

class Website_Form_Element_Date extends Zend_Form_Element_Xhtml {

	protected $_dateFormat = '%year%-%month%-%day%';
	protected $_day;
	protected $_month;
	protected $_year;
	protected $_required = false;

	public function __construct($spec, $options = null) {
		$this->addPrefixPath(
			'Website_Form_Decorator',
			'Website/Form/Decorator',
			'decorator'
		);
		parent::__construct($spec, $options);
	}

	public function loadDefaultDecorators() {
		if ($this->loadDefaultDecoratorsIsDisabled()) {
			return;
		}

		$decorators = $this->getDecorators();
		if (empty($decorators)) {
			$this->addDecorator('Date');
			$this->addDecorator('Errors')
				->addDecorator(
					'Description',
					array(
						'tag' => 'p',
						'class' => 'description'
					)
				)
				->addDecorator(
					'HtmlTag',
					array(
						'tag' => 'dd',
						'id' => $this->getName() . '-element'
					)
				)
				->addDecorator('Label', array('tag' => 'dt'));
		}
	}

	public function isValid($value) {
		// set the value
		$this->setValue($value);

		$view = new Pimcore_View();

		// check for errors
		$errors = array();

		// required field
		if ($this->isRequired()) {
			$valid = true;
			foreach ($value as $val) {
				if (!is_numeric($val) || !$val > 0) {
					$valid = false;
				}
			}
			if (!$valid) {
				$errors[] = $view->translate('Dit veld is verplicht');
			}
		}

		// check for valid date
		if (!checkdate($value['month'], $value['day'], $value['year'])) {
			$errors[] = $view->translate('De datum is ongeldig');
		}

		// add errors
		if (count($errors) > 0) {
			$this->setErrors($errors);
			return false;
		}

		// return parent behavior
		return parent::isValid($value);
	}

	public function setValue($value) {
		if (is_int($value)) {
			$this->setDay(date('d', $value))
				->setMonth(date('m', $value))
				->setYear(date('Y', $value));
		} elseif (is_string($value)) {
			$date = strtotime($value);
			$this->setDay(date('d', $date))
				->setMonth(date('m', $date))
				->setYear(date('Y', $date));
		} elseif (is_array($value)
			&& (isset($value['day'])
				&& isset($value['month'])
				&& isset($value['year'])
			)
		) {
			$this->setDay($value['day'])
				->setMonth($value['month'])
				->setYear($value['year']);
		} else {
			throw new Exception('Invalid date value provided');
		}

		return $this;
	}

	public function isRequired() {
		return $this->_required;
	}

	public function setRequired($required) {
		$this->_required = $required;
		parent::setRequired($required);
	}

	public function getValue() {
		return str_replace(
			array('%year%', '%month%', '%day%'),
			array($this->getYear(), $this->getMonth(), $this->getDay()),
			$this->_dateFormat
		);
	}

	public function getYear() {
		return $this->_year;
	}

	public function setYear($value) {
		$this->_year = (int)$value;
		return $this;
	}

	public function getMonth() {
		return $this->_month;
	}

	public function setMonth($value) {
		$this->_month = (int)$value;
		return $this;
	}

	public function getDay() {
		return $this->_day;
	}

	public function setDay($value) {
		$this->_day = (int)$value;
		return $this;
	}
}