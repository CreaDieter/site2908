<article>
	<h1><?= $this->translate('Nieuws') ?></h1>
	<section id="newsoverview">
		<?php
		if (count($this->paginator) >= 1) {
			?>
			<article class="newsitem">
				<?php
				/**
				 * @var $item Object_Neww
				 */
				foreach ($this->paginator as $item) {
					?>
					<h2><a href="<?=
						$this->url(
							array('language' => $this->language, 'id' => $item->getId(), 'key' => $item->getKey()),
							'news_detail',
							true
						) ?>"><?= $item->getTitle(); ?></a>
					</h2>
					<?= $this->translate('Geplaatst op:') ?>
					<span class="date"><?=
						$item->getDate()->toString(
							"dd.MM.Y"
						) ?></span>
					<p>
						<?= $this->truncate($item->getContent(), 250, '...') ?>
					</p>
					<a href="<?=
					$this->url(
						array('language' => $this->language, 'id' => $item->getId(), 'key' => $item->getKey()),
						'news_detail',
						true
					) ?>"><?= $this->translate("Lees meer") ?></a>
				<?php
				} ?>
			</article>
		<?php
		} else {
			?>
			<p><?= $this->translate('Er werden geen nieuwsberichten gevonden.') ?></p>
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
