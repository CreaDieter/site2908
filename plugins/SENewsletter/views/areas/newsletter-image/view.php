<?php if ($this->editmode) { ?>
<?php $key = md5(microtime().rand()); ?>
<div class="newsletter_image_general editmode" id="<?= $key; ?>">
	<?php echo $this->image('image',array('hidetext'=>true)); ?>
</div>
<?php } else {
	$thumbnail = $this->image('image')->getThumbnail('');
	$thumbnail =  $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $thumbnail;
	?>
	<tr>
		<td>
			<img style="width: 635px"; src="<?= $thumbnail; ?>" alt="<?= $this->translate('afbeelding nieuwsbrief') ?>" >
		</td>
	</tr>
<?php }