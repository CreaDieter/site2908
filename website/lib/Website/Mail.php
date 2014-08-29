<?php

class Website_Mail extends Pimcore_Mail {


	/**
	 * Try to send the mail using Pimcore_Mail
	 * If it fails, still add it to the database, but prepend 'FAILED' to the subject
	 * so we don't lose any mails when something went wrong
	 *
	 * @param null $transport
	 * @return Pimcore_Mail
	 * @throws Zend_Mail_Transport_Exception
	 * @throws Zend_Mail_Exception
	 * @throws Exception
	 */
	public function send($transport = null) {
		// send the mail
		try {
			parent::send($transport);
		} catch (Exception $e) {
			$this->setSubject('FAILED - ' . $this->getSubjectRendered());
			try {
				Pimcore_Helper_Mail::logEmail($this);
			} catch (Exception $e) {
				Logger::emerg("Couldn't log Email");
				return false;
			}
		}

		return true;
	}

}