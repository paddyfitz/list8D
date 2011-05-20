<?php $this->inlineScript()->appendFile('jquery.inview.js'); ?>
<?php $this->inlineScript()->appendFile('actions_follow_screen_resource_search.js'); ?>

<form action="<?php echo $this->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'],'multiple' => 'true', 'token' => $this->token, 'position' => $this->position));?>" method="post">
	<div id="content">
		<!-- search-results -->
		<div class="search-results<?php if (!$this->searchResults) echo " no-results" ?>">
			<p class='searched-for'>
				You searched for
				<?php foreach($this->formValues as $value): ?>
					<?php if(!empty($value[1]) && !empty($value[2])): ?>
						<strong><?php echo $value[1]; ?></strong> <?php echo $value[2]; ?> 
					<?php endif; ?>
				<?php endforeach; ?>
			</p>
			<?php if (!$this->searchResults): ?>
				<h2>No results found</h2>
				<p class='no-results'>Sorry we couldn't find any resources for your search. Filling in more fields or using unique fields like isbn will help us find what you are looking for.</p> 
			<?php else: ?>
				<div class="results-pager">
					<?php foreach($this->formValues as $formValue): ?>
						<input type="hidden" name="<?php echo $formValue[0];?>" value="<?php echo $formValue[2]; ?>" /> 
					<?php endforeach; ?>
					<input type="hidden" name="type" value="<?php echo $this->resourceType; ?>" />
					<p>
					<?php if($this->from != 1):?>
						<span class="box-link"><input type="submit" class="icon fewer-results" value="&laquo; prev" name="fewerresults"/></span>
					<?php endif;?>
					Showing results <?php echo $this->from; ?> to <?php echo $this->currentLimit;?> from each source
					<input type="hidden" name="limit" value="<?php echo $this->currentLimit; ?>" />
					<?php if($this->more):?>
						<span class="box-link"><input type="submit" class="icon more-results" value="next &raquo;" name="moreresults"/></span></p>
					<?php endif;?>
				</div>
				<div class="clearer"></div>
				<?php foreach($this->searchResults as $metatron => $results):  ?>
					<?php if (count($results)): ?>	
						<h3>From <?php echo call_user_func(array($metatron, "getName")); ?></h3>
						<?php foreach($results as $key => $result): ?>
							<div class="search-result">
							  <div class="tl"><div class="tr">&nbsp;</div></div>
							  <div class="c">
							  	<div class="cl">
							  		<div class="cr">
										<div class="resource-results-checkbox">
											<input type="checkbox" name="chosen[<?php echo $metatron; ?>][<?php echo $key; ?>]" id="chosen[<?php echo $metatron; ?>][<?php echo $key; ?>]" />
											<input type="hidden" name="type" id="type" value="<?php echo $result->getType();?>" />
											<input type="hidden" name="tokenValues" id="tokenValues" value="<?php echo $this->tokenValues;?>" />
										</div>
							  			<div class="visual">
							  				<?php if ($result->getDataValue("GoogleBooks_thumbnailUrl")): ?>
								  				<img src="<?php echo $result->getDataValue("GoogleBooks_thumbnailUrl"); ?>" alt="<?php echo $result->getDataValue("title"); ?> cover" />
								  			<?php elseif ($result->getDataValue("Amazon_thumbnailUrl_small")): ?>
								  				<img src="<?php echo $result->getDataValue("Amazon_thumbnailUrl_small"); ?>" alt="<?php echo $result->getDataValue("title"); ?> cover" />
								  			<?php elseif($result->getType()=='Url'): ?>
								  				<?php
														$thumboo_api = "50be95df663d62cf93bc285444e37660";
														$thumboo_url = $result->getDataValue("url");
														$thumoo_params = "u=".urlencode("http://".$_SERVER["HTTP_HOST"].
														$_SERVER["REQUEST_URI"])."&su=".urlencode($thumboo_url)."&c=medium&api=".$thumboo_api;
														$image = file_get_contents("http://counter.goingup.com/thumboo/snapshot.php?".$thumoo_params);
														$image = str_replace("<img ","<img class='img-url' ",$image);
														echo $image; ?>
								  			<?php else: ?>
								  				<img src="<?php echo $this->baseUrl(); ?>/themes/gaia/images/no-cover-art.png" alt="sorry no cover art avaliable" />
								  			<?php endif; ?>
							  			</div>
							  			<div class="add-item-results">
							  				<h2><strong><?php echo $result->getDataValue("publication_date"); ?></strong> <?php echo $result->getDataValue("title"); ?></h2>
							  				<?php if ($result->getType() == 'Article'): ?>
													<p>in <em><?php echo $result->getDataValue("journal"); ?></em> <?php echo $result->getDataValue("volume"); ?>, <?php echo $result->getDataValue("issue"); ?> </p>
												<?php endif; ?>
												<?php if ($result->getType() == 'Url'): ?>
							  					<p><?php echo $result->getDataValue("url"); ?></p>
							  				<?php else: ?>
								  				<p><?php echo $result->getDataValue("authors"); ?></p>
								  			<?php endif; ?>
							  				<span class="text-number"><?php echo $result->getDataValue("isbn10"); ?></span>
							  				<?php if (is_numeric($result->getDataValue("edition"))) {
							  								if ($result->getDataValue("edition") == 1)
							  									$sup = 'st';
							  								elseif ($result->getDataValue("edition") == 2)
							  									$sup = 'nd';
							  								elseif ($result->getDataValue("edition") == 3)
							  									$sup = 'rd';
							  								else
								  								$sup = 'th';
								  							$edition = $result->getDataValue("edition")."<sup>$sup</sup> edition"; 
								  						} else {
										  					$edition = $result->getDataValue("edition");
										  				} ?>
							  				<span class="text-number"><?php echo $edition; ?></span>
							  			</div>
							  			<ul class='links'>
											<?php if ($result->getDataValue("KentVoyager_url")):?>
												<li><a href="<?php echo $result->getDataValue('KentVoyager_url');?>" target="_blank" class="icon-tag-label-arrow icon">view Resource in Library Catalogue</a></li>
											<?php endif; ?>
							  				
												<li>
								<a href="<?php echo $this->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'],'resourceid'=>$key,'metatron'=>$metatron,'token'=>$this->token,'then'=>null, 'position'=>$this->position)); ?>" class="icon-add icon">add to list</a></li>
							  				<li><a href="<?php echo $this->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'],'resourceid'=>$key,'metatron'=>$metatron,'token'=>$this->token,'then'=>'searchagain', 'position'=>$this->position)); ?>" class="icon-arrow-circle-225-left icon">add and search again</a></li>
							  				<li><a href="<?php echo $this->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'],'resourceid'=>$key,'metatron'=>$metatron,'token'=>$this->token,'then'=>'edit', 'position'=>$this->position)); ?>" class="icon-edit icon">add and edit</a></li>

							  			</ul>
							  		</div>
							  	</div>
							  </div>
							  <div class="b"><div class="bl"><div class="br">&nbsp;</div></div></div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<div class="results-pager-bottom">
					<?php foreach($this->formValues as $formValue): ?>
						<input type="hidden" name="<?php echo $formValue[0];?>" value="<?php echo $formValue[2]; ?>" /> 
					<?php endforeach; ?>
					<input type="hidden" name="type" value="<?php echo $this->resourceType; ?>" />
					<p>
					<?php if($this->from != 1):?>
						<span class="box-link"><input type="submit" class="icon fewer-results" value="&laquo; prev" name="fewerresults"/></span>
					<?php endif;?>
					Showing results <?php echo $this->from; ?> to <?php echo $this->currentLimit;?> from each source
					<input type="hidden" name="limit" value="<?php echo $this->currentLimit; ?>" />
					<?php if($this->more):?>
						<span class="box-link"><input type="submit" class="icon more-results" value="next &raquo;" name="moreresults"/></span></p>
					<?php endif;?>
				</div>
				<div class="clearer"></div>
	
			<?php endif; ?>
			<div class="cancel-link">
				
	  			<a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'type'=>null)); ?>" class="cancel-icon icon">cancel</a>
	  			<a href="<?php echo $this->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'],'type'=>$this->params['type'], 'position'=>$this->position)); ?>" class="search-icon icon">search again</a>
			</div>
		</div>
	</div>
	  
	<div id="sidebar">
	  <!-- sidebar-box -->
	  <div class="sidebar-box">
	  	<div class="holder">
	  		<div class="frame">
	  			<h3>Can't find what you're looking for?</h3>
				
	  			<div class="info-box info-box-2">
	  				<h4>Try a more specific search</h4>
	  				<p>Filling in more fields or using unique fields like isbn will help us find what you are looking for.</p>
	  				<span class="box-link"><a class="icon search-icon" href="<?php echo $this->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'],'type'=>$this->params['type'], 'position' => $this->position)); ?>" class="icon search-icon">search again</a></span>
	  			</div>
	  			<div class="info-box info-box-2">
	  				<h4>Tell a librarian</h4>
	  				<p>If you still can't find what you are looking for, it's probably not in the catalogue.</p>
	  				<span class="box-link support"><a class="icon email-icon" href="mailto:readinglisthelp@kent.ac.uk">email a librarian</a></span>
	  			</div>
	  			<div class="info-box info-box-2">
	  				<h4>Add resource manually</h4>
	  				<p>If you really can't find the resource you are looking for you can add it manually.</p>
	  				<span class="box-link box-link-cancel"><a class="icon add-icon" href="<?php echo $this->url(array('controller'=>'list','action'=>'addmyitem','id'=>$this->params['id'], 'position' => $this->position)); ?>">add resource manually</a></span>
	  			</div>
	  			<div class="info-box info-box-2">
	  				<h4>Return to Reading List</h4>
	  				<p>Cancel and return to the reading list.</p>
	  				<span class="box-link box-link-cancel"><a class="icon cancel-icon" href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'type'=>null)); ?>">cancel</a></span>
	  			</div>
	  		</div>
	  	</div>
	  </div>
	  
		<?php if ($this->searchResults): ?>
			<div class="sidebar-box" id="actions">
	  		<div class="holder">
	  			<div class="frame">
	  				<h3>Actions</h3>
			  		<div class="info-box">
							<ul class='links'>
								<li class="clearfix">
									<span class="btn with-icon"><input type="submit" class="icon icon-add-selected" value="Add selected"/></span>
								</li>
								<li class="clearfix">
									<span class="btn with-icon"><input type="submit" class="icon icon-arrow-circle-225-left" value="Add selected and search again" name="searchagain"/></span>
								</li>
							</ul>
	  				</div>
	  			</div>
	  		</div>
	  	</div>
	  <?php endif; ?>
	</div>
</form>

