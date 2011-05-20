<!-- content -->
<div id="content">
	<?php echo $this->partial("tags-tags.tpl.php",array('tags'=>$this->tags)); ?>
</div>

<!-- sidebar -->
<div id="sidebar">
  
  <div class="sidebar-box">
  	<div class="holder">
  		<div class="frame">
  			<h3>Create tags</h3>
  			<div class="info-box">
					<?php echo $this->a('create tag',array('action'=>'create','destination'=>$this->getDestination()),array('attributes'=>array('class'=>'icon icon-create box-link'))); ?>
				</div>
  		</div>
  	</div>
  </div>
  
</div>