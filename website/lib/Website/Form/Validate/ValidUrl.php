<?php
class Website_Form_Validate_ValidUrl extends Zend_Validate_Abstract
{

	/*
	 * source:
	 * http://www.rondobley.com/2011/09/24/how-to-validate-a-url-with-a-scheme-and-hostname-in-zend-framework/
	 */

	const INVALID_URL = 'invalid';


	protected $_messageTemplates = array(
		self::INVALID_URL => "The url %value% is not valid (make sure it starts with http(s)://)."
	);

	public function isValid($value)
	{

		if (!is_string($value)) {
			$this->_error(self::INVALID_URL);
			return false;
		}

		$this->_setValue($value);
		//get a Zend_Uri_Http object for our URL, this will only accept http(s) schemes
		try {
			$uriHttp = Zend_Uri_Http::fromString($value);
		} catch (Zend_Uri_Exception $e) {
			$this->_error(self::INVALID_URL);
			return false;
		}

		//if we have a valid URI then we check the hostname for valid TLDs, and not local urls
		$hostnameValidator = new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_DNS); //do not allow local hostnames, this is the default

		if (!$hostnameValidator->isValid($uriHttp->getHost())) {
			// also allow local hostnames (because .jens could be a valid TLD in the near future)
			$hostnameValidator = new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_LOCAL);
			if (!$hostnameValidator->isValid($uriHttp->getHost())) {
				$this->_error(self::INVALID_URL);
				return false;
			}

		}
		return true;
	}
}