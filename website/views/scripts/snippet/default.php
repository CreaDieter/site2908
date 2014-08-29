<?php if ($this->editmode) { ?>
	<p>
		<label>Titel</label>
		<?php echo $this->input('advtitel')?>
	</p>
	<p>
		<label>Inhoud</label>
		<?php echo $this->input('advcontent')?>
	</p>
	<p>
		<label>Link (document)</label>
		<?php echo $this->href('advlink')?>
	</p>
		
<?php } else { 
	$link  = $this->href("advlink");
	$titel = $this->input('advtitel');
	$content = $this->input('advcontent')
	?>
	
	
	<h3><a href="<?= $link ?>"><?=$this->truncate($titel,25,'...');?></a></h3>
	<p><a href="<?= $link ?>"><?=$this->truncate($content,25,'...');?></a></p>

<?	} ?>