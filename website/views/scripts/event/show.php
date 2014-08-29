<article>
	<h1><?= $this->translate('Evenementen') ?></h1>
	<section id="newsoverview">
		<article class="newsitem">
			<div class="detail">
				<h2><?= $this->item->getTitle() ?></h2> <span class="date"><?= $this->translate('Aangemaakt op') ?>
						: <?= $this->item->getDate()->toString("dd.MM.Y") ?></span>
				<p><?= $this->item->getContent() ?></p>
				<a href="<?= $this->documentUrl($this->config->event_document) ?>"
				   class="backtooverview"><?= $this->translate('Terug naar het evenementen overzicht') ?></a>
			</div>
		</article>
		<?php if ($this->showEventForm) { ?>
		<article class="form">
			<h1><?= $this->translate('Inschrijven?') ?></h1>
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
