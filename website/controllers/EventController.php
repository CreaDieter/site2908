<?php

/**
 * Class EventController
 */
class EventController extends Website_Controller_Action
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
	 *    Generate a list of news objects for this subsite, in this language
	 */
	public function overviewAction()
	{
		$subSiteKey = '*';
		if (Site::isSiteRequest()) {
			$subSiteKey = Site::getCurrentSite()->getRootDocument()->getKey();
		}
		$language = $this->language;

		// Retrieve items from object list
		$newsList = new Object_Event_List();
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
	 *    Show the event detail page
	 */
	public function showAction()
	{
		// manually build the navigation if this action is accessed from a static route
		if (!$this->document instanceof Document_Page && Website_Config::getWebsiteConfig()->event_document instanceof Document_Page) {
			$this->buildNavigation(Website_Config::getWebsiteConfig()->event_document);
		}

		$id = $this->getParam('id', null);

		if ($id) {
			$item = Object_Event::getById($id);
			if ($item) {
				$this->view->item = $item;
			} else {
				throw new Exception('No event item found with this ID: ' . $id);
			}
		} else {
			throw new Exception('No event item found with this ID: ' . $id);
		}

		// display a form?
		if($item->display_form) {
			$this->_eventForm($item);
		}

	}

	/**
	 * @param Object_Event $item
	 * @throws Zend_Form_Exception
	 */
	private function _eventForm($item) {
		$this->view->showEventForm = true;

		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		$form = new Website_Form_Event();

		// form has been submitted
		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
			$mailContent = $form->getFormContent();

			// the mail params
			$mailParams = array(
				'eventTitle' => $item->getTitle($this->language),
				'name' => $form->getValue('name'),
				'mailContent' => $mailContent
			);

			//sending the email to the admin
			$mail = new Website_Mail();
			$document = $this->view->inotherlang(Website_Config::getWebsiteConfig()->email_event_admin);
			$mail->setDocument($document);
			$mail->setParams($mailParams);
			$mail->setFrom($form->getValue('email'));
			$mail->send();

			//sending the email to the user
			$mail = new Website_Mail();
			$document = $this->view->inotherlang(Website_Config::getWebsiteConfig()->email_event_user);
			$mail->setDocument($document);
			$mail->setParams($mailParams);
			$mail->send();

			// Informing the visitors
			$this->addFlashMessage($this->view->translate("Bedankt voor uw inschrijving. Tot dan!"));
			$url = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getRequestUri();
			$this->getHelper("Redirector")->gotoUrl($url);
		}

		$this->view->form = $form;
	}
}