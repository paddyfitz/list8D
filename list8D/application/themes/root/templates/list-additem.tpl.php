<?php echo $this->searchForm; ?>

<?php if ($this->searchResults): ?>
	<?php echo $this->renderAddItem($this->searchResults,array('merge metatrons'=>false)); ?>	
<?php endif; ?>

<?php echo $this->a("cancel",array('action'=>'view')); ?>
