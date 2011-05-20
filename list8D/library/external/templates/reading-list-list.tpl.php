<div id="list8D">
	<?php if (isset($this->nested) && $this->nested) echo "<li class='item list'>"; ?>
	
	<h3>
	  <?php if (!empty($this->list->code)) echo $this->list->code; ?>
	  <?php if (!empty($this->list->code) && !empty($this->list->title)) echo " - "; ?>
	  <?php if (!empty($this->list->title)) echo $this->list->title; ?>
	</h3>
	
	<?php if (!empty($this->list->public_notes)): ?>
	  <p><?php echo preg_replace("/[\n\r]/","</p><p>",preg_replace("/[\n\r]{2}/","<br/>",$this->list->public_notes)); ?></p>
	<?php endif; ?>
	
	
	
	<?php if (!empty($this->list->children)): ?>
		<?php if (!isset($this->nested) || !$this->nested): ?>
			<h4>Resources</h4>
		<?php endif; ?>
		
		<?php if (!isset($this->nested) || !$this->nested): ?>
			<?php echo $this->render("reading-list-pager.tpl.php"); ?>
		<?php endif; ?>

		<?php echo $this->items; ?>
		
		<?php if (!isset($this->nested) || !$this->nested): ?>
			<?php echo $this->render("reading-list-pager.tpl.php"); ?>
		<?php endif; ?>
		
	<?php elseif (!isset($this->nested) || !$this->nested) : ?>
	  <p>This list is currently empty. If the course conveyer is not using list8D the list may exist in another place. If you can't find the reading list please contact course administrator.</p>
	<?php endif; ?>
	
	<?php if (isset($this->nested) && $this->nested) echo "</li>"; ?>
</div>