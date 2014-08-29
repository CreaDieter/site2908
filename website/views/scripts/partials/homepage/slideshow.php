<?php if (!empty($this->elements)) { ?>
<div id="slideshowcontainer">
	<?php $count = 0 ?>
	<div id="slideshow">
		<?php foreach($this->elements as $element) { ?>
			<?php
			// only product pages allowed!
			if ($element->controller != 'product' || $element->action != 'show') continue;

			if ($element instanceof Document_Page or $element instanceof Document_Hardlink) { ?>

				<?php if ($element instanceof Document_Hardlink) {
					// it's a hard link -> get original document
					$element = $element->getSourceDocument();
					if (is_null($element)) continue; // no source document added to hardlink
				} ?>

				<?php
				// get the image
				$image = '';

				$images = $element->getElement('images');
				if ($images) {
					$elements = $images->getElements();
					foreach ($elements as $el) {
						if ($el instanceof Asset_Image) {
							$image = $el;
							break;
						}
					}
				}

				if (empty($image)) $image = Pimcore_Config::getWebsiteConfig()->empty_image;
				?>
				<?php if (!empty($image)) { $count++; ?>
					<div>
						<img src="<?php echo $image->getThumbnail("homepage_slideshow") ?>" alt="" />
						<span class="caption"> <?= $element->getElement('title') ?> <a href="<?= $element->getHref() ?>" title="">Meer info</a></span>
					</div>
				<?php } ?>
			<?php } ?>
		<?php } ?>
	</div>
	<?php if ($count > 1) { ?>
		<a href="#" title="" id="prevslide"></a>
		<a href="#" title="" id="nextslide"></a>
	<?php } ?>
</div>
<?php } ?>