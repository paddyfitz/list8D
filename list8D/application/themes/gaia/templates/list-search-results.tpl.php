<!-- content -->
<div id="content" class='search-results'>
	<?php if(isset($this->searchedFor)): ?>
		<p class='searched-for'>
		  You searched for
		  <?php foreach($this->searchedFor as $title => $value): ?>
		  	<?php if(!empty($title) && !empty($value)): ?>
		  		<strong><?php echo $title; ?>:</strong> <?php echo $value; ?><?php endif; ?><?php endforeach; ?>, we found <strong><?php echo $this->numberOfResults; ?></strong> results.
		</p>
	<?php endif; ?>
	
	<?php if(count($this->searchResults)): ?>
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
		<div class="cancel-link">
  		<a class="search-icon icon" href="<?php echo $this->url(array('controller'=>'list','action'=>'search')); ?>">search again</a>
		</div>
		
	<?php else: ?>
		<?php if(is_array($this->searchResults)): ?>
			<p class='no-results'>Sorry we couldn't find any lists that matched your search.</p>
			<h4>Try a more specific search</h4>
  		<p>Entering the entire title or code, is more likely to find you the module you are after.</p>
  	<?php else: ?>
  		<p>Enter the module code, part of or the whole title for the reading list you are looking for.</p>
		<?php endif; ?>
		<?php echo $this->tryAgain; ?>	
	<?php endif; ?>
</div>
<?php if(is_array($this->searchResults)): ?>
	<div id="sidebar">
	  <!-- sidebar-box -->
	  <div class="sidebar-box">
	  	<div class="holder">
	  		<div class="frame">
	  			<h3>Can't find what you're looking for?</h3>
	  			<div class="info-box info-box-2">
	  				<h4>Try a more specific search</h4>
	  				<p>Entering the entire title or code, is more likely to find you the module you are after.</p>
	  				<span class="box-link"><a class="icon search-icon" href="<?php echo $this->url(array('controller'=>'list','action'=>'search')); ?>" class="icon search-icon">search again</a></span>
	  			</div>
	  			<div class="info-box info-box-2">
	  				<h4>Tell a librarian</h4>
	  				<p>If you still can't find what you are looking for, they may be a problem with the system.</p>
	  				<span class="box-link support"><a class="icon email-icon" href="mailto:readinglisthelp@kent.ac.uk">email a librarian</a></span>
	  			</div>
	  		</div>
	  	</div>
	  </div>
	</div>
<?php endif; ?>