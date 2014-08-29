<?php

class Website_Paginator_Adapter_Object extends Zend_Paginator_Adapter_DbSelect
{

	public $_model;

	/**
	 * Constructor.
	 *
	 * @param Zend_Db_Select $select
	 *        	The select query
	 * @param $model The
	 *        	current model
	 */
	public function __construct (Zend_Db_Select $select, $model)
	{
		$this->_select = $select;
		$this->_model = $model;
	}

	/**
	 * Returns an array of items for a page.
	 *
	 * @param integer $offset
	 *        	Page offset
	 * @param integer $itemCountPerPage
	 *        	Number of items per page
	 * @return array
	 */
	public function getItems ($offset, $itemCountPerPage)
	{
		$this->_select->limit($itemCountPerPage, $offset);
		
		$rows = $this->_select->getAdapter()->fetchAll($this->_select);
		
		$list = array();
		
		$class = get_class($this->_model);
		foreach ($rows as $row) {
			if (is_object($row)) {
				$row = $row->toArray();
			}
			$obj = new $class();
			$obj->setData($row);
			$list[$obj->getId()] = $obj;
		}
		return $list;
	}
}
