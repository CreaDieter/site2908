<?php if ($this->element instanceof Object_Vacancy && $this->url) { ?>
<tr class="newsletter_vacancy_title" >
	<td>
		<table>
			<tr>
				<td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;" >
					<a href="<?= $this->url; ?>"><?= $this->element->getTitle($this->language); ?></a>
				</td>
			</tr>
		</table>
	</td>
</tr>
<?php } ?>