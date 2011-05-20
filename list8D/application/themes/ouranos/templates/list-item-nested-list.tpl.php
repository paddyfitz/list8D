
<li class='item list <?php if (!count($this->list->getChildren())) echo 'empty'; ?>'>
	<div class="list-details">
		<h3>
		  <?php if ($this->list->getTitle()) echo $this->list->getTitle(); ?>
		</h3>
		<?php if ($this->list->getDataValue('public_notes')): ?>
	  	<p class="student-notes"><?php echo preg_replace("/[\n\r]/","</p><p>",preg_replace("/[\n\r]{2}/","<br/>",$this->list->getDataValue('public_notes'))); ?></p>
		<?php endif; ?>
	</div>
	<?php if ($this->list->getChildren()): ?>
		<div class="list-items">
			<div class="list-items-title">
				<h4><?php echo count($this->list->getChildren()); ?> item<?php if (count($this->list->getChildren())>1) echo "s"; ?> in sub list</h4> 
			</div>
			<div class="list-items-content">
				<?php echo $this->partial('list-items.tpl.php',array('list'=>$this->list)); ?>			
			</div>
		</div>
	<?php endif; ?>
	
</li>
