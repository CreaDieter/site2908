<?php if ($this->editmode) { ?>
	<?php $key = md5(microtime().rand()); ?>
	<div class="newsletter_subtitle_general editmode" id="<?= $key; ?>">
		<h2 style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;" ><?= $this->input('title') ?></h2>
	</div>
<?php } else { ?>
	<tr>
		<td>
			<h2 style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;" ><?= $this->input('title') ?></h2>
		</td>
	</tr>
<?php } ?>

