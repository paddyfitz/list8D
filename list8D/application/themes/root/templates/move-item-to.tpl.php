<ul> 
  <li>
    <?php if ($this->insertingNew) $type = 'add'; else $type = 'move'; ?>
    <?php echo $this->a("$type to the start of the list \"{$this->list->getTitle()}",array(
      'action'=>'moveitemto',
      'onlist'=>$this->list->getId(),
	  	"position"=>0,
    )); ?>
  </li>
  <?php foreach($this->list->getChildren() as $item): ?>

    <?php if($item->getId() != $this->item->getId()) : ?>
      <li>
        <?php echo $item->getTitle(); ?>

        <?php if ($item->isList()) : ?>
          <?php echo $this->renderInsertItemAt($item); ?> 

        <?php endif; ?> 
        <br/>
        <?php echo $this->a("$type to after \"{$item->getTitle()}\" on the list \"{$this->list->getTitle()}\"",array(
          'action'=>'moveitemto',
          'onlist'=>$this->list->getId(),
	  	  "position"=>$this->currentPosition < $item->getPosition() && $this->currentListId == $item->getListId() ? $item->getPosition() : $item->getPosition()+1,
        )); ?>
        
      </li>
    <?php endif; ?>
    
  <?php endforeach; ?>
	<?php if (isset($item)): ?>
  <li>
    <?php echo $this->a("$type to the end of the list \"{$this->list->getTitle()}",array(
      'action'=>'moveitemto',
      'onlist'=>$this->list->getId(),
	  	"position"=> $this->currentListId == $item->getListId() ? count($this->list->getChildren())-1 : count($this->list->getChildren()),
    )); ?>
  </li>
  <?php endif; ?>
</ul>

