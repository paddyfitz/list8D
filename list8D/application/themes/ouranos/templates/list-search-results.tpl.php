<div id="content" class="column-right">
	<div class="main-column">
		<?php if(isset($this->searchedFor) && $this->searchCount): ?>
		  <?php if (count($this->searchedFor)): ?>
		  	<p class='searched-for'>
		    	You searched for
		    	<?php foreach($this->searchedFor as $title => $value): ?>
		    		<?php if(!empty($title) && !empty($value)): ?>
		    			<strong><?php echo $title; ?>:</strong> <?php echo $value; ?>, 
		    		<?php endif; ?>
		    	<?php endforeach; ?>
			    we found <strong><?php echo $this->searchCount; ?></strong> result<?php if ($this->searchCount !== 1) echo 's'; ?>
			  </p>
				<!--<a class="icon-search icon search-again" href="<?php echo $this->url(array('controller'=>null,'action'=>null)); ?>">search again</a>-->
			<?php else: ?>
			
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if(count($this->searchResults)): ?>
		  	<div class="pager-center">
				<?php echo $this->pager($this->page,$this->pages); ?>
			</div>
			<div class="clear-both"></div>
			<ul class="search-results lists">
		  		<?php foreach($this->searchResults as $list): ?>
		  			<li class="search-result list">
		  		  	<?php echo $this->partial('lists-list.tpl.php',array('list'=>$list)); ?>
		  			</li>
		  		<?php endforeach; ?>
		  	</ul>
		  	<div class="pager-center">
			  	<?php echo $this->pager($this->page,$this->pages); ?>
			</div>
		<?php else: ?>
		  <?php if(is_array($this->searchResults)): ?>
		  	<p class='no-results'>Sorry we couldn't find any lists that matched your search.</p>
		  	<h4>Try a more specific search</h4>
				<p>Entering the entire title or code, is more likely to find you the module you are after.</p>
			<?php else: ?>
				<p>Enter the module code, part of or the whole title for the reading list you are looking for.</p>
		  <?php endif; ?>
		  <form action="<?php echo $this->url(array('action'=>'search')); ?>" method="get" id="search-form" >
				<input class="search" type="text" value="<?php if (isset($this->q)) echo $this->q; else echo "module code or title"; ?>" name="q" id="q" />
				<input class="submit" type="submit" value="Search" />
			</form>
			<script type="text/javascript">
				$().ready(function(){
					q = $("[name=q]");
					if (q.eq(q.length).val()=='module code or title')
						q.addClass('default');
					q.focus(function() {
						if ($(this).val()=='module code or title') {
							$(this).val("").removeClass("default");
						}
					});
					q.blur(function() {
						if ($(this).val()=='') {
							$(this).val("module code or title").addClass("default");
						}
					});
				});
			</script>
		<?php endif; ?>
	</div>
	
	<div class="right-column">
		<?php if (count($this->filters) || count($this->appliedFilters)): ?>
			<h3>Filters</h3>
			<div class="filters">
				<?php if (count($this->appliedFilters)): ?>
					<h4>Applied Filters</h4>
					<ul>
						<?php foreach($this->appliedFilters as $key => $filter): ?>
							<li><strong><?php echo $key; ?>: </strong> <a href="<?php echo $this->url(array('filter_'.$key=>null,'page'=>null)); ?>" class="icon-right icon-delete"><?php echo $filter; ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php foreach($this->filters as $title => $tags): ?>
					<?php if (!isset($this->appliedFilters[$title])): ?>
						<h4><?php echo $title; ?></h4>
						<ul>
							<?php foreach($tags as $id => $tag) : ?>
								<?php if ($id!='limit'): ?>
									<li><a href="<?php echo $this->url(array('filter_'.$title=>$id,'page'=>null)); ?>"><?php echo $tag['tagname']; ?></a> (<?php echo $tag['count']; ?>)</li>
								<?php elseif ($tag === true): ?>
  								<li class="view-all"><a href="<?php echo $this->url(array('action'=>'viewalltags','namespace'=>$title,'destination'=>$this->getDestination())); ?>">view all</a></li>
  							<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>