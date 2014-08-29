<?php
if ($this->config->search_document instanceof Document) {
	?>
	<div id="search">
		<form action="<?= $this->documentUrl($this->config->search_document) ?>" method="post">
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td><input type="search" name="zoek" placeholder="<?= $this->translate("Zoeken op trefwoord"); ?>"/>
					</td>
					<td><input type="submit" value=""/></td>
				</tr>
			</table>
		</form>
	</div>
<?php
}
