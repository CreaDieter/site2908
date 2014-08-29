<?php if ($this->element instanceof Object_Vacancy) { ?>
<tr class="newsletter_vacancy_detail" >
	<td>
		<table>
			<tr>
				<td>
					<h1 style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;" ><?= $this->element->getTitle($this->language); ?></h1>
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