<?php

class Security_Adapter_Cookie extends Security_Adapter_Abstract {

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot
     *                                     be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $identity = User::getByName($this->username);
        if($identity) {
			if ($identity->isActive()) {
				return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
			}
        }

        return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
    }
}