<section id="eyecatcher">&nbsp;</section>
<div id="content">
	<article>
		<h1><?= $this->input('title') ?></h1>
		<?= $this->wysiwyg('content_text');?>
	</article>
	<article>
		<ul id="youtubeList">
		<?php while($this->block("youtube")->loop()) { ?>
			<li>
				<article>
					<?php if ($this->editmode) { ?>
					<strong><?= $this->translate('Plaats hier de link naar de youtube-video') ?></strong>
					<?= $this->input('youtubeVideo') ?>
					<?php } else if ($this->input('youtubeVideo') != '') { ?>
					<?= $this->partial('./partials/video/youtube.php',array('url'=>$this->input('youtubeVideo'),'width'=>'305','height'=>'210')) ?>
					<?php } ?>
				</article>
			</li>
		<?php } ?>
		</ul>
	</article>
</div>
