<!-- content -->
<div id="content">
	<div class="select-list-to-duplicate-to">
		<?php if (isset($this->searchResults)): ?>
			<div class="search">
			  <?php if (count($this->searchResults) && $this->searchResults): ?>
			  	<h3>Search results</h3>
			  	<p class="searched-for">You searched for <strong>title or code:</strong> <?php echo $this->searchedFor; ?></p>
			  	<ul class="lists">
			  		<?php foreach($this->searchResults as $list): ?>
			  			<li class='list'>
			  				<a href="<?php echo $this->url(array('controller'=>'list','action'=>$this->theAction,'tolist'=>$list->getId()), null, false, true, false); ?>"><?php echo $list->getDataValue('code'); ?><?php if ($list->getDataValue('code') && $list->getDataValue('title')) echo " - "; ?><?php echo $list->getDataValue('title'); ?></a>
			  			</li>
			  		<?php endforeach; ?>
			  	</ul>
			  	<div class="cancel-link">
			  		<a href="<?php echo $this->url(array('controller'=>'list','action'=>$this->theAction)); ?>" class="search-icon icon">search again</a>
					</div>
			  <?php else: ?>
			  	<p class="searched-for">You searched for <strong>title or code:</strong> <?php echo $this->searchedFor; ?></p>
			  	<p class="no-results">Sorry we couldn't find any lists that matched your search.</p>
			  	<h4>Try a more specific search</h4>
			  	<p>Entering the entire title or code, is more likely to find you the module you are after.</p>
			  	<?php echo $this->searchForm; ?>
			  <?php endif; ?>
			</div>		
		<?php else: ?>
			<?php if (isset($this->searchForm)): ?>
				<?php
					$this->searchForm->setDecorators(array(
  				  'FormElements',
					  array(array('data'=>'HtmlTag'),array('tag'=>'table','class'=>'form-table')),
					  'Form',
					));
					foreach ($this->searchForm->getElements() as $key => $element) {
						if ($key == "submit") {
							$element->setDecorators(array(
					  		'ViewHelper',
					  		'Description',
					  		'Errors',
					  		array(array('data'=>'HtmlTag'), array('tag' => 'td','align'=>'center','colspan'=>2,'valign'=>'bottom')),
					  	  array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
					  	));
						} else {
					  	$element->setDecorators(array(
					  		'ViewHelper',
					  		'Description',
					  		'Errors',
					  		array(array('data'=>'HtmlTag'), array('tag' => 'td','align'=>'left','valign'=>'top')),	  		
					  		array('Label', array('tag' => array('tag' => 'td','align'=>'left','class'=>'label'))),
								array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
					  	));
					  }
					}
				?>
				<div class="search">
					<h3>Search</h3>
					<?php echo $this->searchForm; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if (!isset($this->searchResults) || !count($this->searchResults)): ?>
			<?php if (count($this->myLists)): ?>
				<div class="recent-lists">
					<h3>My Lists</h3>
					<ul class='my-lists lists'>
						<?php foreach($this->myLists as $list): ?>
							<li class='list'>
								<a href="<?php echo $this->url(array('controller'=>'list','action'=>$this->theAction,'tolist'=>$list->getId()), null, false, true, false); ?>"><?php echo $list->getDataValue('code'); ?><?php if ($list->getDataValue('code') && $list->getDataValue('title')) echo " - "; ?><?php echo $list->getDataValue('title'); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			
			<?php if (count($this->recentLists)): ?>
				<div class="recent-lists">
					<h3>Recent Lists</h3>
					<ul class='recent-lists lists'>
						<?php foreach($this->recentLists as $list): ?>
							<li class='list'>
								<a href="<?php echo $this->url(array('controller'=>'list','action'=>$this->theAction,'tolist'=>$list->getId()), null, false, true, false); ?>"><?php echo $list->getDataValue('code'); ?><?php if ($list->getDataValue('code') && $list->getDataValue('title')) echo " - "; ?><?php echo $list->getDataValue('title'); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		
	</div>
</div>

<!-- sidebar -->
<div id="sidebar">
	<!-- sidebar-box -->
  <div class="sidebar-box">
  	<div class="holder">
  		<div class="frame">
  			<h3>Select a list to duplicate into</h3>
	  		<div class="info-box info-box-2">
	  			<p>Please choose a list you would like to duplicate the item "<?php echo $this->item->getTitle(); ?>" into.</p>
	  			<p>Once you select a list we will add your item to the bottom and take you to it.</p>
	  			<p>If you are trying to duplicate a large nested list it may take some time please be patient.</p>
	  			<span class="box-link support"><a class="icon cancel-icon" href="<?php echo $this->url(array('controller'=>'list', 'action'=>'view', 'id'=>$this->params['id'])); ?>">cancel</a></span>
	  		</div>
  		</div>
  	</div>
  </div>
</div>