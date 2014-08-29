<?php

class Backend_UserController extends Website_Controller_Action {
	public $_secure = true;

	public function defaultAction() {
		if ($this->getUser()) {
			$this->redirect('/user/overview');
		}else {
			$this->redirect('/user/login');
		}
	}

	public function overviewAction() {
		$form = new Website_Form_Profile();
		$form->setAction('/user/overview');
		$user = $this->getUser();

		$data['firstname'] = $user->getFirstname();
		$data['lastname'] = $user->getLastname();
		$data['email'] = $user->getEmail();
		$form->populate($data);

		if($this->getRequest()->isPost()) {
			if($form->isValid($this->getRequest()->getParams())) {
				$allOk = true;
				if ($this->getParam('pw') && $this->getParam('pw2')) {
					if ($this->getParam('pw') != $this->getParam('pw2')) {
						$allOk = false;
						$el = $form->getElement('pw2');
						$el->addError('Passwords do not match');
					}
					$updatePw = true;
				}

				if ($allOk) {
					if ($updatePw) {
						$user->setPassword(Pimcore_Tool_Authentication::getPasswordHash($user->getUsername(), $this->getParam("pw")));
					}
					$user->setFirstname($this->getParam('firstname'));
					$user->setLastname($this->getParam('lastname'));
					$user->setEmail($this->getParam('email'));
					$user->save();

					$this->redirect('/');
				}
			}
		}
		$this->view->form = $form;
	}

    public function settingsAction() {

    }

	public function logoutAction() {
		$service = Security_Service_Common::getInstance();
		$service->logout();
		$this->redirect("/");
	}


	public function loginAction () {
		$this->setLayout('login');
		$form = new Website_Form_Login();
		$form->setAction('/user/login');

		if($this->getRequest()->isPost()) {
			if($form->isValid($this->getRequest()->getParams())) {

                $service = Security_Service_Common::getInstance();
				if(!$service->authenticate($form->getValues())) {
					// Add error
					$el = $form->getElement('pw');
					$el->addError('No valid username/password combination');
				} else {
					$redirectSession = new Zend_Session_Namespace('loginRedirect');
					if (isset($redirectSession->url)) {
						$this->redirect($redirectSession->url);
					}
                    $this->redirect('/');
                }
			}
		}
		$this->view->form = $form;
	}

    /**
     * Register a new user
     * @todo: move the creation of the user & member to service
     */
    public function registerAction() {
		$this->setLayout('login');
		// If registrations are disabled, redirect to home
		if (!isset($this->config->registration_enabled) || $this->config->registration_enabled == false) {
			$this->redirect('/');
		}

		$form = new Website_Form_Register();

		if($this->getRequest()->isPost()) {
			if($form->isValid($this->getRequest()->getParams())) {
				$allOk = true;
				$testUser = User::getByName($this->getParam('username'));
				if ($testUser) {
					$allOk = false;
					$el = $form->getElement('username');
					$el->addError('Username already taken');
				}

				if ($this->getParam('pw') != $this->getParam('pw2')) {
					$allOk = false;
					$el = $form->getElement('pw2');
					$el->addError('Passwords do not match');
				}

				if ($allOk) {
					$user = User::create(array(
						"parentId" => 3,
						"name" => $this->getParam("username"),
						"password" => Pimcore_Tool_Authentication::getPasswordHash($this->getParam("username"), $this->getParam("pw")),
						"active" => true,
						"email"=>$this->getParam('email'),
						'firstname'=>$this->getParam('firstname'),
						'lastname'=>$this->getParam('lastname')
					));

                    // Create member
                    $memberObject = new Website_Model_Members();
                    $memberObject->setUserId($user->getId());
                    $memberObject->save();

                    // Login
					$securityService = Security_Service_Common::getInstance();
                    $securityService->authenticate($form->getValues());

					$params = array(
						'name'=>$this->view->username($user),
						'url'=>$this->getAbsoluteUrl() . '/user/login',
						'username'=>$user->getName()
					);
					//sending the email
					$mail = new Pimcore_Mail();
					$mail->addTo($user->getEmail());
					$mail->setDocument('/_onderdelen/user_register');
					$mail->setParams($params);
					$mail->send();

                    $this->addFlashMessage($this->view->translate('Your account was successfully created. Have fun!'));
					$this->redirect("/user");
				}
			}
		}
		$this->view->form = $form;
	}

    /**
     * Reset the user password
     *
     * Sends an email with a hash if a valid username is provided
     *
     */
    public function resetPasswordAction() {
		$this->setLayout('login');
        $key = $this->getRequest()->getParam('key',false);

        if (!$key) {
            // Show form
            $form = new Website_Form_ResetPassword();
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getParams())) {
                    $user = User::getByName($form->getValue('username'));
                    if ($user) {
                        $hash = md5(strrev($user->getName() . 'CentroTool'));

                        // Send mail
                        $params = array(
                            'name'=>$this->view->username($user),
                            'url'=>$this->getAbsoluteUrl() . '/user/reset-password?key=' . $hash
                        );

                        $mail = new Pimcore_Mail();
                        $mail->addTo($user->getEmail());
                        $mail->setDocument('/_onderdelen/reset_pw');
                        $mail->setParams($params);
                        $mail->send();

                        // Set flashmsg
                        $this->addFlashMessage($this->view->translate('An email has been send to ' . $user->getEmail() . '. Please follow the link in the email to reset your password'));

                        // Redirect to self
                        $this->redirect('/user/reset-password');

                    } else {
                        // Show error
                        $form->getElement('username')->addError($this->view->translate('No account found with this username'));
                    }
                }
            }

        } else {
            // Process key
            $member = new Website_Model_Members();
            $member = $member->getByReset_pw_token($key);
            $this->view->key = $key;
            if ($member) {
                $form = new Website_Form_UpdatePassword();
                $form->populate(array('key'=>$key));
                $this->view->form = $form;

                if ($this->getRequest()->isPost()) {
                    if ($form->isValid($this->getRequest()->getParams())) {
                        if ($this->getParam('pw') == $this->getParam('pw2')) {
                            // Update user password
                            $user = User::getById($member->getUserId());
                            $user->setPassword(Pimcore_Tool_Authentication::getPasswordHash($user->getName(), $this->getParam("pw")));
                            $user->save();

                            // Reset token (so it cannot be used again)
                            $member->setReset_pw_token("");
                            $member->save();

                            // Notify user of our great success
                            $this->addFlashMessage('Your password has been changed. Please login now.');

                            // Redirect them to the login page
                            $this->redirect('/user/login');
                        } else {
                            $form->getElement('pw2')->addError($this->view->translate('Passwords do not match!'));
                        }
                    }
                }
            } else {
                // No valid user found for the given key
                $this->addFlashMessage($this->view->translate('The key you provided is not valid. Have you used it before?'));
                $this->redirect('/user/reset-password');
            }
        }
    }


}
