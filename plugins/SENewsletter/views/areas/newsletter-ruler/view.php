<?php if ($this->editmode) { ?>
	<?php $key = md5(microtime().rand()); ?>
	<div class="newsletter_ruler_general editmode" id="<?= $key; ?>">
		<br><hr><br>
	</div>
<?php } else { ?>
	<tr>
		<td>
			<br><hr><br>
		</td>
	</tr>
<?php } ?>