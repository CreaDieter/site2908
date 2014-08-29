<?php
if ($this->appendQueryString) {
	$suffix = '&' . preg_replace("/page=\d+/", '', strip_tags($_SERVER['QUERY_STRING']));
	$suffix = preg_replace("/&+/", '&', $suffix);
} else {
	$suffix = '';
}
?>

<?php if ($this->pageCount > 1) { ?>
	<div class="pager">
		<ul>
			<?php if (isset($this->previous)) { ?>
				<li class="previous"><a href="<?= $this->urlprefix . $this->first . $suffix ?>" title="<?= $this->translate('pagination.preview') ?>" class="arrow_left">&larr;</a></li>
			<?php } ?>
			<?php foreach ($this->pagesInRange as $page) {
				if ($page != $this->current) {
					?>
					<li><a title="<?= $this->translate('pagination.page') ?> <?= $page ?>" href="<?= $this->urlprefix . $page . $suffix; ?>"><?= $page; ?></a></li>
				<?php } else { ?>
					<li><a href="#" class="current"><?= $page ?></a></li>
				<?php
				}
			} ?>
			<?php if (isset($this->next)) { ?>
				<li class="next"><a title="<?= $this->translate('pagination.next') ?>" href="<?= $this->urlprefix . $this->last . $suffix; ?>" class="arrow_right">&rarr;</a></li>
			<?php } ?>

		</ul>
	</div>
<?php } ?>