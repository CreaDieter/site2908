<?php

/**
 * Class ContentController
 */
class ProductController extends Website_Controller_Action
{

	public function init()
	{
		parent::init();
		$this->enableLayout();
		$this->setLayoutForSite();
	}

	/**
	 * List the child products (documents)
	 *
	 * @throws Zend_Paginator_Exception
	 */
	public function overviewAction() {
		// get the children
		$children = $this->document->getChilds();

		// paginate them
		$paginator = Zend_Paginator::factory($children);
		$paginator->setCurrentPageNumber($this->_getParam('page','1'));
		$paginator->setItemCountPerPage(10);
		$this->view->children = $paginator;
	}

	/**
	 * Display the detail of a product
	 */
	public function showAction() {
		// display offer button?
		if (Website_Config::getWebsiteConfig()->product_offer) {
			$this->view->productId = $this->document->getId();
			$this->view->productKey = $this->document->getKey();
			$this->view->canRequestOffer = true;
		}

		$this->view->parent = $this->document->getParent();

		// add the js-files for the image gallery
		$this->view->headScript()->appendFile('/js/jquery.cycle2.min.js');
		$this->view->headScript()->appendFile('/js/jquery.cycle2.carousel.min.js');
		$this->view->headScript()->appendFile('/js/jquery.nyroModal.custom.min.js');
	}

	public function offerAction() {
		if (!Website_Config::getWebsiteConfig()->product_offer) {
			throw new Exception('product offer is not allowed');
		}

		// get the product title
		$id = $this->getRequest()->getParam('id');
		$productTitle = $this->getProductTitleById($id);

		// the form
		$this->_offerForm($productTitle);
	}

	/**
	 * Get the product title by a given id
	 * also sets its to the view
	 *
	 * @param $id
	 * @return string
	 */
	private function getProductTitleById($id) {
		$productTitle = '';
		if (is_numeric($id)) {
			try {
				$document = Document::getById($id);
			} catch (Exception $e) {
				$document = null;
			}
			if ($document instanceof Document_Page) {
				$productTitle = $document->getTitle();
				$this->view->product = $document;
			}
		}
		$this->view->productTitle = $productTitle;
		return $productTitle;
	}

	/**
	 * Display and manage the offer form
	 *
	 * @param $productTitle
	 * @throws Exception
	 * @throws Zend_Form_Exception
	 * @throws Zend_Mail_Exception
	 */
	private function _offerForm($productTitle) {
		$form = new Website_Form_Offer();

		// form has been submitted
		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
			// build the html for the mail
			$params = $form->getValues();
			$mailContent = '';
			foreach ($params as $field => $value) {
				$el = $form->getElement($field);
				if ($el) {
					$mailContent .= '<strong>' . $el->getLabel() . ':</strong> ' . $value . '<br>';
				}
			}
			// the mail params
			$mailParams = array(
				'productTitle' => $productTitle,
				'mailContent' => $mailContent
			);

			//sending the email
			$mail = new Website_Mail();
			$document = $this->view->inotherlang($this->config->email_offer);
			$mail->setDocument($document);
			$mail->setParams($mailParams);
			$mail->setFrom($form->getValue('email'));
			$mail->send();

			// Informing the visitors
			$this->addFlashMessage($this->view->translate("Bedankt voor uw offerteaanvraag. We nemen zo snel mogelijk contact met u op!"));
			$url = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()->getRequestUri();
			$this->getHelper("Redirector")->gotoUrl($url);
		}

		$this->view->form = $form;
	}


}
