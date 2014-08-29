<?php
if ($this->config->disclaimer_document instanceof Document) {
	?>
	&ndash; <a href="<?=$this->documentUrl($this->config->disclaimer_document)?>"><?= $this->translate('Disclaimer')?></a>
<?php
}
