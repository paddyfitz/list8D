<?php $this->inlineScript()->appendFile('jquery.inview.js'); ?>
<?php $this->inlineScript()->appendFile('actions_follow_screen.js'); ?>
<!-- content -->
<div id="content">
	<!-- box-holder -->
	<div id="box-holder" class="box-holder <?php echo $this->view; ?>-list">
		<?php echo $this->flashMessages(); ?>
		<?php echo $this->partial('list-years.tpl.php',array('list'=>$this->list)); ?>
		<?php echo $this->partial('list-items.tpl.php',array('list'=>$this->list,'readOnly'=>$this->readOnly, 'params'=>$this->params)); ?>
		<?php //echo $this->renderListItems($this->list); ?>
	</div>
</div>

<!-- sidebar -->
<div id="sidebar">
  <!-- sidebar-box -->
  
  <?php if (!$this->readOnly):?>
  <!-- create-item -->
  <div id="create-item">
  	<span class="open-link">Create new item</span>
  	<div class="sub-create-item">
  		<div class="holder">
  			<strong class="head-create">Create new item</strong>
  			<ul class="create-types">
  				<?php foreach($this->contentTypes as $type): ?>
	  				<li class="<?php echo $type->getType(); ?>">
  						<a href="<?php echo $this->url(array('controller'=>'list','action'=>'additem','id'=>$this->list->getId(),'type'=>$type->getType())); ?>">
  							<strong><?php echo $type->getTypeName(); ?></strong>
  							<span><?php echo $type->getExpanded(); ?></span>
  						</a>
  					</li>
					<?php endforeach; ?>
  			</ul>
  		</div>
  		<div class="bottom-bg">&nbsp;</div>
  	</div>
  </div>
  <?php endif; ?>
  <!-- sidebar-box -->
  <div class="sidebar-box">
  	<div class="holder">
  		<div class="frame">
  			<h3>List details</h3>
				<div class="info-box">
					<h4>Tags</h4>
					<?php if ($this->list->getTags()): ?>
						<ul class='tags'>
							<?php foreach($this->list->getTags() as $tag): ?>
								<li>
									<?php echo $tag; ?>
									<?php if (!$this->readOnly):?>
										<?php echo $this->a(
	  										"remove tag '$tag'",
	  										array('controller'=>'tag','action'=>'listdel','tagid'=>$tag->getId(),'listid'=>$this->list->getId(),'destination'=>array()),
	  										array('attributes'=>array('class'=>'just-icon icon-delete'))
	  									); ?>
									<?php endif; ?>
									<?php //echo $this->url(array('controller'=>'tag','action'=>'listdel','tagid'=>$tag->getId(),'listid'=>$this->list->getId(),'destination'=>array())); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php if (!$this->readOnly): ?>
						<?php echo $this->a('add tags',array('controller'=>'list','action'=>'addtag','destination'=>$this->getDestination()),array('attributes'=>array('class'=>'icon icon-create box-link'))); ?>
					<?php endif; ?>
				</div>
  			<?php foreach($this->list->getData() as $key => $data): ?>
  				<?php if($key !='title' && $key != 'code' && isset($data['title'])): ?>
  					<div class="info-box">
  						<h4><?php echo $data['title']; ?></h4>
  						<?php if (!empty($data['value']) || $data['type']=='boolean'): ?>
  							<?php if ($data['type']=='multiline'): ?>
		  						<p><?php echo preg_replace("/[\n\r]/","</p><p>",preg_replace("/[\n\r]{2}/","<br/>",$data['value'])); ?></p>
		  					<?php elseif ($data['type'] == 'boolean'): ?>
		  						<?php if (isset($data['value']) && $data['value']): ?>
		  							<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/tick-circle.png" alt="yes" />
		  						<?php else: ?>
		  							<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/cross-circle.png" alt="no" />
		  						<?php endif; ?>
		  					<?php elseif ($data['type'] == 'users'): ?>
		  						<ul>
		  							<?php foreach($data['value'] as $user_id): ?>
		  								<li>
		  									<?php echo $this->user($user_id); ?>
		  								</li>
		  							<?php endforeach; ?>
		  						</ul>
		  					<?php else: ?>
		  						<p><?php echo $data['value']; ?></p>
	  						<?php endif; ?>
	  					<?php else: ?>
	  						<p class="none-defined">none defined</p>
	  					<?php endif; ?>
  						<!--<span class="box-link edit"><a href="#">edit description</a></span>-->
  					</div>
  				<?php endif; ?>
  			<?php endforeach; ?>
			<?php if (!$this->readOnly): ?>
  				<div class="info-box">
	  				<a href="<?php echo $this->url(array("controller"=>'data','action'=>'edit','listid'=>$this->list->getId(),'destination'=>array())); ?>" class="box-link icon-edit icon">edit list</a>
	  			</div>
			<?php endif ;?>
		  </div>
  	</div>
  </div>
  <!-- sidebar-box -->
  <div id="advanced-actions" class="sidebar-box">
  	<div class="holder">
  		<div class="frame">
  			<h3>Actions</h3>
	  		<div class="info-box">
	  			<ul class="links">
		  			<?php if (!$this->readOnly): ?>
						<li><a class="icon node-copy-child-icon" href="<?php echo $this->url(array('controller'=>'list','action'=>'duplicate','id'=>$this->list->getId())); ?>">duplicate item or sublist</a></li>
	  				<?php endif; ?>
					<li><a class="icon node-copy-child-across-icon" href="<?php echo $this->url(array('controller'=>'list','action'=>'duplicatetolist','id'=>$this->list->getId())); ?>">duplicate item or sublist to another list</a></li>
	  				<li><a class="icon node-copy-icon" href="<?php echo $this->url(array('controller'=>'list','action'=>'duplicatecontents','id'=>$this->list->getId())); ?>">duplicate list contents to another list</a></li>
						<?php if($this->currentUser->getRole() == "sysAdmin" || $this->currentUser->getRole() == "librarian") : ?>
	  				<li><a class="icon node-copy-icon" href="<?php echo $this->url(array('controller'=>'list','action'=>'duplicatetonew','id'=>$this->list->getId())); ?>">duplicate list contents to a new list</a></li>
						<?php endif;?>
	  				
	  			</ul>
	  		</div>
  		</div>
  	</div>
  </div>
  <!-- sidebar-box -->
  <div class="sidebar-box">
  	<div class="holder">
  		<div class="frame">
  			<h3>Key</h3>
  			<ul class="create-types">
  				<?php foreach($this->contentTypes as $type): ?>
	  				<li class="<?php echo $type->getType(); ?>">
						<?php if (!$this->readOnly): ?>
  							<a href="<?php echo $this->url(array('controller'=>'list','action'=>'additem','id'=>$this->list->getId(),'type'=>$type->getType())); ?>">
  						<?php else: ?>
							<div class="readOnly">
						<?php endif; ?>
							<strong><?php echo $type->getTypeName(); ?></strong></span>
  							<span><?php echo $type->getExpanded(); ?></span>
  						<?php if (!$this->readOnly): ?>
							</a>
						<?php else: ?>
							</div>
						<?php endif; ?>
  					</li>
					<?php endforeach; ?>
  			</ul>
  		</div>
  	</div>
  </div>
</div>
