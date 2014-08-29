<ul class="sf-menu">
<?
$activestate = $this->navigation()->findOneBy('active', 1);

	// Documents which should not be a link in the menu
$notRealPages = array();

if ($activestate instanceof Zend_Navigation_Page ){
    $activePage = $this->navigation()->findOneBy('active', 1)->getId();
}else{
    $activePage = 0;	
}
foreach ($this->container as $page) {
    if($page instanceof Zend_Navigation_Page && $page->getDocument()->getProperty("navigation_exclude") == 0){
	    $active = "";

	    if ($page->getId() == $activePage) {
	        $active = "class='active'";
	    }

	    $url = $page->getHref();
	    if (in_array($page->getId(),$notRealPages)) {
		    $url = "#";
	    }
    	//echo($page->getHref() . " => " . $page->getLabel() . "<br>");

?>
    <li <?=$active?>>
    	<a href="<?=$url?>" title=""><?=$page->getLabel()?></a>
    	<? if(!empty($page->pages)) { ?>
    	<div></div>
    	<ul>
    		<?
    		foreach($page->pages as $childPage) {
				if ($childPage->getDocument()->getProperty("navigation_exclude") == 1) continue;
    			$class = $childPage->isActive() ? 'active' : '';
    		?>
    		<li class="<?=$class?>"><a href="<?=$childPage->getHref()?>" title=""><?=$childPage->getLabel()?></a></li>
    		<? } ?>
    	</ul>
    	<? } ?>
    </li>

<?
}
}
?>
</ul>