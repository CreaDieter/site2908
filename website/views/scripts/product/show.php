<h1><?= $this->input('title') ?></h1>

<a class="backtooverview" href="<?= $this->parent->getHref(); ?>"><?= $this->translate('terug naar overzicht') ?></a>

<?=$this->wysiwyg('intro')?>

<article id="gallery">
	<?php if ($this->editmode) { ?>
		<h1><?= $this->translate('Plaats hier de afbeeldingen van het product') ?></h1>
		<?php print $this->multihref("images"); ?>
	<?php } else if (count($this->multihref("images")->getData()) > 0) { ?>
			<div id="maingallery" class="cycle-slideshow"
				 data-cycle-slides="> div"
				 data-cycle-timeout="0"
				 data-cycle-fx="fade">
				<?php foreach($this->multihref("images") as $element) { ?>
					<?php
					if ($element instanceof Asset_Image) { ?>
						<div><a href="<?php echo $element->getThumbnail("product_detail") ?>" title="" class="enlarge"><img src="<?php echo $element->getThumbnail("product_detail") ?>" /></a></div>
					<?php } ?>
				<?php } ?>
			</div>
			<div id="carouselgallery" class="cycle-slideshow"
				 data-cycle-slides="> div"
				 data-cycle-timeout="0"
				 data-cycle-prev=".cycle-prev"
				 data-cycle-next=".cycle-next"
				 data-cycle-fx="carousel"
				 data-allow-wrap="true"
				>
				<?php
				$i = 0;
				foreach($this->multihref("images") as $element) { ?>
					<?php
					if ($element instanceof Asset_Image) { ?>
						<div data-index="<?php echo $i; ?>"><img src="<?php echo $element->getThumbnail("product_detail") ?>" /></div>
						<?php $i++; ?>
					<?php } ?>
				<?php } ?>
			</div>
			<nav id="pagernav">
				<a href="#" class="cycle-prev">prev</a>/<a href="#" class="cycle-next">next</a>
			</nav>
	<?php } ?>
</article>

<article id="attachments">
	<?php if ($this->editmode) { ?>
		<h1><?= $this->translate('Plaats hier de bijlages van het product') ?></h1>
		<?php print $this->multihref("attachments"); ?>
	<?php } else if (count($this->multihref("attachments")->getData()) > 0) { ?>
		<h1><?= $this->translate('Bijlages') ?></h1>
		<ul>
			<?php foreach($this->multihref("attachments") as $element) { ?>
				<?php
				$class = pathinfo($element->filename, PATHINFO_EXTENSION);
				// note for frontend: class is the filetype (e.g. pdf), so you can add a nice icon in front of the filename :)
				?>
				<li><a class="<?=$class?>" target="_blank" href="<?= $element->getFullPath() ?>" ><?= $element->filename ?></a></li>
			<?php } ?>
		</ul>
	<?php } ?>
</article>

<div>
	<?php
	if ($this->canRequestOffer) {
		try {
			$url = $this->url(
				array('language' => $this->language, 'id' => $this->productId, 'key' => $this->productKey),
				'offer',
				true
			);
		} catch (Exception $e) {
			if ($this->debug) { ?>
				<div class="bs-callout bs-callout-danger">
					<h4>static route not set</h4>
					<p>Please set the static route for 'offer' in the backoffice.</p>
				</div>
			<?php }
		}
		if (isset($url)) {
			?>
			<a href="<?= $url ?>" class="offerBtn"><?= $this->translate('Offerte aanvragen') ?></a>
		<?php
		}
	}
	?>
</div>

<script>
	var slideshows = $('.cycle-slideshow').on('cycle-pager-activated', function(e, opts) {
		slideshows.not(this).cycle('goto', opts.currSlide);
	});

	$('#carouselgallery').on('click', '.cycle-slide', function(){
		var index = $("#carouselgallery").data('cycle.API').getSlideIndex(this);
		slideshows.cycle('goto', index);
		$('#maingallery').cycle('goto', $(this).data('index'));
	});
</script>