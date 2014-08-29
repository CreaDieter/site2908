<article>
	<h1><?= $this->translate('Vacatures') ?></h1>
	<?= $this->wysiwyg('vacature_content') ?>
	<section id="newsoverview">
		<?php
		if (count($this->paginator) >= 1) {
			?>
			<article class="newsitem">
				<div class="heading">
					<?php
					/**
					 * @var $item Object_Vacancy
					 */
					foreach ($this->paginator as $item) {
						?>
						<h2><a href="<?=
							$this->url(
								array('language' => $this->language, 'id' => $item->getId(), 'key' => $item->getKey()),
								'vacancy_detail',
								true
							) ?>"><?= $item->getTitle(); ?></a></span>
						</h2>
						<?= $this->translate('Geplaatst op:') ?>
						<span class="date"><?=
							$item->getDate()->toString(
								"dd.MM.Y"
							) ?></span>
						<p><?= $this->truncate($item->getContent(), 200, '...') ?></p>
					<a href="<?=
					$this->url(
						array('language' => $this->language, 'id' => $item->getId(), 'key' => $item->getKey()),
						'vacancy_detail',
						true
					) ?>"><?= $this->translate("Lees meer") ?>
						</a>
					<?php
					} ?>
				</div>
			</article>
		<?php
		} else {
			?>
			<p><?= $this->translate('Er werden geen vacatures gevonden.') ?></p>
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