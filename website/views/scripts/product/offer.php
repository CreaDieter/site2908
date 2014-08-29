<h1><?= sprintf($this->translate('Offerteaanvraag voor %s'),$this->productTitle != '' ? $this->productTitle : $this->translate('een product')); ?></h1>

<?php if ($this->product && $this->product instanceof Document_Page) { ?>
<a href="<?= $this->product->getHref(); ?>"><?= $this->translate('Terug naar het product') ?></a>
<?php } ?>

<?php if (count($this->messages)) { ?>
	<ul id="messages">
		<?php foreach ($this->messages as $message) : ?>
			<li><?php echo $this->escape($message); ?></li>
		<?php endforeach; ?>
	</ul>
<?php } else {
	echo $this->form;
} ?>