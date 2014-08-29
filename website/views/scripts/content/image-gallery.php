<section id="eyecatcher">&nbsp;</section>
<div id="content">
	<article>
		<h1><?= $this->input('title') ?></h1>
		<?= $this->wysiwyg('content_text');?>
	</article>
	<article>
		<?php if ($this->editmode) { ?>
			<?= $this->multihref("images"); ?>
		<?php } else { ?>
			<div id="blueimp-gallery" class="blueimp-gallery  blueimp-gallery-controls">
				<div class="slides"></div>
				<h3 class="title"></h3>
				<a class="prev">‹</a>
				<a class="next">›</a>
				<a class="close">×</a>
				<a class="play-pause"></a>
				<ol class="indicator"></ol>
			</div>

			<div id="links">
				<?php array_walk($this->getPhotoAlbumImages($this->multihref('images')),function(Asset_Image$image) {
						if (!$image instanceof Asset_Image) return; ?>
							<a class="thumbnail center-all" href="<?= $image->getThumbnail('photoalbum_full') ?>" title="<?= $image->getFilename() ?>">
								<img src="<?= $image->getThumbnail('photoalbum_thumb') ?>" class="additional-image" >
							</a>
					<?php }) ?>
			</div>

			<ul id="photoalbum">

			</ul>
		<?php } ?>
	</article>
</div>
<script>
	$(function() {
		document.getElementById('links').onclick = function (event) {
			event = event || window.event;
			var target = event.target || event.srcElement,
				link = target.src ? target.parentNode : target,
				options = {index: link, event: event},
				links = this.getElementsByTagName('a');
			blueimp.Gallery(links, options);
		};
	});
</script>