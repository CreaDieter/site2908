<?php 
$_forbiddenPages = array();

if ($this->pages) {
	$i = count($this->pages);
	foreach ($this->pages as $page) { $i--;
		if (!in_array($page->getLabel(),$_forbiddenPages)) { ?>
			  <span>/</span> 
			 <? if ($i > 0) { ?>
				<a href="<?= $page->getUri(); ?>">
			<? } ?>
				<?= $page->getLabel(); ?>	
			<? if ($i > 0) { ?>
				</a>
			<? } ?>
	<?	}
	}
}
