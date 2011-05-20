<div class="tl"><div class="tr">&nbsp;</div></div>
<div class="c">
  <div class="cl">
  	<div class="cr">
  		<div class="search-results-info">
  			<h3>
  				<a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$this->list->getId())); ?>">
    				<?php if ($this->list->getDataValue('code')) echo $this->list->getDataValue('code'); ?>
    				<?php if ($this->list->getDataValue('title') && $this->list->getDataValue('code')) echo " - "; ?>
    				<?php if ($this->list->getDataValue('title')) echo $this->list->getDataValue('title'); ?>
    			</a>
    		</h3>
  			<!--<em>Run by the School of Biology, conved by Fred Smith</em>-->
  		</div>
  		<a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$this->list->getId())); ?>" class="edit-icon icon">edit list</a>
  	</div>
  </div>
</div>
<div class="b"><div class="bl"><div class="br">&nbsp;</div></div></div>