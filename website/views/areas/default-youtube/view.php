<?php if ($this->editmode) { ?>
	<strong><?= $this->translate('Plaats hier de link naar de youtube-video') ?></strong>
	<?= $this->input('youtubeVideo') ?>
<?php } else if ($this->input('youtubeVideo') != '') { ?>
	<div>
		<?= $this->partial('./partials/video/youtube.php',array('url'=>$this->input('youtubeVideo'),'width'=>'610','height'=>'420')) ?>
	</div>
<?php } ?>