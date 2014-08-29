<?php

class Security_Adapter_Model extends Security_Adapter_Abstract{

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot
     *                                     be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $members = Object_Member::getByUsername($this->username);
        if(count($members) == 1) {
            /**
             * @var $members Object_Member_List
             */
            $member = $members->current();

            if($member->getPassword() == md5($this->password)) {
                return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $member->getId());
            }

        }

        return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
    }
}