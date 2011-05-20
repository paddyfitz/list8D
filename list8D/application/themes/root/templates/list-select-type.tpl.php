<ul>
	<?php foreach($this->types as $type): ?>
		<li><?php echo $this->a($type->getTypeName(), array('id'=>$this->list->getId(),'controller'=>'list','action'=>'addmyitem','type'=>$type->getType())); ?></li>
	<?php endforeach; ?>
</ul>