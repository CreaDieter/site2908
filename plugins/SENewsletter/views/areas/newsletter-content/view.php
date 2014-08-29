<?php if (!$this->editmode) { ?>
	<tr>
		<td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;">
			<?= $this->wysiwyg('content') ?>
		</td>
	</tr>
<?php } else { ?>
	<?php $key = md5(microtime().rand()); ?>
	<div class="newsletter_wysiwyg_general editmode" id="<?= $key; ?>">
		<?= $this->wysiwyg('content') ?>
	</div>
<?php } ?>
