<article>
	<section id="newsoverview">
		<article class="newsitem">
			<div class="detail">
				<h2><?= $this->item->getTitle() ?> <span class="date"></h2>
				<?= $this->item->getContent() ?>
			</div>
			<a href="<?= $this->documentUrl($this->config->vacancy_document) ?>" class="backtooverview"><?=
				$this->translate(
					'Terug naar het vacature overzicht'
				) ?></a>
		</article>
		<?php if ($this->showApplyForm) { ?>
			<article class="form">
				<h1><?= $this->translate('Solliciteren?') ?></h1>
				<?php if (count($this->messages) > 0) { ?>
					<ul id="messages">
						<?php foreach ($this->messages as $message) : ?>
							<li><?php echo $this->escape($message); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php } else { ?>
					<?= $this->form ?>
				<?php } ?>
			</article>
		<?php } ?>
	</section>
</article>
