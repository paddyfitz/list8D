<?php if($this->item->isList()): ?>

<p>Are you sure you want to remove the list "<?php echo $this->item->getTitle(); ?>" from the list "<?php echo $this->item->getList()->getTitle(); ?>"?</p>

<p>All of this lists child list's and items will also be removed. This action is irreversible!</p>

<?php else: ?>

<p>Are you sure you want to remove the item "<?php echo $this->item->getTitle(); ?>" from the list "<?php echo $this->item->getList()->getTitle(); ?>"?</p>

<p>This action is irreversible!</p>

<?php endif; ?>

<form action="<?php echo $this->url(array('controller'=>'item','action'=>'remove')); ?>" method="get">
	<input type="submit" value="Remove" name="confirmed" />
</form>

<?php echo $this->a("cancel", array("controller"=>"list","action"=>'view')); ?>

