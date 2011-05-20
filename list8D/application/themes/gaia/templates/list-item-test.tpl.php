<div id="<?php if ($this->item->isList()) echo "list_"; else echo "item_"; echo $this->item->getId(); ?>" class="list-item <?php echo $this->item->getType(); if ($this->item->isList()) echo " open "; else echo " not-list "; ?><?php if($this->item->getDataValue('part')) echo " part"; ?> sortable-item">
  <div class="tl">
  	<div class="tr">
  		<div class="tc">&nbsp;</div>
  	</div>
  </div>
  <div class="c">
  	<div class="cl">
  		<div class="clc"> 
 				<div class="type-icon">
  				<?php if ($this->item->isList()) $type = "list"; else $type = 'item'; ?>
  				<a href="<?php echo $this->url(array('controller'=>'list','action'=>'moveitem',$type."id"=>$this->item->getId())); ?>" class="move"><span class='text'>move "<?php echo $this->item->getTitle(); ?>"</span></a>
  			</div>
  			<div class="list-box-content">
	 			<?php if($this->item->isList()): ?>
 						<?php echo $this->partial("list-items-test.tpl.php",array('list'=>$this->item)); ?>
 				<?php endif; ?>
				
  		</div>
  	</div>
  </div>
  <div class="b">
  	<div class="bl">
  		<div class="br">&nbsp;</div>
  	</div>
  </div>
</div>