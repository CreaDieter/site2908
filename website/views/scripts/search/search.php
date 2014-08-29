<div id="content">
	<article>
		<?php if ($this->noSearch || !$this->query) { ?>
			<h1><?= $this->translate('Gelieve een zoekterm in te geven') ?></h1>
		<?php } else { ?>
			<h1><?= $this->translate('Aantal resultaten gevonden:') . ' ' . count($this->results) ?></h1>
			<p><?= $this->translate('U hebt gezocht op')?> <strong><?= $this->query ?></strong></p>

			<?php if (count($this->results) == 0) { ?>
				<p><strong><?= $this->translate('Helaas, uw zoekbewerking heeft geen resultaten opgeleverd.') ?></strong><br><?= $this->translate('Controleer de spelling van uw zoekopdracht of zoek opnieuw met andere zoektermen.') ?></p>
			<?php } ?>
		<?php } ?>
		<form method="post" action="<?= $this->documentUrl($this->config->search_document) ?>">
			<input type="text" name="zoek" required="required" value="<?= $this->query ?>">
			<input type="submit" value="<?= $this->translate('Zoeken') ?>" >
		</form>

		<?php if (!$this->noSearch && $this->query) { ?>
			<ul>
				<?php foreach ($this->paginator as $result) { ?>
					<li class="searchresult">
						<?php if (isset($result['title'])): ?><h2><?= $result['title'] ?></h2><?php elseif (isset($result['document_title'])): ?><h2><?= $result['document_title'] ?></h2>  <?php endif; ?>
						<?php if (isset($result['content'])): ?><?= $this->truncate($result['content'], 200, '...') ?><?php endif; ?>
						<?php if (isset($result['url'])): ?>
							<br /><a href="<?= $result['url'] ?>">
								<?php if (isset($result['title'])): ?><?= $this->translate('Bekijk zoekresultaat') ?><?php endif; ?>
							</a>
						<?php endif; ?>
					</li>
				<?php } ?>
			</ul>
			<?php echo $this->paginationControl(
				$this->paginator,
				'Sliding',
				'includes/paging.php',
				array(
					'urlprefix' => $this->document->getFullPath() . '?page=',
					'appendQueryString' => true
				)
			); ?>
		<?php } ?>
	</article>
</div>