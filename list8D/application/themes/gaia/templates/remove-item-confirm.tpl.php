<div class="remove-item">
	<?php if($this->item->isList()): ?>
	
	<p>Are you sure you want to remove the list "<?php echo $this->item->getTitle(); ?>" from the list "<?php echo $this->item->getList()->getTitle(); ?>"?</p>
	
	<p class="not-reversible">All of this list's child lists and items will also be removed. This action is irreversible!</p>
	
	<?php else: ?>
	
	<p>Are you sure you want to remove the item "<?php echo $this->item->getTitle(); ?>" from the list "<?php echo $this->item->getList()->getTitle(); ?>"?</p>
	
	<p class="not-reversible">This action is irreversible!</p>
	
	<?php endif; ?>
	<?php
	$urlArray = array('controller'=>'item','action'=>'remove','id'=>$this->params['id']);
	if(isset($this->params['itemid']) && !empty($this->params['itemid'])) {
		$urlArray['itemid'] = $this->params['itemid'];
	}
	if(isset($this->params['listid']) && !empty($this->params['listid'])) {
		$urlArray['listid'] = $this->params['listid'];
	}
	?>
	<form action="<?php echo $this->url($urlArray); ?>" method="get" class='confirm'>
		<input type="submit" value="remove" name="confirmed" class="btn" />
	</form>
	
	<a href="<?php echo $this->url(array("controller"=>"list","action"=>'view','id'=>$this->params['id'])); ?>" class='cancel'>cancel</a>
</div>
