<?php 
if ($this->editmode || $this->noLayout) {
	if (!$this->noLayout) $style = "style='margin-top:100px'";
}

?>
<div <?=$style?>>
	<?php echo $this->wysiwyg("myWysiwyg") ?>
</div>


