<?php
class Website_Navigation_Main
{
    protected $_activeDocument;
    protected $_navigationContainer;
    protected $_htmlMenuIdPrefix;
    protected $_pageClass = 'Pimcore_Navigation_Page_Uri';
    protected $_navDepth = 1;

    /**
     * Gets the navigation
     *
     * @param $activeDocument               which document is active?
     * @param null $navigationRootDocument  what document do we need to start from?
     * @param null $htmlMenuIdPrefix
     * @param int $depth                    How many levels of navigation do you need?
     * @return Zend_Navigation
     */
    public function getNavigation($activeDocument, $navigationRootDocument = null, $htmlMenuIdPrefix = null, $depth = 2)
    {
        $this->_navDepth = $depth;

        $this->_activeDocument = $activeDocument;
        $this->_htmlMenuIdPrefix = $htmlMenuIdPrefix;

        $this->_navigationContainer = new Zend_Navigation();

        if (!$navigationRootDocument) {
            $navigationRootDocument = Document::getById(1);
        }

        if ($navigationRootDocument->hasChilds()) {
            $this->buildNextLevel($navigationRootDocument, null, true, $this->_navDepth);
        }
        return $this->_navigationContainer;
    }

    /**
     * sets the name of the pageclass (class must extend Zend_Navigation_Page)
     *
     * @param type $pageClass
     * @return Pimcore_View_Helper_PimcoreNavigation_Controller fluent interface, returns self
     */
    public function setPageClass($pageClass)
    {
        $this->_pageClass = $pageClass;
        return $this;
    }

    /**
     * Returns the name of the pageclass
     *
     * @return String
     */
    public function getPageClass()
    {
        return $this->_pageClass;
    }

    /**
     * @param  Document $parentDocument
     * @param  Pimcore_Navigation_Page_Uri $parentPage
     * @return void
     */
    protected function buildNextLevel($parentDocument, $parentPage = null, $isRoot = false, $depth)
    {
        $depth--;

        $pages = array();

        $childs = $parentDocument->getChilds();
        if (is_array($childs)) {
            foreach ($childs as $child) {

                if($child instanceof Document_Hardlink) {
                    $child = Document_Hardlink_Service::wrap($child);
                }

                if (($child instanceof Document_Page or $child instanceof Document_Link) and $child->getProperty("navigation_name")) {

                    $active = false;

                    if (strpos($this->_activeDocument->getRealFullPath(), $child->getRealFullPath() . "/") === 0 || $this->_activeDocument->getRealFullPath() == $child->getRealFullPath()) {
                        $active = true;
                    }

                    $path = $child->getFullPath();
                    if ($child instanceof Document_Link) {
                        $path = $child->getHref();
                    }

                    $page = new $this->_pageClass();
                    $page->setUri($path . $child->getProperty("navigation_parameters") . $child->getProperty("navigation_anchor"));
                    $page->setLabel($child->getProperty("navigation_name"));
                    $page->setActive($active);
                    $page->setId($this->_htmlMenuIdPrefix . $child->getId());
                    $page->setClass($child->getProperty("navigation_class"));
                    $page->setTarget($child->getProperty("navigation_target"));
                    $page->setTitle($child->getProperty("navigation_title"));
                    $page->setAccesskey($child->getProperty("navigation_accesskey"));
                    $page->setTabindex($child->getProperty("navigation_tabindex"));
                    $page->setRelation($child->getProperty("navigation_relation"));
                    $page->setDocument($child);

                    if ($child->getProperty("navigation_exclude") || !$child->getPublished()) {
                        $page->setVisible(false);
                    }

                    if ($active and !$isRoot) {
                        $page->setClass($page->getClass() . " active");
                    } else if ($active and $isRoot) {
                        $page->setClass($page->getClass() . " main mainactive");
                    } else if ($isRoot) {
                        $page->setClass($page->getClass() . " main");
                    }

                    if ($depth > 0 && $child->hasChilds()) {
                        $childPages = $this->buildNextLevel($child, $page, false, $depth);
                        $page->setPages($childPages);
                    }

                    $pages[] = $page;

                    if ($isRoot) {
                        $this->_navigationContainer->addPage($page);
                    }
                }
            }
        }

        return $pages;
    }

}