
<ul>
	<?php foreach ($this->users as $user): ?>
		<li><a href="<?php echo $this->url(array('controller'=>'user','action'=>'view','id'=>$user->getId())); ?>"><?php echo $user->getDisplayName(); ?></a></li>
	<?php endforeach; ?>
</ul>