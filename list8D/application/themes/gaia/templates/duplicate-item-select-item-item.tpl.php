<div id="<?php if ($this->item->isList()) echo "list_"; else echo "item_"; echo $this->item->getId(); ?>" class="list-item <?php echo $this->item->getType(); if ($this->item->isList()) echo " open "; else echo " not-list "; ?><?php if($this->item->getDataValue('part')) echo " part"; ?> sortable-item">
					

	
	<div class="list-box-content">
	  	<div class="small-list-info">
	  		<p>
	  			<a href="<?php echo $this->url(array('controller'=>'list','action'=>$this->theAction,$this->item->isList() ? "copylist" : "copyitem" =>$this->item->getId()), false, false, true, false); ?>">
	  			  <?php if ($this->item->isList()): ?>
	  					<?php echo $this->item->getDataValue("title"); ?>
	 					<?php else: ?>
	  					<?php echo $this->reference($this->item); ?>	
	  				<?php endif; ?>
	  				<?php if ($this->item->getDataValue("core_text")): ?>
	  				  <img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/star.png" alt="Core text" title="Core text" />	
	  				<?php endif; ?>
	  				<?php if ($this->item->getDataValue("recommended_for_purchase")): ?>
	  				  <img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/currency.png" alt="Recommended for purchase" title="Recommended for purchase" />		
	  				<?php endif; ?>
	  				<?php if ($this->item->getDataValue("private_notes") || $this->item->getDataValue("public_notes")): ?>
	  				   <img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/sticky-note-pin.png" alt="Notes" class="notes-icon" />
	  				<?php endif; ?> 
	  			</a>
	  		</p>
	  		<?php if ($this->item->getDataValue("private_notes") || $this->item->getDataValue("public_notes")): ?>
	  			<div class="notes">
	  				<?php if ($this->item->getDataValue("public_notes")): ?>
	  			  	<h4>Notes for students</h4>
	  			    <p><?php echo preg_replace("/[\n\r]{2}/","</p><p>",preg_replace("/[\n\r]/","<br/>",$this->item->getDataValue("public_notes"))); ?></p>
						<?php endif; ?>
	  			  <?php if ($this->item->getDataValue("private_notes")): ?>
	  			    <h4>Notes for librarians</h4>
	  			    <p><?php echo preg_replace("/[\n\r]/","</p><p>",preg_replace("/[\n\r]{2}/","<br/>",$this->item->getDataValue("private_notes"))); ?></p>
	  			  <?php endif; ?>
					</div>
	  		<?php endif; ?>
	  	</div>
	  	
	  <?php if($this->item->isList()): ?>
	  	<?php echo $this->partial("duplicate-item-select-item-items.tpl.php",array("list"=>$this->item,"nested"=>true,'theAction'=>$this->theAction)); ?>
	  <?php endif; ?>
	  
	</div> 
  	<div class="type-icon">
	  <?php if ($this->item->isList()) $type = "list"; else $type = 'item'; ?>
	  <!--<a href="#" class="move"><span class='text'>move "<?php echo $this->item->getTitle(); ?>"</span></a>-->
	</div>		
</div>
  
  