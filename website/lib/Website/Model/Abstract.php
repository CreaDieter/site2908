<?php

class Website_Model_Abstract extends Pimcore_Model_Abstract
{
	private $____pimcore_cache_item__;

	public function __get ($variable)
	{
		if (property_exists($this, $variable)) {
			return $this->{$variable};
		} else {
			throw new Exception("Property not found: " . $variable);
		}
	}

	public function __set ($variable, $value)
	{


		if (property_exists($this, $variable)) {
			$this->{$variable} = $value;
		} else {
			throw new Exception("Property not found: " . $variable);
		}
	}

	public function __call ($name, $argument)
	{
		if (method_exists($this->getResource(), $name)) {
			return parent::__call($name, $argument);
		}

		if (strpos(substr($name,0,3), 'get') !== false) {
			if (strpos($name, 'getBy') !== false) {
				$varName = lcfirst(str_replace('getBy', '', $name));
				return $this->getResource()->getByProperty($varName, $argument);
			} else {
				$varName = lcfirst(substr($name,3));

				if (property_exists($this, $varName)) {
					return $this->$varName;
				} else {
					throw new Exception(
						"Property $varName - $name does not exists in class " .
						get_class($this) . ", so cannot be get'd.");
				}
			}
		} elseif (strpos(substr($name,0,3), 'set') !== false) {
			$varName = lcfirst(substr($name,3));
			if (is_array($argument)) {
				$argument = $argument[0];
			}
			if (property_exists($this, $varName)) {
				return $this->$varName = $argument;
			} else {
				throw new Exception(
					"Poperty $varName does not exists in class " .
					get_class($this) . ", so cannot be set'd.");
			}
		} else {
			throw new Exception("Method not found: " . $name);
		}
	}

	public function getData ()
	{
		$data = array();
		$props = get_object_vars($this);
		$noDataProp = array(
			'resource'
		);
		foreach ($props as $property => $value) {
			if (substr($property, 0, 1) != "_" &&
				! in_array($property, $noDataProp)) {
				$getter = "get" . ucfirst($property);
				if (is_callable(array(
						$this,
						$getter
					))) {
					$value = $this->$getter();
				}
				$data[$property] = $value;
			}
		}
		return $data;
	}

	public function setData ($data)
	{
		$props = get_object_vars($this);
		$noDataProp = array(
			'resource'
		);
		foreach ($props as $property => $value) {
			if (substr($property, 0, 1) != "_" &&
				! in_array($property, $noDataProp) &&
				isset($data[$property])) {
				$setter = "set" . ucfirst($property);
				if (is_callable(array(
						$this,
						$setter
					))) {
					$value = $this->$setter($data[$property]);
				}
			}
		}
		return $data;
	}

	/**
	 * Checks if a given string is UTF8 encoded or not
	 *
	 * @param string $str
	 * @return boolean is utf8
	 */
	public function is_utf8 ($str)
	{
		$len = strlen($str);
		for ($i = 0; $i < $len; $i ++) {
			$c = ord($str[$i]);
			if ($c > 128) {
				if (($c > 247))
					return false;
				elseif ($c > 239)
					$bytes = 4;
				elseif ($c > 223)
					$bytes = 3;
				elseif ($c > 191)
					$bytes = 2;
				else
					return false;
				if (($i + $bytes) > $len)
					return false;
				while ($bytes > 1) {
					$i ++;
					$b = ord($str[$i]);
					if ($b < 128 || $b > 191)
						return false;
					$bytes --;
				}
			}
		}
		return true;
	}
}