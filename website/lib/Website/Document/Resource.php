<?php

class Website_Document_Resource extends Document_List_Resource {
    private $fromClause = "FROM documents AS d LEFT JOIN documents_elements ON d.id = documents_elements.documentId LEFT JOIN documents_page ON d.id = documents_page.id ";

    /**
     * Loads a list of objects (all are an instance of Document) for the given parameters an return them
     *
     * @return array
     */
    public function load() {
        $documents = array();
        $documentsData = $this->db->fetchAll("SELECT d.id, d.type " . $this->fromClause . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());

        foreach ($documentsData as $documentData) {
            if($documentData["type"]) {
                if($doc = Document::getById($documentData["id"])) {
                    $documents[] = $doc;
                }
            }
        }

        $this->model->setDocuments($documents);
        return $documents;
    }

    /**
     * Loads a list of document ids for the specific parameters, returns an array of ids
     *
     * @return array
     */
    public function loadIdList() {
        $documentIds = $this->db->fetchCol("SELECT d.id " . $this->fromClause . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());
        return $documentIds;
    }

    protected function getCondition() {
        $filterCondition= array();
        $i = 0;
        foreach ($this->model->getFilterOnElements() as $filterGroup) {
            if($i !== 0) $filterCondition[] = "OR ";
            $j = 0;
            foreach ($filterGroup as $key => $filterCondition) {
                if($j !== 0) $filterCondition[] = "AND ";
                $filterCondition[] = "documents_elements.name = '$key' AND documents_elements.data $filterCondition ";
                $j++;
            }
            $i++;
        }

        if ($condition = $this->model->getCondition()) {
            if (Document::doHideUnpublished() && !$this->model->getUnpublished()) {
                return " WHERE (" . $condition . ") AND (" . implode($filterCondition) . ") AND published = 1";
            }
            return " WHERE (" . $condition . ") AND (" . implode($filterCondition) . ")";
        }
        else if (!empty($filterCondition)) {
            return " WHERE (" . implode($filterCondition) . ") AND published = 1";
        }
        else if (Document::doHideUnpublished() && !$this->model->getUnpublished()) {
            return " WHERE published = 1";
        }
        return "";
    }

    public function getCount() {
        $amount = (int) $this->db->fetchOne("SELECT COUNT(*) as amount " . $this->fromClause . $this->getCondition() . $this->getOffsetLimit(), $this->model->getConditionVariables());
        return $amount;
    }

    public function getTotalCount() {
        $amount = (int) $this->db->fetchOne("SELECT COUNT(*) as amount " . $this->fromClause . $this->getCondition(), $this->model->getConditionVariables());
        return $amount;
    }
}
