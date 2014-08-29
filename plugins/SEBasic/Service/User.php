<?php

class SEBasic_Service_User {


	/**
	 * Create a backoffice user that has correct permissions
	 *
	 * @param $params
	 * @return bool|string
	 */
	public function createBackofficeUser($params) {
		// create the user
		try {
			$user = $this->createUser($params);
		} catch (Exception $e) {
			return $e->getMessage();
		}

		// set workspaces & permissions
		$this->setUserWorkspaces($user);
		$this->setUserPermissions($user);

		$user->save();

		return true;
	}

	/**
	 * Create the user
	 *
	 * @param $params
	 * @return User
	 * @throws Exception
	 */
	private function createUser($params) {
		// the params
		$userParams = array(
			"parentId" => 0,
			"username" => '',
			"password" => '',
			"hasCredentials" => true,
			"active" => true,
			"language" => reset(Pimcore_Tool::getValidLanguages())
		);

		// check if we have all needed variables
		foreach (array('username', 'password', 'email') as $param) {
			if (isset($params[$param]) && !empty($params[$param])) {
				$userParams[$param] = $params[$param];
			} else {
				throw new Exception("Parameter $param is missing");
			}
		}
		// unset the password from the variables
		$password = $userParams['password'];
		$userParams['password'] = '';

		// does the user already exist?
		if (User::getByName($userParams['username'])) {
			throw new Exception('User already exists');
		}

		// let's create the new user
		$user = User::create($userParams);

		// hash the password and set it for the user
		$user->setPassword(Pimcore_Tool_Authentication::getPasswordHash($user->getName(), $password));
		$user->save();

		$this->setUserWorkspaces($user);
		$this->setUserPermissions($user);

		return $user;
	}

	/**
	 * Set the workspaces for the user
	 *
	 * @param User $user
	 */
	private function setUserWorkspaces(User &$user) {
		$properties = array(
			'document' => array(
				"list" => true,
				"view" => true,
				"save" => true,
				"publish" => true,
				"unpublish" => true,
				"delete" => true,
				"rename" => true,
				"create" => true,
				"settings" => true,
				"versions" => true,
				"properties" => true
			),
			'asset' => array(
				"list" => true,
				"view" => true,
				"publish" => true,
				"delete" => true,
				"rename" => true,
				"create" => true,
				"settings" => true,
				"versions" => true,
				"properties" => true
			),
			'object' => array(
				"list" => true,
				"view" => true,
				"save" => true,
				"publish" => true,
				"unpublish" => true,
				"delete" => true,
				"rename" => true,
				"create" => true,
				"settings" => true,
				"versions" => true,
				"properties" => true
			)
		);

		// set the workspaces
		foreach (array('document', 'asset', 'object') as $type) {
			$classname = 'User_Workspace_' . ucfirst($type);
			/** @var User_Workspace_Document $workspaceClass */
			$workspaceClass = new $classname();

			// set the workspaces properties
			$workspaceClass->setValues(isset($properties[$type]) ? $properties[$type] : array());

			// get the root document
			$element = Element_Service::getElementByPath($type, '/');

			// set the workspace path
			$workspaceClass->setCid($element->getId());
			$workspaceClass->setCpath($element->getFullPath());

			// set the workspace user
			$workspaceClass->setUserId($user->getId());

			// assign to user
			$user->{"setWorkspaces" . ucfirst($type)}(array($workspaceClass));
		}
		$user->save();
	}

	/**
	 * Set the permissions for the user
	 *
	 * @param User $user
	 */
	private function setUserPermissions(User &$user) {
		// set the permissions
		$permissions = array(
			"assets",
			"clear_cache",
			"documents",
			"document_style_editor",
			"objects",
			"qr_codes",
			"reports",
			"seo_document_editor",
			"translations",
		);
		$user->setPermissions($permissions);
		$user->save();
	}


}