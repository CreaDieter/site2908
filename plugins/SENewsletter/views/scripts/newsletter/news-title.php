<?php if ($this->element instanceof Object_News && $this->url) { ?>
<tr class="newsletter_news_title" >
	<td>
		<table>
			<tr>
				<td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;" >
					<a href="<?= $this->url; ?>"><?= $this->element->getDate()->toString(
							"dd.MM.Y"
						) ?> . <?= $this->element->getTitle($this->language); ?></a>
				</td>
			</tr>
		</table>
	</td>
</tr>
<?php } ?>