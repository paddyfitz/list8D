<div id="content">
	<!-- box-holder -->

	<div id="box-holder" class="box-holder">	
		<div class="list-item <?php echo $this->item->getType(); ?> open">
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
			  			<h2><?php echo $this->item->getDataValue("title"); ?>&nbsp;</h2>
  						<em class="details"><?php echo count($this->item->getChildren()); ?> item<?php if (count($this->item->getChildren())>1) echo "s"; ?> on list&nbsp;</em>
			  			
			  			<div class="box-content">
			  						  				
  							<div class="buttons-open">
								<?php if(!$this->readOnly):?>
				  					<a href="<?php echo $this->url(array('controller'=>'data','action'=>'edit','listid'=>$this->item->getId())); ?>?destination=<?php echo $this->getDestination(); ?>" title="edit item"><img alt='edit "<?php echo $this->item->getDataValue('title'); ?>"' src='<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/pencil.png' /><span class="text">edit item</span></a>
  							  		<a href="<?php echo $this->url(array('controller'=>'item','action'=>'remove',"listid"=>$this->item->getId(),'id'=>$this->item->getTrunkId())); ?>" title="remove from list"><img alt='remove "<?php echo $this->item->getDataValue('title'); ?>" from "<?php echo $this->item->getList()->getDataValue('title'); ?>"' src='<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/minus-circle.png' /><span class="text">remove list from list</span></a>
								<?php endif; ?>
  							</div>
			  				<?php foreach($this->item->getData() as $key => $data): ?>
  								<div class="info-box">
  									<?php if (!empty($data['title']) && !empty($data['type'])): ?>
  										<h4><?php echo $data['title']; ?></h4>
  										<div class="info">
  											<?php if (!empty($data['value']) || $data['type']=='boolean'): ?>
  												<?php if ($data['type']=='multiline'): ?>
		  											<p><?php echo preg_replace("/[\n\r]/","</p><p>",preg_replace("/[\n\r]{2}/","<br/>",$data['value'])); ?></p>
		  										<?php elseif ($data['type'] == 'boolean'): ?>
		  											<?php if (isset($data['value']) && $data['value']): ?>
		  												<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/tick-circle.png" alt="yes" />
		  											<?php else: ?>
		  												<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/cross-circle.png" alt="no" />
		  											<?php endif; ?>
		  										<?php else: ?>
		  											<p><?php echo $data['value']; ?></p>
	  											<?php endif; ?>
	  										<?php else: ?>
	  											<p class="none-defined">none defined</p>
	  										<?php endif; ?>
  										</div>
  										<!--<span class="box-link edit"><a href="#">edit description</a></span>-->
  									<?php endif; ?>
  								</div>
  							<?php endforeach; ?>
  							
  							<div class="buttons-open">
								<?php if(!$this->readOnly): ?>
				  					<a href="<?php echo $this->url(array('controller'=>'data','action'=>'edit','listid'=>$this->item->getId())); ?>?destination=<?php echo $this->getDestination(); ?>" title="edit list"><img alt='edit "<?php echo $this->item->getDataValue('title'); ?>"' src='<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/pencil.png' /><span class="text">edit list</span></a>
  							  		<a href="<?php echo $this->url(array('controller'=>'item','action'=>'remove','listid'=>$this->item->getId(),'id'=>$this->item->getTrunkId())); ?>" title="remove from list"><img alt='remove "<?php echo $this->item->getDataValue('title'); ?>" from "<?php echo $this->item->getList()->getDataValue('title'); ?>"' src='<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/minus-circle.png' /><span class="text">remove list from list</span></a>
  								<?php endif; ?>
							</div>
  							
			  			</div>
			  			
			  			
  						<a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$this->item->getTrunkId())); ?>" class="close" title="close and return to list"><img src="<?php echo $this->baseURl(); ?>/themes/gaia/images/icon-close.gif" alt="collapse" /></a>
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
	</div>
</div>

