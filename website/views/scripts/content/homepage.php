<section id="eyecatcher">
	<section class="inner">
		<article>
			<h1><?= $this->input('title') ?></h1>
			<h2><?= $this->input('subtitle')?></h2>
		</article>
	</section>
</section>
<div id="content">
	<article>
		<?= $this->wysiwyg('content_text');?>
		<section id="nieuws">
			<?= $this->partial('./partials/news/widget.php',array('news'=>$this->news,'document'=>$this->document)) ?>
		</section>
	</article>
</div>