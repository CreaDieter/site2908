<article>
	<section id="newsoverview">
		<article class="newsitem">
			<div class="detail">
				<h2><?= $this->item->getTitle() ?><span class="date"><?=
						$this->item->getDate()->toString(
							"dd.MM.Y"
						) ?></span></h2>
				<?= $this->item->getContent() ?>
			</div>
			<a href="<?= $this->documentUrl($this->config->news_document) ?>" class="backtooverview"><?=
				$this->translate(
					'Terug naar het nieuwsoverzicht'
				) ?></a>
		</article>
	</section>
</article>
