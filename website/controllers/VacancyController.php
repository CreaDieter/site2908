<?php

/**
 * Class VacancyController
 */
class VacancyController extends Website_Controller_Action
{
	/**
	 * This is called for all actions in this controller
	 */
	public function init()
	{
		parent::init();
		$this->enableLayout();
		$this->setLayoutForSite();
	}

	/**
	 *    Generate a list of vacancy objects for this subsite, in this language
	 */
	public function overviewAction()
	{
		$subSiteKey = '*';
		if (Site::isSiteRequest()) {
			$subSiteKey = Site::getCurrentSite()->getRootDocument()->getKey();
		}
		$language = $this->language;

		// Retrieve items from object list
		$newsList = new Object_Vacancy_List();
		$newsList->setOrderKey("date");
		$newsList->setOrder("desc");
		$newsList->setLocale($language);
		if (Site::isSiteRequest()) {
			$newsList->setCondition('subsites LIKE ? AND title != ""', '%' . $subSiteKey . '%');
		}

		$paginator = Zend_Paginator::factory($newsList);
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$paginator->setItemCountPerPage(10);
		$this->view->paginator = $paginator;
	}

	/**
	 *    Show the vacancy detail page
	 */
	public function showAction()
	{
		$id = $this->getParam('id', null);

		if ($id) {
			$item = Object_Vacancy::getById($id);
			if ($item) {
				$this->view->item = $item;
			} else {
				throw new Exception('No vacancy item found with this ID: ' . $id);
			}
		} else {
			throw new Exception('No event vacancy found with this ID: ' . $id);
		}

		if($item->display_form) {
			$this->_applyForm($item);
		}
	}

	private function _applyForm($item) {
		$this->view->showApplyForm = true;

		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		$form = new Website_Form_Apply();

		// form has been submitted
		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
			$mailContent = $form->getFormContent();

			// the mail params
			$mailParams = array(
				'vacancyTitle' => $item->getTitle($this->language),
				'name' => $form->getValue('name'),
				'mailContent' => $mailContent
			);

			// get the files
			$upload = new Zend_File_Transfer();
			$files = $upload->getFileInfo();
			$upload->receive();


			//sending the email to the admin
			$mail = new Website_Mail();
			$document = $this->view->inotherlang(Website_Config::getWebsiteConfig()->email_vacancy_admin);
			$mail->setDocument($document);
			$mail->setParams($mailParams);
			$mail->setFrom($form->getValue('email'));

			// attach the files
			foreach ($files as $file => $info) {
				if ($form->{$file} && $form->{$file}->getFileName() && file_exists($form->{$file}->getFileName())) {
					$at = $mail->createAttachment(file_get_contents($form->{$file}->getFileName()));
					$at->filename = $file . '_' . $info['name'];
				}
			}

			$mail->send();

			//sending the email to the user
			$mail = new Website_Mail();
			$document = $this->view->inotherlang(Website_Config::getWebsiteConfig()->email_vacancy_user);
			$mail->setDocument($document);
			$mail->setParams($mailParams);
			$mail->send();

			// Informing the visitors
			$this->addFlashMessage($this->view->translate("Uw sollicitatie werd verstuurd!"));
			$url = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getRequestUri();
			$this->getHelper("Redirector")->gotoUrl($url);
		}

		$this->view->form = $form;
	}
}