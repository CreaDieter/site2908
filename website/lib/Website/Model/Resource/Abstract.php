<?php

class Website_Model_Resource_Abstract extends Pimcore_Model_Resource_Abstract
{

	protected $_table;

	/**
	 * Constructor, auto loads table class
	 */
	public function __construct ()
	{
		$className = $this->findDbTableClass(get_class($this));
		if ($className) {
			$this->_table = new $className();
		}
	}

	/**
	 * Finds the table class for the current model
	 *
	 * @param string $classname
	 * @return string $dbTableName
	 */
	private function findDbTableClass ($classname)
	{
		$classNameArray = explode('_', $classname);
		if (is_array($classNameArray) && isset($classNameArray[2])) {
			$modelShortName = $classNameArray[2];
			$dbTableName = "Website_Table_" . $modelShortName;
		}

		return $dbTableName;
	}

	public function getTable ()
	{
		return $this->_table;
	}

	public function getModel ()
	{
		return $this->model;
	}

	protected function _getDefaultSelect ($withOrder = true)
	{
		return $this->_table->select();
	}

	public function getSelect() {
		return $this->_getDefaultSelect();
	}

	/**
	 * Get all records of this model
	 *
	 * @return array of objects
	 */
	public function getAll ()
	{
		$rows = $this->_table->fetchAll($this->_getDefaultSelect());
		$list = array();

		foreach ($rows as $row) {
			$class = get_class($this->getModel());
			$obj = new $class();
			$obj->setData($row->toArray());
			$list[] = $obj;
		}
		return $list;
	}

	/**
	 * Dynamicly get a record by one or more params
	 *
	 * @param string $property
	 * @param mixed|array $params
	 * @return boolean Pimcore_Model_Abstract
	 */
	public function getByProperty ($property, $params)
	{
		$select = $this->_table->select();

		if (strpos($property, 'And') !== false) {
			$propertyList = explode('And', $property);

			if (count($params) == count($propertyList)) {
				$i = 0;
				foreach ($propertyList as $prop) {
					$prop = lcfirst($prop);
					if (property_exists($this->getModel(), $prop)) {
						$select = $select->where("$prop = ?", $params[$i]);
					} else {
						throw new Exception(
							"$prop is not a valid property of class" .
							get_class($this->getModel()));
					}
					$i ++;
				}
			} else {
				throw new Exception(
					"The number of parameters is not equal to the number of properties.");
			}
		} else {
			if (property_exists($this->getModel(), $property)) {
				if (is_array($params)) {
					$param = $params[0];
				}
				$select->where("$property = ?", $params);
			} else {
				throw new Exception(
					"$property is not a valid property of class" .
					get_class($this->getModel()));
			}
		}

		$data = $this->_table->fetchRow($select);

		if ($data == null) {
			return false;
		}

		$this->getModel()->setData($data->toArray());
		return $this->getModel();
	}

	/**
	 * Delete the current model from database, based on the primary key(s)
	 *
	 * @throws Exception
	 */
	public function delete ()
	{
		$primaryKey = $this->getTable()->getPrimary();
		if (! is_array($primaryKey)) {
			$primaryKey = array(
				$primaryKey
			);
		}

		$where = array();
		foreach ($primaryKey as $key) {
			if (property_exists($this->getModel(), $key)) {
				$where[] = $this->_table->getAdapter()->quoteInto("$key = ?",
					$this->model->{'get' . ucfirst($key)}());
			} else {
				throw new Exception(
					"Given primary key $key is not a valid property of class " .
					get_class($this->getModel()));
			}
		}
		if (count($where) >= 1) {
			return $this->_table->delete($where);
		} else {
			throw new Exception(
				"No valid where clause could be build to delete this object");
		}
	}

	/**
	 * Saves a model
	 *
	 * Will insert model if not exists, else if will update the db record
	 *
	 * If no data provided, it will get all the data from the model
	 *
	 * @param array $data
	 * @throws Exception
	 * @return insertID
	 */
	public function save ($data = null)
	{

		if ($data == null) {
			$data = $this->getModel()->getData();
		}

		$primaryKey = $this->getTable()->getPrimary();
		if (! is_array($primaryKey)) {
			$primaryKey = array(
				$primaryKey
			);
		}

		$where = array();
		$select = $this->_table->select();
		$allowUpdate = true;
		foreach ($primaryKey as $key) {

			if (property_exists($this->getModel(), $key)) {
				if ($this->getModel()->{'get' . ucfirst($key)}() == null) {
					$allowUpdate = false;
				}
				$select = $select->where("$key = ?",
					$this->getModel()->{'get' . ucfirst($key)}());
				$where[] = $this->_table->getAdapter()->quoteInto("$key = ?",
					$this->getModel()->{'get' . ucfirst($key)}());
			} else {
				throw new Exception(
					"Given primary key $key is not a valid property of class " .
					get_class($this->getModel()));
			}
		}



		if ($allowUpdate) {
			$return = $this->_table->fetchRow($select);
		}

		if ($return == null) {
			return $this->_table->insert($data);
		} else {
			return $this->_table->update($data, $where);
		}



	}



	// todo: paginate, where-clause, order-clause, ...
	/**
	 * $where = array("active='1'", "reserved='0'" )
	 *
	 * @param unknown_type $where
	 * @param unknown_type $order
	 * @return Zend_Paginator
	 */
	public function getPaginator ($where = null, $order = null, $join = null,
		$paginatorAdapter = "Website_Paginator_Adapter_Object")
	{
		if ($join && ! empty($join)) {
			$select = $this->_table->getAdapter()
				->select()
				->from(array(
						"t" => $this->_table->info("name")
					));
			foreach ($join as $joinClause) {
				$fields = array();
				if (isset($joinClause['fields'])) {
					$fields = $joinClause['fields'];
				}
				$select->join(
					array(
						$joinClause["tableAlias"] => $joinClause["table"]
					), $joinClause["on"], $fields);
			}
		} else {
			$select = $this->_getDefaultSelect(is_null($order));
		}
		if ($where) {
			if (is_array($where)) {
				foreach ($where as $case) {
					$case = '(' . $case . ')';
				}
				$where = implode(' AND ', $where);
			}
			$select->where($where);
		}
		if ($order) {
			$select->order($order);
		}
		$adapter = new $paginatorAdapter($select, $this->model);
		$paginator = new Zend_Paginator($adapter);
		return $paginator;
	}

	public function fetch(Website_Criteria $criteria) {
		$select = $this->criteriaToSelect($criteria);

		$rows = $this->_table->fetchAll($select);

		$list = array();
		$class = get_class($this->model);
		foreach ($rows as $row) {
			if (is_object($row)) {
				$row = $row->toArray();
			}
			$obj = new $class;
			$obj->setdata($row);
			$list[] = $obj;
		}
		return $list;
	}

	/**
	 * Transform a criteria object to a select object
	 *
	 * @param  Website_Criteria $criteria
	 *
	 * @return Zend_Db_Table_Select
	 */
	public function criteriaToSelect(Website_Criteria $criteria)
	{
		$select = $this->getSelect();
		foreach ($criteria as $logic) {
			switch ($logic['type']) {
				case 'orWhere':
					$select->orWhere($logic['statement'], $logic['value']);
					break;
				case 'where':
				default:
					$select->where($logic['statement'], $logic['value']);
					break;
			}
		}
		// add limit logic:
		$limit = $criteria->getLimitData();
		$select->limit($limit[Website_Criteria::LIMIT_COUNT], $limit[Website_Criteria::LIMIT_OFFSET]);

		// add sort logic:
		$sortData = $criteria->getSortData();
		if (is_array($sortData) && !empty($sortData)) {
			foreach ($sortData as $sort) {
				$select->order($sort[Website_Criteria::SORT_FIELD] . " " . $sort[Website_Criteria::SORT_ORDER]);
			}
		}

		// add group-by logic:
		$groupData = $criteria->getGroupData();
		if (is_array($groupData) && !empty($groupData)) {
			$select->group($groupData);
		}

		return $select;
	}

	public function search($q) {
		return array();
	}

}