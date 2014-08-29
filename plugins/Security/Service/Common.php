<?php
class Security_Service_Common {

	private static $instance = null;

    private $authAdapter = "Security_Adapter_PimcoreUser";
	private $cookieHash = "Sdsjdf4sdf5sFDsd";

	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		if (!$this->getUser()) {
			if (isset($_COOKIE['frontend_user'])) {
				$cookieData = unserialize($_COOKIE['frontend_user']);
				$userName = $cookieData['userName'];
				$userHash = $cookieData['hash'];

				$identity = User::getByName($userName);
				if ($identity) {
					$hash = $this->generateCookieHash($identity);
					if ($hash == $userHash) {
						$authAdapter = new Security_Adapter_Cookie($identity->getName(),null);
						$auth = Zend_Auth::getInstance();
						$auth->clearIdentity();
						$auth->authenticate($authAdapter);
					}
				}
			}
		}
	}

    public function setAuthAdapter($adapter) {
        $this->authAdapter = $adapter;
    }

	public function setCookieHash($hash) {
		$this->cookieHash = $hash;
	}

	public function getCookieHash() {
		return $this->cookieHash;
	}

	/**
	 * Retrieve user from cookie or session
	 *
	 * @return User
	 */
	public function initUser() {
		return $this->getUser();
	}

	/**
	 * Check user permissions for the current page
     * @todo: refactor this
	 */
	public function securityCheck($controller) {
        $action = $controller->getRequest()->getParam('action');
		$controllerName = $controller->getRequest()->getParam('controller');
		if(Zend_Auth::getInstance()->hasIdentity()) {
			// Check if the requested action is allowed for this user
			if(isset($controller->_securedActions)) {
				if(array_key_exists($action,$controller->_securedActions)) {
					if(!$this->getUser()->isAllowed($controller->_securedActions[$action])) {
						die('ACCESS DENIED! Nice try though. Don\'t you have anything better to do?');
					}
				}
			}
		} else {
			if((isset($controller->_securedActions) && array_key_exists($action, $controller->_securedActions)) || (isset($controller->_secure) && $controller->_secure == true)) {
				if (!Zend_Controller_Front::getInstance()->getRequest()->getParam('pimcore_editmode')) {
					if($controllerName != 'user' || !in_array($action,array("login","register","reset-password"))) {
                        $redirectSession = new Zend_Session_Namespace('loginRedirect');
						$redirectSession->url = $_SERVER['REQUEST_URI'];
						$redirectSession->controller = $controllerName;
						$redirectSession->action = $action;
						Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoUrl('/user/login', array());
					}
				}
			}
		}
	}

	public function authenticate($data) {
        $username = $data['username'];
        $password = $data['pw'];

        $authAdapter = new $this->authAdapter($username,$password);
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $result = $auth->authenticate($authAdapter);

        if($result->isValid()) {
			$hash = $this->generateCookieHash($this->getUser());
			$data = serialize(array(
				'userName'=>$username,
				'hash'=>$hash
			));
			setcookie('frontend_user',$data,time()+60*60*24*7,'/');

            return true;
        } else {
            return false;
        }
	}

	public function logout() {
		Zend_Auth::getInstance()->clearIdentity();
		setcookie('frontend_user',null,time()-60,'/');
	}

	public function getUser(){
		if (Zend_Auth::getInstance()->hasIdentity()) {
            return Zend_Auth::getInstance()->getIdentity();
        }
        return false;
	}

	private function generateCookieHash($user) {
		$hash = md5($user->getId() . $this->getCookieHash());
		$hash = sha1($hash);
		$hash = substr($hash,0,50);
		$hash = strrev($hash);

		return $hash;
	}
}
