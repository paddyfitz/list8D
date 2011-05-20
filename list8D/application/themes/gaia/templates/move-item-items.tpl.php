<?php //echo $this->renderMoveToLocation($this->item,0); ?>

<div id="list-items" class="list-items<?php if ($this->list->isNested()) echo " nested"; ?> moving">
	<?php echo $this->partial('move-item-to-location.tpl.php',array('item'=>$this->moving,'position' => 0,'list'=>$this->list)); ?>
	<?php foreach($this->list->getChildren() as $item): ?>
  	<?php if($item->getId() != $this->moving->getId()) : ?>
			<?php echo $this->partial('move-item-item.tpl.php',array('item'=>$item,'moving'=>$this->moving)); ?>
	  	<?php echo $this->partial('move-item-to-location.tpl.php',array('item'=>$this->moving,'position' => $this->currentPosition < $item->getPosition() && $this->currentListId == $item->getListId() ? $item->getPosition() : $item->getPosition()+1,'list'=>$this->list)); ?>
  	<?php endif; ?>
	<?php endforeach; ?>
</div>