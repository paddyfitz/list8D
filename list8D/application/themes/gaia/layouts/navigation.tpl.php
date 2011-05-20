<ul id="nav">  
	<?php if (count($this->user->getMyLists())): ?>
  	<li class="my-lists">
  		<a href="<?php echo $this->url(array('controller'=>'user','action'=>'mylists')); ?>"><span><em>My Lists</em></span></a>
  		<div class="sub-nav">
				<ul class="my-lists">
					<?php foreach($this->user->getMyLists() as $list): ?>
					  <li>
					  	<a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$list->getId())); ?>">
					  		<?php echo $list->getCode(); ?> - <?php echo $list->getTitle(); ?>
					  	</a>
					  </li>
					<?php endforeach; ?>
				</ul>
  			<div class="bottom-bg">&nbsp;</div>
			</div>
  	</li>
	<?php endif; ?>
  <?php if (count($this->user->getRecentLists())): ?>
  	<li class="recent-lists">
  		<a href="<?php echo $this->url(array('controller'=>'user','action'=>'recentlists')); ?>"><span><em>Recent Lists</em></span></a>
  		<div class="sub-nav">
				<ul class="recent-lists">
					<?php foreach($this->user->getRecentLists() as $list): ?>
					  <li>
					  	<a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$list->getId())); ?>">
					  		<?php echo $list->getCode(); ?> - <?php echo $list->getTitle(); ?>
					  	</a>
					  </li>
					<?php endforeach; ?>
				</ul>
  			<div class="bottom-bg">&nbsp;</div>
			</div>
  	</li>
  <?php endif; ?>
  <?php if ($this->user->isAllowed("user",'view') || $this->user->isAllowed("tag",'view') || $this->user->isAllowed("list", "create")) : ?>
  	<li class="administration">
  		<a href="#"><span><em>Administration</em></span></a>
  		<div class="sub-nav">
  			<ul>
  				<?php if ($this->user->isAllowed("user","view")): ?>
	  				<li class='users'><a href="<?php echo $this->url(array('controller'=>'user','action'=>'index', 'id'=>null)); ?>">Users</a></li>
  				<?php endif; ?>
  				<?php if ($this->user->isAllowed("tag","view")): ?>
	  				<li class='tags'><a href="<?php echo $this->url(array('controller'=>'tag','action'=>'index', 'id'=>null)); ?>">Tags</a></li>
	  			<?php endif; ?>
  				<?php if ($this->user->isAllowed("list","create")): ?>
	  				<li class='create'><a href="<?php echo $this->url(array('controller'=>'list','action'=>'create', 'id'=>null)); ?>">Create new list</a></li>
	  			<?php endif; ?>
  			</ul>
  			<div class="bottom-bg">&nbsp;</div>
  		</div>
  	</li>
  <?php endif; ?>
</ul>