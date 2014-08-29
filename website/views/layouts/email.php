<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<?php echo $this->headTitle() ?>
	<?php echo $this->headMeta() ?>	
	
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-15">
	<meta http-equiv="content-language" content="<?php echo $this->language?>">

	<link rel="stylesheet" type="text/css" media="screen" href="/css/screen.css">

	<?php echo $this->headScript() ?>
	<?php echo $this->headLink() ?>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<div id="logo">
				<a href="#">Company name</a>
			</div>
		</div>
		<div id="content">
			<p>You can edit this template in website/views/layouts/email.php</p>
			<?php echo $this->layout()->content ?>
		</div>
		<div id="footer">
			&copy; Company name <?= date('Y'); ?>
		</div>
	</div>
</body>
</html>