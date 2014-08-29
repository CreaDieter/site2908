<?php

/**
 * Class EmailController
 */
class EmailController extends Website_Controller_Action
{

	/**
	 *    Default action for viewing and rendering emails
	 *    Use this controller/action in your email documents in the backoffice
	 */
	public function defaultAction()
	{

		// Set specific email layout
		$this->enableLayout();
		if (Site::isSiteRequest()) {
			$siteKey = Site::getCurrentSite()->getRootDocument()->getKey();
			$layoutName = 'email_' . $siteKey;
		} else {
			$layoutName = 'email';
		}
		$this->setLayout($layoutName);


		// Demo code for sending emails

//		//dynamic parameters
//		$params = array('name' => 'tester');
//
//		//sending the email
//		$mail = new Website_Mail();
//		$mail->addTo('test@studioemma.be');
//		$document = $this->inotherlang($this->config->email_contact));
//		$mail->setDocument($document);
//		$mail->setParams($params);
//		$mail->send();
	}
}
