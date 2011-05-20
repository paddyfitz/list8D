<div id="all-tags">
	<ul class="tags">
  	<?php foreach($this->tags as $id => $tag) : ?>
  		<?php if ($id!='limit'): ?>
  			<li><a href="<?php echo $this->url(array('controller'=>'tag','action'=>'search','filter_'.$this->namespace=>$id,'page'=>null)); ?>"><?php echo $tag['tagname']; ?></a> (<?php echo $tag['count']; ?>)</li>
	  	<?php elseif ($tag === true): ?>
  			<li class="view-all"><a href="<?php echo $this->url(array('controller'=>'tag','action'=>'viewalltags','namespace'=>$this->namespace)); ?>">view all</a></li>
  		<?php endif; ?>
	  <?php endforeach; ?>
	</ul>

	<a href="<?php echo $this->getDestination(); ?>" class="icon icon-cancel">cancel</a>
</div>