<article>
	<h1><?= $this->translate('Evenementen') ?></h1>
	<?= $this->wysiwyg('intro') ?>
	<section id="newsoverview">
		<?php
		if (count($this->paginator) >= 1) {
			?>
			<article class="newsitem">
				<div class="heading">
					<?php
					/**
					 * @var $item Object_Event
					 */
					foreach ($this->paginator as $item) {
						?>
						<h2><a href="<?=
							$this->url(
								array('language' => $this->language, 'id' => $item->getId(), 'key' => $item->getKey()),
								'event_detail',
								true
							) ?>"><?= $item->getTitle(); ?></a><span class="date"><?= $item->getDate()->toString(
									"dd.MM.Y"
								) ?></span>
						</h2>
						<p><?= $this->truncate($item->getContent(), 200, '...') ?></p>
					<?php
					} ?>
				</div>
			</article>
		<?php
		} else {
			?>
			<p><?= $this->translate('Er werden geen evenementen gevonden.') ?></p>
		<?php } ?>
	</section>
	<?php if ($this->paginator->getTotalItemCount() >= 10) { ?>
		<!-- pagination start -->
		<?php echo $this->paginationControl(
			$this->paginator,
			'Sliding',
			'includes/paging.php',
			array(
				'urlprefix' => $this->document->getFullPath() . '?page=',
				'appendQueryString' => true
			)
		); ?>
		<!-- pagination end -->
	<?php } ?>
</article>
