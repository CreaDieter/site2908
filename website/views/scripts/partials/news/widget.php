<?php if (!empty($this->news)) { ?>
	<h1><?= $this->translate('Nieuws') ?></h1>
	<p>
		<ul>
			<?php foreach ($this->news as $news) { ?>
				<?php if ($news instanceof Object_News) { ?>
					<li>
						<a href="<?= $this->url(array('language' => $this->language, 'id' => $news->getId(), 'key' => $news->getKey()), 'news_detail', true ) ?>">
							<span class="date"> <?= date('d.m.y',$news->getDate()->getTimestamp()) ?> </span>
							<?= $news->getTitle($this->language) ?>
						</a>
					</li>
				<?php } ?>
			<?php } ?>
		</ul>
		<?php
		$doc = Website_Config::getWebsiteConfig()->news_document;
		if ($doc) { ?>
			<a href="<?= $doc->getHref() ?>" class="morenews"><?= $this->translate('Meer nieuws lezen') ?></a>
		<?php } ?>
	</p>
<?php } ?>