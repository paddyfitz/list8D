<?php if (count($this->list->getChildren())): ?>

<?php if($this->insertingNew): ?>
<p>Select the position you would like to add the item.</p>
<?php else: ?>
<p>Select the position you would like to move the item to.</p>
<?php endif; ?>

<?php echo $this->renderInsertItemAt(); ?>

<?php else: ?>

<p>There are no other items on this list, so you can't reorder it.</p>

<?php endif; ?>

<?php if ($this->insertingNew): ?>
<?php echo $this->a("cancel",array("controller"=>"item","action"=>"remove",'itemid'=>$this->item->getId(),"confirmed"=>true)); ?>
<?php else: ?>
<?php echo $this->a("cancel",array("action"=>"view")); ?>
<?php endif;?>
