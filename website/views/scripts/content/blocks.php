<section id="eyecatcher">&nbsp;</section>
<div id="content">
	<article>
		<h1><?= $this->input('title') ?></h1>
		<section id="wall">
			<!-- Here come the bricks! -->
			<?= $this->areablock(
				"anotherbrick",
				[
					"allowed"           => [
						"default-content",
						"default-h1",
						"default-h2",
						"default-h3",
						"default-youtube",
						"default-image",
						"default-attachments",
						"default-marquee",
						"default-blink",
					],
					"areablock_toolbar" => [
						"title"               => "",
						"width"               => 230,
						"x"                   => 20,
						"y"                   => 50,
						"xAlign"              => "right",
						"buttonWidth"         => 218,
						"buttonMaxCharacters" => 35
					],
					"params"            => [
						"editmode"       => $this->editmode,
					],
				]
			);
			?>
		</section>
	</article>
</div>