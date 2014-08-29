<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<?php echo $this->headTitle() ?>
	<?php echo $this->headMeta() ?>	
	
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-15">
	<meta http-equiv="content-language" content="<?php echo $this->language?>">

	<?php if ($this->editmode) { ?>
		<link rel="stylesheet" type="text/css" media="screen" href="/css/newsletter_editmode.css">
	<?php } ?>

	<?php echo $this->headScript() ?>
	<?php echo $this->headLink() ?>
</head>
<body style="background-color: #f1f1f1" >

<table style="font-size: 12px; color: #333333;" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="50%" valign="top" style="background-color: #f1f1f1;"></td>

		<td width="650" valign="top" style="background-color: #ffffff;">

			<table style="background-color: #f1f1f1;" >
				<tr>
					<td>
						<hr style="width: 650px; visibility: hidden;" >
					</td>
				</tr>
			</table>
			<table style="width: 100%;" border="0" cellspacing="10" cellpadding="0">
				<tbody>
				<tr>
					<br>
					<h1 style="text-align: center; font-size: 36px;" >Newsletter</h1>
					<p style="text-align: center; font-style: italic; color: #666;" >You can edit this template in website/views/layouts/newsletter.php</p>
				</tr>
				<tr>
					<td style="border-top: 1px solid #efeae9; height: 1px; line-height: 1px;"> </td>
				</tr>
				<tr>
					<td>
						<?php echo $this->layout()->content ?>
					</td>
				</tr>
				<tr>
					<td>
						<p style="text-align: center;" >
							&copy; Company name <?= date('Y'); ?> - <a href="%UNSUBSCRIBELINK%"><?= $this->translate('Uitschrijven?') ?></a>
						</p>
					</td>
				</tr>
				</tbody>
			</table>
			<table style="background-color: #f1f1f1;" >
				<tr>
					<td>
						<hr style="width: 650px; visibility: hidden;" >
					</td>
				</tr>
			</table>
		</td>
		<td width="50%" valign="top" style="background-color: #f1f1f1;"></td>
		<td style="display:none;color:white;font-size:1px;" >%SENDER-INFO-SINGLELINE%</td>
	</tr>
</table>
</body>
</html>