<?php if ($this->editmode) { ?>
	<?= $this->input('default-marquee'); ?>
<?php } else { ?>
	<marquee><?= $this->input('default-marquee'); ?></marquee>
<?php } ?>