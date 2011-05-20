<div id="content" class="browse">
	<div class="filters">
		<?php foreach($this->filters as $title => $tags): ?>
	  	<?php if (!isset($this->appliedFilters[$title])): ?>
	  		<div class="filter">
		  		<h4><?php echo $title; ?></h4>
	  			<ul class="tags">
	  				<?php foreach($tags as $id => $tag) : ?>
	  					<?php if ($id!='limit'): ?>
	  						<li><a href="<?php echo $this->url(array('controller'=>'list','action'=>'search','filter_'.$title=>$id,'page'=>null)); ?>"><?php echo $tag['tagname']; ?></a> (<?php echo $tag['count']; ?>)</li>
	  					<?php elseif ($tag === true): ?>
	  						<li class="view-all"><a href="<?php echo $this->url(array('controller'=>'list','action'=>'viewalltags','namespace'=>$title,'destination'=>$this->getDestination())); ?>">view all</a></li>
	  					<?php endif; ?>
	  				<?php endforeach; ?>
		  		</ul>
		  	</div>
	  	<?php endif; ?>
	  		
	  <?php endforeach; ?>
	</div>
</div>