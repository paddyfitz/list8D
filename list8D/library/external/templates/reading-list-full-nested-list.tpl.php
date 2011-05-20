
<li class='item list'>
	
	<h3>
	  <?php if (!empty($this->list->title)) echo $this->list->title; ?>
	</h3>
	
	<?php if (!empty($this->list->public_notes)): ?>
	  <p><?php echo preg_replace("/[\n\r]/","</p><p>",preg_replace("/[\n\r]{2}/","<br/>",$this->list->public_notes)); ?></p>
	<?php endif; ?>
	
	<?php if (!empty($this->list->children)): ?>
		

		<?php echo $this->render('reading-list-full.tpl.php'); ?>
				
	<?php elseif (!isset($this->nested) || !$this->nested) : ?>
	  <p>This list is currently empty. If the course conveyer is not using list8D the list may exist in another place. If you can't find the reading list please contact course administrator.</p>
	<?php endif; ?>
	
</li>
