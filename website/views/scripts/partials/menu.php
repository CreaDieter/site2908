<ul>
	<?php
	$activestate = $this->navigation()->findOneBy('active', 1);

	if ($activestate instanceof Zend_Navigation_Page ){
		$activePage = $this->navigation()->findOneBy('active', 1)->getId();
	}else{
		$activePage = 0;
	}
	foreach ($this->container as $page) {
		if($page instanceof Zend_Navigation_Page && $page->getDocument()->getProperty("navigation_exclude") == 0){
			// element is active?
			$active = "";
			if ($page->getId() == $activePage) {
				$active = "id='active'";
			}

			// the target
			$target = $page->getDocument()->getProperty('navigation_target') ? 'target="' . $page->getDocument()->getProperty('navigat    ion_target')  . '"' : '';
			?>
			<li <?=$active?> ><a <?= $target; ?> href="<?=$page->getHref()?>"><?=$page->getLabel()?><span></span></a></li>
		<?php
		}
	}
	?>
</ul>