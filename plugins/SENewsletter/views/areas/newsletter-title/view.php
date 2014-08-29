<?php if ($this->editmode) { ?>
	<?php $key = md5(microtime().rand()); ?>
	<div class="newsletter_title_general editmode" id="<?= $key; ?>">
		<h1 style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;" ><?= $this->input('title') ?></h1>
	</div>
<?php } else { ?>
	<tr>
		<td>
			<h1 style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;" ><?= $this->input('title') ?></h1>
		</td>
	</tr>
<?php } ?>

