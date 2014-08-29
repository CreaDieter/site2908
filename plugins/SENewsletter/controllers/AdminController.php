<?php

class SENewsletter_AdminController extends Pimcore_Controller_Action_Admin {

	/**
	 * Send out the newsletter
	 */
	public function sendNewsletterAction() {
		// send the newsletter immediate?
		$sendNow = (bool)$this->_getParam('sendNow');

		$result = false;
		$msg = '';

		$id = $this->getRequest()->getParam('id');
		if ($id && is_numeric($id)) {
			// get the newsletter
			$email = Document_Email::getById($this->getRequest()->getParam('id'));

			if ($email instanceof Document_Email) {
				$subject = $email->getSubject();
				if (!$subject) {
					$msg = 'subject not set';
				} else {
					// render the html
					header('Content-Type: text/html; charset=utf-8'); // for testing purposes
					$html = Document_Service::render($email, array(), true);

					// get the sender
					$fromEmail = $email->getFrom();
					$fromName = Website_Config::getWebsiteConfig()->enews_from_name;
					if (!$fromEmail) $fromEmail = Website_Config::getWebsiteConfig()->enews_from_email;

					if (!$fromEmail) {
						$msg = 'From email not set';
					} else {
						if (!$fromName) $fromName = $fromEmail;
						// get the lists
						if ($this->_getParam('lists')) {
							$lists = explode('-',$this->_getParam('lists'));
							if (count($lists) > 0) {
								$valid_lists = $this->getLists(true);
								$allGood = true;
								foreach ($lists as $list) {
									if (!array_key_exists($list,$valid_lists)) {
										$msg = 'One of the selected lists is not valid';
										$allGood = false;
										break;
									}
								}

								if ($allGood) {
									// send to e-news
									$apiResult = $this->sendToEnews($html, $subject, $fromName, $fromEmail, $sendNow, $lists);
									if ($apiResult === true) $result = true;
									else $msg = $apiResult;
								}
							} else {
								$msg = 'No lists selected';
							}
						}
					}
				}
			} else {
				$msg = 'newsletter document not found';
			}
		} else {
			$msg = 'given id should be numeric';
		}

		$this->_helper->json(array("success" => $result, "msg" => $msg));
	}

	/**
	 * Get the enews lists of the user with given data
	 */
	public function getListsAction() {
		$fields = $this->getLists();
		$success = (bool)(count($fields) > 0);

		$this->_helper->json(array("success" => $success, "fields" => $fields));
	}

	/**
	 * Get an array of the lists that the user can access in the enews system
	 *
	 * @param bool $setArrayKeys set the list-id as the array key (extJS can't handle those IDs...)
	 * @return array
	 */
	private function getLists($setArrayKeys = false) {
		// get url
		$url = Website_Config::getWebsiteConfig()->enews_url;
		if (!$url) {
			return 'enews api url not set in website config';
		}

		// get key or credentials
		$key = Website_Config::getWebsiteConfig()->enews_api_key;
		if (!$key) {
			// no key found? check if username/password is set (old active campaign version)
			if (Website_Config::getWebsiteConfig()->enews_username && Website_Config::getWebsiteConfig()->enews_password) {
				// password & username found! get the lists
				$lists = $this->getListsUsingCredentials(
					$url,
					Website_Config::getWebsiteConfig()->enews_username,
					Website_Config::getWebsiteConfig()->enews_password
				);
			}
		} else {
			// we have an api key! get the lists
			$lists = $this->getListsUsingApiKey($url, $key);
		}

		$fields = [];
		// yeah, we have some lists!
		if (isset($lists) && (int)$lists->success == 1) {
			$arr = get_object_vars($lists);
			foreach ($arr as $key => $value) {
				/*
				 * Numeric key = list
				 * Non-numeric key != list (e.g. success, http_code, result_code...)
				 * -> so only add if the key is numeric
				 */
				if (is_numeric($key) && $value instanceof stdClass) {
					$list = get_object_vars($value);
					$field = [
						'boxLabel' => $list['name'],
						'name' => 'enews_list',
						'inputValue'  => $list['id'],
						'id' => $list['stringid'] . $list['id']
					];

					if ($setArrayKeys) $fields[$list['id']] = $field;
					else $fields[] = $field;
				}
			}
		}

		return $fields;
	}

	/**
	 * Get the enews lists that the user has access to using the api key
	 *
	 * @param $url
	 * @param $key
	 * @return string
	 */
	private function getListsUsingApiKey($url,$key) {
		$ac = new ActiveCampaign($url, $key);
		if (!$ac->credentials_test()) return ('Access denied: Invalid credentials'); // only works when using api key
		return $this->getListsFromEnewsSystem($ac);
	}

	/**
	 * Get the enews lists that the user has access to using username & password
	 *
	 * @param $url
	 * @param $username
	 * @param $password
	 * @return mixed
	 */
	private function getListsUsingCredentials($url,$username,$password) {
		$ac = new ActiveCampaign($url, false, $username, $password);
		return $this->getListsFromEnewsSystem($ac);
	}

	/**
	 * Get the enews lists that the user has access to
	 *
	 * @param ActiveCampaign $ac
	 * @return mixed
	 */
	private function getListsFromEnewsSystem(ActiveCampaign $ac) {
		$lists = $ac->api('list/list',['ids'=>'all']);
		return $lists;
	}

	/**
	 * Send the given html content to the e-news system
	 *
	 * @param $html
	 * @return bool|string
	 */
	private function sendToEnews($html, $subject, $from_name, $from_email, $sendNow, $lists) {
		// get url
		$url = Website_Config::getWebsiteConfig()->enews_url;
		if (!$url) {
			return 'enews api url not set in website config';
		}

		// get key or credentials
		$key = Website_Config::getWebsiteConfig()->enews_api_key;
		if (!$key) {
			// no key found? check if username/password is set (old active campaign version)
			if (!Website_Config::getWebsiteConfig()->enews_username || !Website_Config::getWebsiteConfig()->enews_password) {
				// can't connect without credentials or key
				return ('No username/password/api-key found in website config');
			}
			// password & username found! send the mail to the enews
			return $this->sendToEnewsUsingCredentials(
				$html,
				$subject,
				$from_name,
				$from_email,
				$url,
				Website_Config::getWebsiteConfig()->enews_username,
				Website_Config::getWebsiteConfig()->enews_password,
				$sendNow,
				$lists
			);
		} else {
			// we have an api key! send the mail to the enews
			return $this->sendToEnewsUsingApiKey(
				$html,
				$subject,
				$from_name,
				$from_email,
				$url,
				$key,
				$sendNow,
				$lists
			);
		}
	}

	/**
	 * send to the enews system using an api key
	 *
	 * @param $html
	 * @param $subject
	 * @param $from_name
	 * @param $from_email
	 * @param $url
	 * @param $key
	 * @return bool|string
	 */
	private function sendToEnewsUsingApiKey($html, $subject, $from_name, $from_email, $url, $key, $sendNow, $lists) {
		$ac = new ActiveCampaign($url, $key);
		if (!$ac->credentials_test()) return ('Access denied: Invalid credentials'); // only works when using api key
		return $this->createNewCampaign($ac, $html, $subject, $from_name, $from_email, $sendNow, $lists);
	}

	/**
	 * Send to the enews system using username and password
	 *
	 * @param $html
	 * @param $subject
	 * @param $from_name
	 * @param $from_email
	 * @param $url
	 * @param $username
	 * @param $password
	 * @return bool|string
	 */
	private function sendToEnewsUsingCredentials($html, $subject, $from_name, $from_email, $url, $username, $password, $sendNow, $lists) {
		$ac = new ActiveCampaign($url, false, $username, $password);
		return $this->createNewCampaign($ac, $html, $subject, $from_name, $from_email, $sendNow, $lists);
	}

	/**
	 * Create a new campaign in Active Campaign
	 *
	 * @param ActiveCampaign $ac
	 * @param                $html
	 * @param                $subject
	 * @param                $from_name
	 * @param                $from_email
	 * @param                $sendNow
	 * @param                $lists
	 * @return bool|string
	 */
	private function createNewCampaign(ActiveCampaign $ac, $html, $subject, $from_name, $from_email, $sendNow, $lists) {
		// first, we need to create a message
		$messageResult = $this->createNewMessage($ac, $html, $subject, $from_name, $from_email, $lists);

		// returns id
		if (!is_numeric($messageResult)) {
			return $messageResult;
		}

		// now create new campaign with the new message
		$post_data_campaign = [
			"type"                => "single",
			"name"                => $subject,
			"sdate"               => "2013-07-01 00:00:00",
			"status"              => (int)$sendNow, // 1 = scheduled | 0 = draft
			"public"              => 1,
			"tracklinks"          => "all",
			"trackreads"          => 1,
			"htmlunsub"           => 1,
			"m[{$messageResult}]" => 100
		];

		foreach ($lists as $list) {
			$post_data_campaign["p[$list]"] = $list;
		}

		$campaign_create = $ac->api("campaign/create", $post_data_campaign);

		if (!(int)$campaign_create->success) {
			return 'Error while creating campaign';
		}

		return true;
	}

	/**
	 * Create a new message in active campaign
	 *
	 * @param ActiveCampaign $ac
	 * @param                $html
	 * @param                $subject
	 * @param                $from_name
	 * @param                $from_email
	 * @return int|string id if success, error msg if error
	 */
	private function createNewMessage(ActiveCampaign $ac, $html, $subject, $from_name, $from_email, $lists) {
		$post_data_message = [
			'format'          => 'mime',
			'subject'         => $subject,
			'fromemail'       => $from_email,
			'fromname'        => $from_name,
			'charset'         => 'utf-8',
			'htmlconstructor' => 'editor',
			'html'            => $html
		];

		foreach ($lists as $list) {
			$post_data_message["p[$list]"] = $list;
		}

		// create the message
		$message_add = $ac->api('message/add', $post_data_message);

		if (!(int)$message_add->success) {
			return ("Adding email message failed: {$message_add->error}");
		} else {
			return (int)$message_add->id;
		}
	}
}
