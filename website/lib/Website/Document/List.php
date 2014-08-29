<?php

class Website_Document_List extends Document_List {
    protected $elementFilter = array();

    /**
     * Sets the filter on the elements of the document
     *
     * $filter = array( // OR between childfilters
     * 		array(
     * 			"elementName1" => "= blabla"
     * 		),
     * 		array(	// AND between these filters
     * 			"elementName2" => "= blabla",
     * 			"elementName3" => "= blabla",
     * 			"elementName4" => "= blabla"
     * 		);
     * );
     *
     * Result: elementName1 OR elementName2 AND elementName3 AND elementName4
     *
     * @param array $filter
     */
    public function setFilterOnElements($filter){
        $this->initResource('Website_Document');

        $this->elementFilter = $filter;
    }

    /**
     * Gets the filter on the elements of the document
     *
     * $filter = array( // OR between childfilters
     * 		array(
     * 			"elementName1" => "= blabla"
     * 		),
     * 		array(	// AND between these filters
     * 			"elementName2" => "= blabla",
     * 			"elementName3" => "= blabla",
     * 			"elementName4" => "= blabla"
     * 		);
     * );
     *
     * Result: elementName1 OR elementName2 AND elementName3 AND elementName4
     *
     * @return array
     */
    public function getFilterOnElements() {
        return $this->elementFilter;
    }

}
