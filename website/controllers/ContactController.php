<?php

/**
 * Class ContactController
 */
class ContactController extends Website_Controller_Action
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
	 *    This generates the actual contact page
	 *    Comment one of these actions to disable them
	 */
	public function defaultAction()
	{
		// Show and process the contact form
		$this->_contactForm();

		// Show the google maps
		$this->_googleMaps();
	}

	/**
	 *    Process the contact form
	 */
	protected function _contactForm()
	{
		$this->view->messages = $this->_helper->flashMessenger->getMessages();

		$form = SEBasic_FrontForm::getByName('contact');

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->getRequest()->getParams())) {
				// set the url (so they know wich webiste it is)
				$params = $form->getValues();
				$params['website'] = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();

				//sending the email
				$mail = new Website_Mail();
				$document = $this->view->inotherlang($this->config->email_contact);
				$mail->setDocument($document);
				$mail->setParams($params);
				$mail->send();

				// Informing the visitors
				$this->addFlashMessage(
					$this->view->translate("Bedankt voor uw bericht. We nemen zo snel mogelijk contact met u op!")
				);
				$this->redirect($this->document->getFullPath());
			}
		}
		$this->view->form = $form;

		if (count($this->view->messages) > 0) {
			$this->view->showContactForm = false;
		} else {
			$this->view->showContactForm = true;
		}
	}

	/**
	 *    Show the Google maps
	 */
	protected function _googleMaps()
	{
		$geo_long = $this->document->getProperty('geo_long');
		$geo_lat = $this->document->getProperty('geo_lat');

		if (is_null($geo_lat) || is_null($geo_long)) {
			$this->view->geo_lat = 0;
			$this->view->geo_long = 0;
		} else {
			// Do some magic on the coordinates
			$this->view->geo_lat = str_replace(',', '.', $geo_lat);
			$this->view->geo_long = str_replace(',', '.', $geo_long);
		}

		// Add Google maps script to header
		$this->view->headScript()->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false');
		$this->view->geo_address = $this->document->getProperty('geo_address');
		$this->view->showGoogleMaps = true;
	}
}