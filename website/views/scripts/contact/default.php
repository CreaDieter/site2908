<article>
	<h1><?php echo $this->input("headline"); ?></h1>
	<?php $content = $this->wysiwyg("content"); ?>

	<?php if (!$this->editmode) {
		// Obfuscate all emailaddresses in the content
		$content = $this->fullTextEmailObfuscate($content);
	}

	echo $content; ?>

	<section id="contactcontainer">
		<?php if (count($this->messages)) { ?>
			<ul id="messages">
				<?php foreach ($this->messages as $message) : ?>
					<li><?php echo $this->escape($message); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php } ?>
		<?php if ($this->showContactForm) { ?>
			<div id="contactform">
				<dl id="contactinfo">
					<?php if ($this->editmode || $this->textarea('adres')->text != '') { ?>
						<dt><?= $this->translate('Adres') ?></dt>
						<dd><?= nl2br($this->textarea('adres')) ?></dd>
					<?php } ?>
					<?php if ($this->editmode || $this->input('telefoonnummer')->text != '') { ?>
						<dt><?= $this->translate('Telefoonnummer') ?></dt>
						<dd><?= $this->input('telefoonnummer') ?></dd>
					<?php } ?>
					<?php if ($this->editmode || $this->input('fax')->text != '') { ?>
						<dt><?= $this->translate('Fax') ?></dt>
						<dd><?= $this->input('fax') ?></dd>
					<?php } ?>
					<?php if ($this->editmode || $this->input('email')->text != '') { ?>
						<dt><?= $this->translate('E-mailadres') ?></dt>
						<dd><?php if ($this->editmode) : echo $this->input('email');
							else: echo($this->fullTextEmailObfuscate($this->input('email'))); endif; ?></dd>
					<?php } ?>
				</dl>
				<?php echo $this->form; ?>
				<p class="sec">
					<?php echo $this->translate('Fields marked with * are required') ?>
				</p>
				<script type="text/javascript">
					if (document.getElementById("hiddie")) {
						document.getElementById("hiddie").value = "correctValue";
					}
				</script>
			</div>
		<?php } ?>

		<?php if ($this->showGoogleMaps) { ?>
			<div id="map_canvas" class="contactmap"></div>
		<?php } ?>
	</section>

	<?php if ($this->showGoogleMaps) { ?>
		<script type="text/javascript">
			Storme.setConfig({
				'contact': {
					'geoAddress': "<?= $this->geo_address?>",
					'geoLat': "<?= $this->geo_lat?>",
					'geoLng': "<?= $this->geo_long?>"
				}
			});
		</script>
	<?php } ?>
</article>
