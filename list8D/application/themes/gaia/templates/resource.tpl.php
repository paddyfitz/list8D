<!-- content -->
<div id="content">
	<!-- box-holder -->

	<div id="box-holder" class="box-holder">	
		<div class="list-item <?php echo $this->resource->getType(); ?> open">
			<div class="tl">
			  <div class="tr">
			  	<div class="tc">&nbsp;</div>
			  </div>
			</div>
			<div class="c">
			  <div class="cl">
			  	<div class="clc">
				  	<div class="type-icon">
						</div>
			  		<div class="list-box-content">
			  			<?php if ($this->resource->getType()=="Book" || $this->resource->getType()=="Legacy") : ?>
	  						<h2><strong><?php echo $this->resource->getDataValue("published"); ?></strong> <?php echo $this->resource->getDataValue("title"); ?>&nbsp;</h2>
  							<em class="details"><?php echo $this->resource->getDataValue("authors"); ?>&nbsp;</em>
			  			<?php elseif ($this->resource->getType()=="PhysicalMedia" || $this->resource->getType()=="LegacyPhysicalMedia") : ?>
	  						<h2><strong><?php echo $this->resource->getDataValue("published"); ?></strong> <?php echo $this->resource->getDataValue("title"); ?>&nbsp;</h2>
  							<em class="details"><?php echo $this->resource->getDataValue("authors"); ?>&nbsp;</em>
			  			<?php elseif ($this->resource->getType()=="Journal" || $this->resource->getType()=="LegacyJournal" || $this->resource->getType()=="Article") : ?>
	  						<h2><strong><?php echo $this->resource->getDataValue("published"); ?></strong> <?php echo $this->resource->getDataValue("title"); ?>&nbsp;</h2>
  							<em class="details"><?php echo $this->resource->getDataValue("authors"); ?>&nbsp;</em>
  						<?php elseif ($this->resource->getType()=="Url"): ?>	
	  						<h2><strong><?php echo $this->resource->getDataValue("title"); ?></strong>&nbsp;</h2>
  							<em class="details"><?php echo $this->resource->getDataValue("url"); ?>&nbsp;</em>
  						<?php endif; ?>
			  			<div class="box-content<?php if($this->resource->getDataValue('Amazon_thumbnailUrl_large') || $this->resource->getType() == 'Url') echo " content-img"; ?>">
			  				<?php if(($this->resource->getDataValue('Amazon_thumbnailUrl_large'))) : ?>
			  					<img class='img-book' src="<?php echo $this->resource->getDataValue('Amazon_thumbnailUrl_large'); ?>" alt="book cover" />
								<?php elseif($this->resource->getType() == "Url"): ?>
									<?php
									$thumboo_api = "50be95df663d62cf93bc285444e37660";
									$thumboo_url = $this->resource->getDataValue("url");
									$thumoo_params = "u=".urlencode("http://".$_SERVER["HTTP_HOST"].
									$_SERVER["REQUEST_URI"])."&su=".urlencode($thumboo_url)."&c=medium&api=".$thumboo_api;
									$image = file_get_contents("http://counter.goingup.com/thumboo/snapshot.php?".$thumoo_params);
									$image = str_replace("<img ","<img class='img-url' ",$image);
									echo $image; ?>
			  				<?php endif; ?>
			  				
  							<div class="buttons-open">

  							  <?php	$title = $this->resource->getTitle(); ?>
  							  
								  <?php if($this->resource->getDataValue('KentVoyager_url')): ?>
								  	<a href="<?php echo $this->resource->getDataValue('KentVoyager_url'); ?>" title="view resource in the catalogue"><img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/book-closed-tag.png" alt="view resource in the catalogue" /><span class="text">view resource in the catalogue</span></a>
								  <?php elseif (is_string($this->resource->getDataValue('url',false))) : ?>
  							  	<a href="<?php echo $this->resource->getDataValue('url'); ?>" title="view online resource"><img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/globe-green-arrow.png" alt="view online resource" /><span class="text">view online resource</span></a>
  							  <?php endif; ?>
  							</div>
			  				
  							<?php foreach($this->resource->getData() as $key => $data): ?>
  								<?php if (!empty($data['title']) && !empty($data['type']) && !empty($data['value'])): ?>
  									<div class="info-box">
  										<h4><?php echo $data['title']; ?></h4>
  										<div class="info">
  											<?php if (!empty($data['value']) || $data['type']=='boolean'): ?>
  												<?php if ($data['type']=='multiline'): ?>
		  											<p><?php echo preg_replace("/[\n\r]/","</p><p>",preg_replace("/[\n\r]{2}/","<br/>",$data['value'])); ?></p>
		  										<?php elseif ($data['type'] == 'boolean'): ?>
		  											<?php if (isset($data['value']) && $data['value']): ?>
		  												<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/tick-circle.png" alt="yes" />
		  											<?php else: ?>
		  												<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/cross-circle.png" alt="no" />
		  											<?php endif; ?>
		  										<?php else: ?>
		  											<p><?php echo $data['value']; ?></p>
	  											<?php endif; ?>
	  										<?php else: ?>
	  											<p class="none-defined">none defined</p>
	  										<?php endif; ?>
  										</div>
  										<!--<span class="box-link edit"><a href="#">edit description</a></span>-->
	  								</div>
 									<?php endif; ?>
  							<?php endforeach; ?>
  							
  							<?php $url = $this->resource->getDataValue('url',false); ?>
  							<?php if($url): ?>
									<div class="info-box">
										<h4>Link<?php if(is_array($url) && count($url)>1) echo 's'; ?></h4>

										<div class="info">
											<?php if (is_string($url)): ?>
												<a href="<?php echo $url; ?>">view online resource</a>
											<?php else: ?>
												<ul>
													<?php foreach($url as $url2): ?>
														<li>
															<?php if (is_string($url2)) : ?>
																<a href="<?php echo $url2; ?>">view resource</a>
															<?php elseif (is_string($url2['url'])): ?>
																<a href="<?php echo $url2['url']; ?>"><?php echo $url2['text']; ?></a>
															<?php elseif(is_array($url2['url'])): ?>
																<?php echo $url2['label']; ?>:
																<ul>
																	<?php foreach($url2['url'] as $url3): ?>
																		<li>
																			<a href="<?php echo $url3['url']; ?>"><?php echo $url3['text']; ?></a>
																		</li>
																	<?php endforeach; ?>
																</ul>
															<?php endif; ?>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>
								
  							<div class="buttons-open">

  							  <?php	$title = $this->resource->getTitle(); ?>
								  <?php	$key = 'listid'; ?>
  							  <?php if($this->resource->getDataValue('KentVoyager_url')): ?>
								  	<a href="<?php echo $this->resource->getDataValue('KentVoyager_url'); ?>" title="go to resource"><img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/book-closed-tag.png" alt="go to resource" /><span class="text">view resource in the catalogue</span></a>
								  <?php elseif (is_string($this->resource->getDataValue('url',false))) : ?>
  							  	<a href="<?php echo $this->resource->getDataValue('url'); ?>" title="go to resource"><img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/globe-green-arrow.png" alt="go to resource info" /><span class="text">view online resource</span></a>
								  <?php elseif (is_array($this->resource->getDataValue('url',false))) : ?>

  							  <?php endif; ?>
  							</div>
  							
  							
  							
								
			  			</div>
			  			<?php if (isset($_GET['destination'])): ?>
	  						<a href="<?php echo $this->getDestination(); ?>" class="close" title="close and return to list"><img src="<?php echo $this->baseURl(); ?>/themes/gaia/images/icon-close.gif" alt="collapse" /></a>
	  					<?php endif; ?>
  						
			  		</div>
			  	</div>
			  </div>
			</div>
			<div class="b">
			  <div class="bl">
			  	<div class="br">&nbsp;</div>
			  </div>
			</div>
		</div>
	</div>
</div>

<?php if ($this->currentUser->isAllowed('report','view')): ?>
	<!-- sidebar -->
	<div id="sidebar">
	  <div class="sidebar-box">
	  	<div class="holder">
	  		<div class="frame">
	  			<h3>Places this resource appears</h3>
					<div class="info-box">
						<?php $items = $this->resource->getItems(); ?>
	  				<?php if (count($items)): ?>
	  					<ul class="lists">
			  				<?php foreach($items as $item): ?>
									<li><a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunk()->getId())); ?>#item_<?php echo $item->getId(); ?>"><strong><?php echo $item->getTrunk()->getTitle(); ?></strong><br/><?php echo $item->getTrunk()->getDataValue('code'); ?> - Item <?php echo $item->getId(); ?></a></li>
	  						<?php endforeach; ?>
	  					</ul>
						<?php else: ?>
		  				<p>This resource is on no other lists.</p>
	  				<?php endif; ?>
	  			
	  			</div>
	  		</div>
	  	</div>
		</div>
	</div>
<?php endif; ?>