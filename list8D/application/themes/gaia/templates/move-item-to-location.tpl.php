<a href="<?php echo $this->url(array(
		  'controller'=>'list',
      'action'=>'moveitemto',
      'onlist'=>$this->list->getId(),
	  	"position"=> $this->position,
    )); ?>" class="move-to-link"><span class="text">move item "<?php echo $this->item->getTitle()?>" to position <?php echo $this->position; ?> on list "<?php echo $this->list->getTitle(); ?>"</span>
	<div id="<?php if ($this->item->isList()) echo "list_"; else echo "item_"; echo $this->item->getId(); ?>" class="list-item <?php echo $this->item->getType(); if ($this->item->isList()) echo " open"; ?><?php if($this->item->getDataValue('part')) echo " part"; ?> move-to">

	<div class="tl">
  	<div class="tr">
  		<div class="tc">&nbsp;</div>
  	</div>
  </div>
  <div class="c">
  	<div class="cl">
  		<div class="clc">  						
  			<div class="type-icon">
  			
  			</div>
  			<div class="list-box-content">
  				<?php if ($this->item->isList()): ?>
		  			<div class="small-list-info"><p><?php echo $this->item->getDataValue("title"); ?> </p></div>
  				<?php else: ?>
		  			<div class="small-list-info">
		  				<p>
		  					<?php if ($this->item->getResource()->getDataValue("authors")) echo $this->item->getResource()->getDataValue("authors").", "; ?>
		  					<?php if ($this->item->getResource()->getDataValue("published")): ?>
		  						<strong><?php echo $this->item->getResource()->getDataValue("published").", "; ?></strong> 
		  					<?php endif; ?>
		  					<?php if ($this->item->getResource()->getDataValue("title")) echo $this->item->getResource()->getDataValue("title").","; ?>
		  				</p>
		  			</div>
	  			<?php endif; ?>
  				<?php if ($this->item->getType()=="Book" || $this->item->getType()=="Legacy") : ?>
	  				<h2><strong><?php echo $this->item->getResource()->getDataValue("published"); ?></strong> <?php echo $this->item->getResource()->getDataValue("title"); ?>&nbsp;</h2>
  					<em class="details"><?php echo $this->item->getResource()->getDataValue("authors"); ?>&nbsp;</em>
  				<?php elseif ($this->item->getType()=="List"): ?>	
	  				<h2><strong><?php echo $this->item->getDataValue("title"); ?></strong>&nbsp;</h2>
  					<em class="details"><?php echo count($this->item->getChildren()); ?> item<?php if (count($this->item->getChildren())!==1) echo "s"; ?> on list&nbsp;</em>
  				<?php elseif ($this->item->getType()=="Url"): ?>	
	  				<h2><strong><?php echo $this->item->getResource()->getDataValue("title"); ?></strong>&nbsp;</h2>
  					<em class="details"><?php echo $this->item->getResource()->getDataValue("url"); ?>&nbsp;</em>
  				<?php endif; ?>
  			</div>
  		</div>
  	</div>
  </div>
  <div class="b">
  	<div class="bl">
  		<div class="br">&nbsp;</div>
  	</div>
  </div>
</div>
</a>
  
  