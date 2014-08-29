<?php

abstract class Security_Adapter_Abstract implements Zend_Auth_Adapter_Interface {

    protected $username;
    protected $password;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

}