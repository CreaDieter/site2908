<div class="pagination">
	<div class="page_nav">
		<!-- First page link 
		<?php if (isset($this->previous)): ?>
			<a href="<?= $this->url(array('page' => $this->first)); ?>"><?=$this->translate("Start")?></a>
		<?php else: ?>
			<span class="disabled"><?=$this->translate("Start")?></span>
		<?php endif; ?>
		-->
		<!-- Previous page link -->
		<?php if (isset($this->previous)): ?>
			<a href="<?= $this->url(array('page' => $this->previous)); ?>">&lt; Previous</a>&nbsp;&nbsp;&nbsp;
		<?php else: ?>
			<span class="disabled">&lt; Previous</span>&nbsp;&nbsp;&nbsp;
		<?php endif; ?>
		<!-- Numbered page links -->
		<?php foreach ($this->pagesInRange as $page): ?>
			<?php if ($page != $this->current): ?>
				<a href="<?= $this->url(array('page' => $page)); ?>" class="pnr"><?= $page; ?></a>
			<?php else: ?>
				<span class="pnr disabled"><?= $page; ?></span>
			<?php endif; ?>
		<?php endforeach; ?>
		<!-- Next page link -->
		<?php if (isset($this->next)): ?>
			&nbsp;&nbsp;&nbsp;<a href="<?= $this->url(array('page' => $this->next)); ?>"><?= $this->translate('Next &gt;') ?></a>
		<?php else: ?>
			&nbsp;&nbsp;&nbsp;<span class="disabled"><?= $this->translate('Next &gt;') ?></span>
		<?php endif; ?>

		<!-- Last page link 
		<?php if (isset($this->next)): ?>
			<a href="<?= $this->url(array('page' => $this->last)); ?>"><?=$this->translate('End')?></a>
		<?php else: ?>
			<span class="disabled"><?=$this->translate('End')?></span>
		<?php endif; ?>
		-->
	</div>
	<div class="page_count">
		<?=$this->translate('Page')?> <?= $this->current; ?> <?=$this->translate('of')?> <?= $this->last; ?>
	</div>
</div>