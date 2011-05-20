
<?php if (count($this->list->getChildren())): ?>
	<div <?php if (!$this->list->isNested()) echo 'id="list-items"'; ?> class="list-items<?php if ($this->list->isNested()) echo " nested"; else echo "" ?> not-moving"> 
 		<?php foreach($this->list->getChildren() as $item): ?>
 			<?php echo $this->partial('duplicate-item-select-item-item.tpl.php',array('item'=>$item, 'theAction'=>$this->theAction)); ?>			
	 	<?php endforeach; ?>
	</div>
<?php endif; ?>