<!-- content -->
<div id="content">
	<!-- box-holder -->

	<div id="box-holder" class="box-holder <?php echo $this->view; ?>-list">			
		
		<?php if (count($this->list->getChildren())): ?>

			<?php echo $this->partial('move-item-items.tpl.php',array('list'=>$this->list,'moving'=>$this->moving)); ?>
			
		<?php else: ?>
			
			<p>There are no other items on this list, so you can't reorder it.</p>
		
		<?php endif; ?>
		
		<div class="cancel-link">
  		<a href="<?php echo $this->url(array('itemid'=>null,'listid'=>null,'action'=>'view','controller'=>'list', 'id'=>$this->list->getId())); ?>" class="cancel-icon icon">cancel</a>
		</div>

	</div>
</div>

<div id="sidebar">
  <!-- sidebar-box -->
  <div class="sidebar-box">
  	<div class="holder">
  		<div class="frame">
  			<h3>Change the position of an item</h3>
  			<div class="info-box info-box-2">
  				<h4>Using the mouse</h4>
  				<p>Moving your mouse up and down the list will reveal possible positions to place your item. Once you have positioned your item correctly, simple click to move it there.</p>
  			</div>
  			<div class="info-box info-box-2">
  				<h4>Using the keyboard</h4>
  				<p>You can also use the tab and shift+tab keys to cycle through the possible positions for your item.  Once you have positioned your item correctly, simple press the enter key to move it there.</p>
  			</div>
  			<div class="info-box info-box-2">
  				<h4>Change your mind</h4>
  				<p>Want to leave this item where is was?</p>
  				<span class="box-link"><a class="icon cancel-icon" href="<?php echo $this->url(array('itemid'=>null,'listid'=>null,'action'=>'view','controller'=>'list','id'=>$this->list->getId())); ?>">cancel</a></span>
  			</div>
  		</div>
  	</div>
  </div>
</div>