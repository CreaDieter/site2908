<?php

/**
 * Class ContactController
 */
class NewsletterController extends Website_Controller_Action {

	/**
	 * This is called for all actions in this controller
	 */
	public function init() {
		parent::init();
		$this->enableLayout();
		$this->setLayoutForSite();
	}


	public function defaultAction() {
		$this->setLayout('newsletter');
	}

	/**
	 * This action returns the details of a given object as json
	 * it returns the title, the content and an image
	 */
	public function ajaxGetObjectContentAction() {
		$this->disableViewAutoRender();

		$result = $this->getObjectData($this->getRequest()->getParam('id'));

		$this->getHelper('json')->sendJson($result);
	}

	/**
	 * Get the data of an object of given id, class and with given elements
	 *
	 * @param        $id
	 * @param string $type
	 * @param array  $elements
	 * @param        $staticroute
	 * @return array
	 */
	private function getObjectData(
		$id,
		$type = 'Object_Abstract',
		$elements = ['title', 'content'],
		$staticroute = null
	) {
		$result = array();

		// get the object by the given id
		try {
			$object = $type::getById((int)$id);
		} catch (Exception $e) {
			$object = null;
		}
		// no id? try with the path
		if (!$object instanceof $type) {
			try {
				$object = $type::getByPath($id);
			} catch (Exception $e) {
				$object = null;
			}
		}

		if ($object instanceof $type) {
			// get the elements
			foreach ($elements as $element) {
				try {
					$result[$element] = $object->{'get' . ucfirst(strtolower($element))}($this->language);
				} catch (Exception $e) {
					$result[$element] = '';
				}
			}

			// need a url?
			if (is_string($staticroute) && strlen($staticroute) > 0 && Staticroute::getByName($staticroute)) {
				$result['url'] = $this->getUrl($object, $staticroute);
			}
		}

		return $result;
	}

	/**
	 * Get the url of an object
	 * this is the default format, using the id and the key
	 *
	 * @param $object
	 * @param $staticroute
	 * @return string
	 */
	private function getUrl($object, $staticroute) {
		try {
			$url = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->view->url(
					array('language' => $this->language, 'id' => $object->getId(), 'key' => $object->getKey()),
					$staticroute,
					true
				);
		} catch (Exception $e) {
			$url = '';
		}
		return $url;
	}

	/**
	 * Get the detail of a vacancy object
	 */
	public function ajaxGetVacancyDetailAction() {
		$this->disableViewAutoRender();

		$result = $this->getObjectData(
			$this->getRequest()->getParam('id'),
			'Object_Vacancy',
			['title', 'content'],
			'vacancy_detail'
		);

		$this->getHelper('json')->sendJson($result);
	}

	/**
	 * Get the teaser of a vacancy object
	 */
	public function ajaxGetVacancyTeaserAction() {
		$this->disableViewAutoRender();

		$result = $this->getObjectData(
			$this->getRequest()->getParam('id'),
			'Object_Vacancy',
			['title', 'content'],
			'vacancy_detail'
		);
		// truncate
		if (isset($result['content'])) {
			$result['content'] = substr($result['content'], 0, 247) . '...';
		}

		$this->getHelper('json')->sendJson($result);
	}

	/**
	 * Get a the title and the url of a given vacancy
	 */
	public function ajaxGetVacancyTitleAction() {
		$this->disableViewAutoRender();

		$result = $this->getObjectData(
			$this->getRequest()->getParam('id'),
			'Object_Vacancy',
			['title'],
			'vacancy_detail'
		);

		$this->getHelper('json')->sendJson($result);
	}

	/**
	 * Get the details of a given event
	 */
	public function ajaxGetEventDetailAction() {
		$this->disableViewAutoRender();

		$result = $this->getObjectData(
			$this->getRequest()->getParam('id'),
			'Object_Event',
			['title', 'content','date'],
			'event_detail'
		);

		if (isset($result['date']) && $result['date'] instanceof Pimcore_Date) {
			$date = $result['date'];
			$result['date'] = $date->toString("dd.MM.Y");
		}

		$this->getHelper('json')->sendJson($result);
	}

	/**
	 * Get the teaser of an event object
	 */
	public function ajaxGetEventTeaserAction() {
		$this->disableViewAutoRender();

		$result = $this->getObjectData(
			$this->getRequest()->getParam('id'),
			'Object_Event',
			['title', 'content','date'],
			'event_detail'
		);
		// truncate
		if (isset($result['content'])) {
			$result['content'] = substr($result['content'], 0, 247) . '...';
		}
		if (isset($result['date']) && $result['date'] instanceof Pimcore_Date) {
			$date = $result['date'];
			$result['date'] = $date->toString("dd.MM.Y");
		}

		$this->getHelper('json')->sendJson($result);
	}

	/**
	 * Get a the title and the url of a given event
	 */
	public function ajaxGetEventTitleAction() {
		$this->disableViewAutoRender();

		$result = $this->getObjectData(
			$this->getRequest()->getParam('id'),
			'Object_event',
			['title','date'],
			'event_detail'
		);
		if (isset($result['date']) && $result['date'] instanceof Pimcore_Date) {
			$date = $result['date'];
			$result['title'] = $date->toString("dd.MM.Y") . ' - ' . $result['title'];
		}

		$this->getHelper('json')->sendJson($result);
	}

	/**
	 * Get the details of a given news item
	 */
	public function ajaxGetNewsDetailAction() {
		$this->disableViewAutoRender();

		$result = $this->getObjectData(
			$this->getRequest()->getParam('id'),
			'Object_News',
			['title', 'content','date'],
			'news_detail'
		);

		if (isset($result['date']) && $result['date'] instanceof Pimcore_Date) {
			$date = $result['date'];
			$result['date'] = $date->toString("dd.MM.Y");
		}

		$this->getHelper('json')->sendJson($result);
	}

	/**
	 * Get the teaser of an event object
	 */
	public function ajaxGetNewsTeaserAction() {
		$this->disableViewAutoRender();

		$result = $this->getObjectData(
			$this->getRequest()->getParam('id'),
			'Object_News',
			['title', 'content','date'],
			'news_detail'
		);
		// truncate
		if (isset($result['content'])) {
			$result['content'] = substr($result['content'], 0, 247) . '...';
		}
		if (isset($result['date']) && $result['date'] instanceof Pimcore_Date) {
			$date = $result['date'];
			$result['date'] = $date->toString("dd.MM.Y");
		}

		$this->getHelper('json')->sendJson($result);
	}

	/**
	 * Get a the title and the url of a given event
	 */
	public function ajaxGetNewsTitleAction() {
		$this->disableViewAutoRender();

		$result = $this->getObjectData(
			$this->getRequest()->getParam('id'),
			'Object_news',
			['title','date'],
			'news_detail'
		);
		if (isset($result['date']) && $result['date'] instanceof Pimcore_Date) {
			$date = $result['date'];
			$result['title'] = $date->toString("dd.MM.Y") . ' - ' . $result['title'];
		}

		$this->getHelper('json')->sendJson($result);
	}

	/**
	 * This action returns the details of a given document as json
	 * it returns the title, the content and an image
	 */
	public function ajaxGetDocumentContentAction() {

	}

}