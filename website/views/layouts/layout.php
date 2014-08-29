<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<?php echo $this->headTitle() ?>
	<?php echo $this->headMeta() ?>

	<meta http-equiv="content-type" content="text/html; charset=iso-8859-15">
	<meta http-equiv="content-language" content="<?php echo $this->language ?>">

	<link rel="stylesheet" type="text/css" media="screen" href="/css/screen.css">
	<link rel="stylesheet" type="text/css" media="print" href="/css/print.css">

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<!--	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>-->


	<?php
	// bootstrap?
	$this->partial('partials/bootstrap.php');
	?>

	<?php echo $this->headLink() ?>
	<script src="/js/storme/core/core.js" type="text/javascript"></script>
	<?php $this->headScript()->captureStart() ?>
	$(function($){
	Storme.loadClass('general');
	Storme.callAction('<?= $this->getParam('controller') ?>','<?= $this->getParam('action') ?>');
	});
	<?php $this->headScript()->captureEnd() ?>
	<?= $this->headScript(); ?>
</head>
<body>
<div class="container">
	<header id="header">
		<div id="logo">
			<h1><a href="<?= $this->home() ?>"><?= Website_Config::getWebsiteConfig()->site_title ?></a></h1>
		</div>
        <div id="taalkeuze">
		<?= $this->partial('partials/language.php', array('document' => $this->document)) ?></div>
		<?= $this->partial('partials/header/search.php', array('config' => $this->config)) ?>
        <?= $this->partial('partials/navigation.php') ?>
    </header>

	<div id="content">
			<?php echo $this->layout()->content ?>
		</div>
</div>
<div id="footer">
	<div class="container">
		<p class="text-muted">&copy; <?= Website_Config::getWebsiteConfig()->site_title ?> <?= date('Y'); ?>
		<?= $this->partial('partials/footer/disclaimer.php', array('config' => $this->config)) ?></p>
	</div>
</div>
</body>
</html>