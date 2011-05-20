<h3>Details</h3>
<?php if (!isset($this->form)): ?>
	<h5>Display name</h5>
	<p><?php echo $this->user->getDisplayName(); ?></p>
	<h5>Login</h5>
	<p><?php echo $this->user->getLogin(); ?></p>
	<h5>email</h5>
	<p><?php echo $this->user->getEmail(); ?></p>
	<h5>Role</h5>
	<p><?php echo $this->user->getRole(); ?></p>
	<h5>Tags</h5>
	<?php if (count($this->user->getTags())): ?>
	  <ul class='tags'>
	  	<?php foreach($this->user->getTags() as $tag): ?>
	  		<li>
	  			<?php echo $tag; ?>
					<?php if($this->currentUser->isAllowed('user', 'tag') || $this->currentUser->isAllowed('user', 'tag-tagged') ): ?>
		  			<?php echo $this->a(
		  				"remove tag '$tag'",
							array('controller'=>'tag','action'=>'userdel','tagid'=>$tag->getId(),'userid'=>$this->user->getId(),'destination'=>array()),
							array('attributes'=>array('class'=>'just-icon icon-delete'))
						); ?>
					<?php endif; ?>
	  		</li>
	  	<?php endforeach; ?>
	  
	  </ul>
	<?php else: ?>
	  <p>This user has no tags yet</p>
	<?php endif; ?>
	<?php if( $this->currentUser->isAllowed('user', 'tag') || $this->currentUser->isAllowed('user', 'tag-tagged') ): ?>
		<?php echo $this->a('add tags',array('controller'=>'user','action'=>'addtag','id'=>$this->user->getId(),'tagid'=>null,'destination'=>array()),array('attributes'=>array('class'=>'icon icon-create'))); ?>
	<?php endif; ?>
	<div class='actions'>
		<?php if( $this->currentUser->isAllowed('user', 'edit') ): ?>
			<a class="icon edit-icon" href="<?php echo $this->url(array('controller'=>'user', 'action'=>'edit', 'id'=>$this->user->getId())); ?>">edit <?php echo $this->user->getDisplayName(); ?></a>
		<?php endif; ?>
	</div>
<?php else: ?>

	<?php echo $this->form; ?>

<?php endif; ?>