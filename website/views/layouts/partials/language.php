<div id="language">
	<ul>
		<?php
		// Loop through frontend languages
		foreach (Pimcore_Tool::getValidLanguages() as $language) {
		$document = $this->inotherlang($this->document,$language);
		if ($document && $document->getPublished()) { ?>
		<li <?= $language == CURRENT_LANGUAGE ? 'id="activeLanguage"' : ''?>><a href='<?= $document->getFullPath()?>' ><?= $this->translate($language)?></a>
			<?php  } else { ?>
		<li <?= $language == CURRENT_LANGUAGE ? 'id="activeLanguage"' : ''?>><a href='<?= $this->home($language)?>' ><?= $this->translate($language)?></a>
			<?php	}
			}
			?>
	</ul>
</div>