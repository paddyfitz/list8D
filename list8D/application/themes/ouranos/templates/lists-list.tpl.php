<?php
	if ($this->list->getDataValue('code') && $this->list->getDataValue('year')) {
		$url = $this->url(array('controller'=>'front','action'=>'list','getBy'=>'code','id'=>$this->list->getDataValue('code'),'year'=>$this->list->getDataValue('year'),'q'=>null),null, false, true, true);
	} else {
		$url = $this->url(array('controller'=>'front','action'=>'list','code'=>$this->list->getId(),'q'=>null),null, false, true, true);
	}
?>

<a href="<?php echo $url; ?>">
	<?php if ($this->list->getDataValue('code')): ?>
		<?php echo $this->list->getDataValue('code'); ?>
	<?php endif; ?>
	<?php if ($this->list->getDataValue('code') && $this->list->getDataValue('title')): ?>
	 - 
	<?php endif; ?>
	<?php if ($this->list->getDataValue('title')): ?>
		<?php echo $this->list->getDataValue('title'); ?>
	<?php endif; ?>	
</a>