<ul>
	<?php foreach ($this->tags as $tag): ?>
		<li>
			<?php echo $this->a($tag,array('tagid'=>$tag->getId(),'destination'=>$_GET['destination'])); ?>
			<?php if ($tag->getChildren()): ?>
				<?php echo $this->partial('add-tag-tags.tpl.php',array('tags'=>$tag->getChildren())); ?>
			<?php endif; ?>	
		</li>
	<?php endforeach; ?>
</ul>