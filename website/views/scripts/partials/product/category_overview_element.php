<?php if ($this->element instanceof Document_Hardlink) {
	// it's a hard link -> get original document
	$childContent = $this->element->getSourceDocument();
} else {
	$childContent = $this->element;
}?>

<li class="productList" >
	<article>
		<a href="<?= $this->element->getFullPath()?>" title="" class="desc">
			<span class="title"><?= $childContent->title; ?></span>
			<?php if ($childContent->getElement('subtitle')) { ?><span class="subtitle"><?= $childContent->getElement('subtitle') ?></span> <?php } ?>
		</a>
	</article>
</li>