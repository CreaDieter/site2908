<h1><?= $this->getTitle() ?></h1>
<?= $this->wysiwyg('intro') ?>


<?php if (count($this->children) > 0) { ?>
	<div class="listingByBlock">
		<?php foreach ($this->children as $child) { ?>
			<?php if ($child instanceof Document_Hardlink && $child->getSourceDocument() == null) {
				continue;
			}?>
			<?= $this->partial('./partials/product/category_overview_element.php',array('element'=>$child)) ?>
		<?php } ?>
	</div>

	<?= $this->paginationControl($this->children, 'Sliding', 'partials/paginator.php', array(
			'urlprefix' => $this->document->getFullPath() . '?page=',
			'appendQueryString' => true
		));
	?>

<?php } else { ?>
	<?= $this->translate('Er werden nog geen producten toegevoegd aan deze categorie'); ?>
<?php } ?>