
<?php $this->headLink()->appendStylesheet('list-items.css'); ?>
<div style="margin-left: 0px" <?php if (!$this->list->isNested()) echo 'id="list-items"'; ?> class="list-items">
	<?php if (count($this->list->getChildren())): ?>

 		<?php foreach($this->list->getChildren() as $item): ?>
 			<?php echo $this->partial('list-item-test.tpl.php',array('item'=>$item)); ?>			
 		<?php endforeach; ?>

	<?php else: ?>
	  <p class='no-items>There are no items on this list yet.</p>
	<?php endif; ?>
</div>