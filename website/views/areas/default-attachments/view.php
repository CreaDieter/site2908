<?php if ($this->editmode) { ?>
	<h3><?= $this->translate('Plaats hier de bijlages om te downloaden') ?></h3>
	<?php print $this->multihref("attachments"); ?>
<?php } else if (count($this->multihref("attachments")->getData()) > 0) { ?>
	<div id="attachments">
		<h2><?= $this->translate('Bijlages') ?></h2>
		<ul>
			<?php foreach($this->multihref("attachments") as $element) { ?>
				<?php $class = pathinfo($element->filename, PATHINFO_EXTENSION); ?>
				<li><a class="<?=$class?>" target="_blank" href="<?= $element->getFullPath() ?>" ><?= $element->getProperty('displayName') ?: $element->getFilename() ?></a></li>
			<?php } ?>
		</ul>
	</div>
<?php } ?>