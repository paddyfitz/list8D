<div class="tl"><div class="tr">&nbsp;</div></div>
<div class="c">
  <div class="cl">
  	<div class="cr">
  		<div class="search-results-info">
  			<?php if ($this->list->getDataValue('code')): ?>
	  		  <div class='module-code<?php if ($this->list->getDataValue('credits')) echo " module-code-".$this->list->getDataValue('credits'); ?>'><?php echo $this->list->getDataValue('code'); ?></div>
	  		<?php endif; ?>
  			<h3>
  				<a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$this->list->getId())); ?>">
						<?php if ($this->list->getDataValue('title')) echo $this->list->getDataValue('title'); ?>
    			</a>
    		</h3>
    		<?php if ($this->list->getDataValue('convener') || $this->list->getDataValue('department') || $this->list->getDataValue('campus') || $this->list->getDataValue('week_beginning')): ?>
				  <h4>
				  	<?php $s = false; ?>
				  	<?php if ($this->list->getDataValue('convener')): ?><?php if ($s) echo 'c'; else echo 'C'; $s=true; ?>onvened by 
              <strong><?php 
$f=false; 
$co = $this->list->getDataValue('convener',false);
$co = is_array( $co ) ? $co : array( $co );
foreach($co as $user): ?><?php if ($f) { echo ", "; } $f=true; ?><?php echo $this->user($user); ?><?php endforeach; ?></strong><?php endif; ?><?php if ($this->list->getDataValue('department')): ?><?php if ($s) echo ', r'; else echo 'R'; $s=true; ?>un by the 	<strong><?php echo $this->list->getDataValue('department'); ?></strong> <?php endif; ?>
				  	<?php if ($this->list->getDataValue('campus')): ?><?php if ($s) echo 'a'; else echo 'Run a'; $s=true; ?>t <strong><?php echo $this->list->getDataValue('campus'); ?></strong> <?php endif; ?>
				  	<?php if ($this->list->getDataValue('week_beginning')): ?><?php if ($s) echo 's'; else echo 'S'; $s=true; ?>tarting <strong>week <?php echo $this->list->getDataValue('week_beginning'); ?></strong> <?php endif; ?>
				  </h4>
				<?php endif; ?>
  			<!--<em>Run by the School of Biology, conved by Fred Smith</em>-->
  		</div>
  		<a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$this->list->getId())); ?>" class="edit-icon icon">edit list</a>
  	</div>
  </div>
</div>
<div class="b"><div class="bl"><div class="br">&nbsp;</div></div></div>
