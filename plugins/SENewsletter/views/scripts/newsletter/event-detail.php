<?php if ($this->element instanceof Object_Event) { ?>
<tr class="newsletter_event_detail" >
	<td>
		<table>
			<tr>
				<td>
					<h1 style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;" ><?= $this->element->getTitle($this->language); ?></h1>
					<p style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;" ><?= $this->element->getDate()->toString(
							"dd.MM.Y"
						) ?></p>
				</td>
			</tr>
			<?php if ($this->hasImage && $this->element->getImage()) { ?>
				<tr>
					<td>
						<img src="<?= $this->baseUrl . $this->element->getImage()->getThumbnail('newsletter_default'); ?>"  alt="<?= $this->translate('Image'); ?>" />
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;" >
					<?= $this->element->getContent($this->language); ?>
					<?php if ($this->url) { ?>
						<a href="<?= $this->url; ?>"> <?= $this->translate('Lees meer') ?></a>
					<?php } ?>
				</td>
			</tr>
		</table>
	</td>
</tr>
<?php } ?>