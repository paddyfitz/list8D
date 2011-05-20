<ul>
	<?php foreach ($this->tags as $tag): ?>
		<li>
			<?php echo $tag; ?>
			<?php //echo $this->a($tag,array('tagid'=>$tag->getId(),'destination'=>$_GET['destination'])); ?>
			<?php if ($tag->getChildren()): ?>
				<?php echo $this->partial('tags-tags.tpl.php',array('tags'=>$tag->getChildren())); ?>
			<?php endif; ?>	
		</li>
	<?php endforeach; ?>
</ul>