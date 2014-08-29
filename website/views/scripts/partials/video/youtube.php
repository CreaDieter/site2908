<?php if (!empty($this->url)) { ?>
	<?php
		$width = is_null($this->width) ? '560' : $this->width;
		$height = is_null($this->height) ? '315' : $this->height;

		// get the youtube url
		preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $this->url, $matches);
		if (count($matches) > 0) {
	?>
	<iframe width="<?= $width ?>" height="<?= $height ?>" src="https://www.youtube.com/embed/<?= reset($matches) ?>" frameborder="0" allowfullscreen></iframe>
	<?php } ?>
<?php } ?>