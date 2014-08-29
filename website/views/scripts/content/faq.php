<h1><?= $this->input('title') ?></h1>
<?= $this->wysiwyg('content_text'); ?>

<?php if (!$this->editmode) { // only show these if not in edit mode ?>
	<ul id="faqAnchors">
		<?php
		$counter = 1;
		while ($this->block("contentblock")->loop()) {
			?>
			<li>
				<a href="#faq_<?=$counter?>"><?=$counter?>. <?=$this->input('faq_title')?></a>
			</li>
			<?php
			$counter++;
		} ?>
	</ul>
<?php } ?>
<ul id="faqItems">
	<?php
	$counter = 1;
	while ($this->block("contentblock")->loop()) {
		?>
		<li id="faq_<?=$counter?>">
			<article class="faqDetail" >
				<h2><?= $this->editmode ? '' : $counter . '. ' ?><?= $this->input('faq_title')?></h2>
				<?= $this->editmode ? $this->textarea('faq_content') : nl2br($this->textarea('faq_content')) ?>
				<br /><a href="#" class="totop" title="<?= $this->translate('Terug naar boven') ?>"><?= $this->translate('Terug naar boven') ?></a>
			</article>
		</li>
		<?php
		$counter++;
	}
	?>
</ul>
