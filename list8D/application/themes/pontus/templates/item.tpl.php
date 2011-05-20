<!-- content -->
<div id="content">
	<!-- box-holder -->
	<div id="box-holder" class="box-holder">	
		<div class="list-item <?php echo $this->item->getType(); ?> open">
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
			  			<?php if ($this->item->getType()=="Book" || $this->item->getType()=="BookChapter" || $this->item->getType()=="Legacy") : ?>
	  						<h2><strong><?php echo $this->item->getResource()->getDataValue("published"); ?></strong> <?php echo $this->item->getResource()->getDataValue("title"); ?>&nbsp;</h2>
  							<em class="details"><?php echo $this->item->getResource()->getDataValue("authors"); ?>&nbsp;</em>
			  			<?php elseif ($this->item->getType()=="PhysicalMedia" || $this->item->getType()=="LegacyPhysicalMedia") : ?>
	  						<h2><strong><?php echo $this->item->getResource()->getDataValue("published"); ?></strong> <?php echo $this->item->getResource()->getDataValue("title"); ?>&nbsp;</h2>
  							<em class="details"><?php echo $this->item->getResource()->getDataValue("authors"); ?>&nbsp;</em>
			  			<?php elseif ($this->item->getType()=="Journal" || $this->item->getType()=="LegacyJournal" || $this->item->getType()=="Article") : ?>
	  						<h2><strong><?php echo $this->item->getResource()->getDataValue("published"); ?></strong> <?php echo $this->item->getResource()->getDataValue("title"); ?>&nbsp;</h2>
  							<em class="details"><?php echo $this->item->getResource()->getDataValue("authors"); ?>&nbsp;</em>
  						<?php elseif ($this->item->getType()=="List"): ?>	
	  						<h2><strong><?php echo $this->item->getDataValue("title"); ?></strong>&nbsp;</h2>
  							<em class="details"><?php echo count($this->item->getChildren()); ?> item<?php if (count($this->item->getChildren())!==1) echo "s"; ?> on list&nbsp;</em>
  						<?php elseif ($this->item->getType()=="Url"): ?>	
	  						<h2><strong><?php echo $this->item->getResource()->getDataValue("title"); ?></strong>&nbsp;</h2>
  							<em class="details"><?php echo $this->item->getResource()->getDataValue("url"); ?>&nbsp;</em>
  						<?php endif; ?>
			  			<div class="box-content<?php if($this->item->getResource()->getDataValue('Amazon_thumbnailUrl_large') || $this->item->getType() == 'Url') echo " content-img"; ?>">
			  				<?php if(($this->item->getResource()->getDataValue('Amazon_thumbnailUrl_large'))) : ?>
			  					<img class='img-book' src="<?php echo $this->item->getResource()->getDataValue('Amazon_thumbnailUrl_large'); ?>" alt="book cover" />
								<?php elseif($this->item->getType() == "Url"): ?>
									<?php
									$thumboo_api = "50be95df663d62cf93bc285444e37660";
									$thumboo_url = $this->item->getResource()->getDataValue("url");
									$thumoo_params = "u=".urlencode("http://".$_SERVER["HTTP_HOST"].
									$_SERVER["REQUEST_URI"])."&su=".urlencode($thumboo_url)."&c=medium&api=".$thumboo_api;
									$image = file_get_contents("http://counter.goingup.com/thumboo/snapshot.php?".$thumoo_params);
									$image = str_replace("<img ","<img class='img-url' ",$image);
									echo $image; ?>
			  				<?php endif; ?>
			  				
  							<div class="buttons-open">
								<?php if(!$this->readOnly): ?>
				  					<a href="<?php echo $this->url(array('controller'=>'data','action'=>'edit','itemid'=>$this->item->getId(),'destination'=>array())); ?>" title="edit item"><img alt='edit "<?php echo $this->item->getResource()->getDataValue('title'); ?>"' src='<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/pencil.png' /><span class="text">edit item</span></a>
								<?php endif; ?>
  							  <?php	$title = $this->item->getResource()->getTitle(); ?>
  							  <?php $url = $this->item->getResource()->getDataValue('meta_url',false); ?>
  							  <?php if ($url) : ?>
  							  	<?php if (is_array($url)) $url = reset($url); ?>
  							  	<?php if (is_array($url['url'])) $url = reset($url['url']); ?>
  							  	<?php if (is_string($url)) $url = array('url'=>$url,'text'=>'external system'); ?>
  							  	<a href="<?php echo $url['url']; ?>" title="view online resource">
  							  		<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/tag-label-arrow.png" alt="more information on this resource" />
  							  		<span class="text">view resource in <?php echo $url['text']; ?></span>
  							  	</a>
  							  <?php endif; ?>
							  <?php if(!$this->readOnly): ?>
  							  	<a href="<?php echo $this->url(array('controller'=>'item','action'=>'remove',"itemid"=>$this->item->getId(),'id'=>$this->item->getTrunkId())); ?>" title="remove from list"><img alt='remove "<?php echo $title; ?>" from "<?php echo $this->item->getList()->getDataValue('title'); ?>"' src='<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/minus-circle.png' /><span class="text">remove item from list</span></a>
  							  <?php endif; ?>
							</div>
			  				<?php foreach($this->item->getData() as $key => $data): ?>
  								<div class="info-box">
  									<?php if (!empty($data['title']) && !empty($data['type'])): ?>
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
  									<?php endif; ?>
  								</div>
  							<?php endforeach; ?>
  							
  							<div class="buttons-open">
								<?php if(!$this->readOnly):?>
				  					<a href="<?php echo $this->url(array('controller'=>'data','action'=>'edit','itemid'=>$this->item->getId(),'destination'=>$this->getDestination())); ?>" title="edit item"><img alt='edit "<?php echo $this->item->getResource()->getDataValue('title'); ?>"' src='<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/pencil.png' /><span class="text">edit item</span></a>
								<?php endif; ?>
  							  <?php	$title = $this->item->getResource()->getTitle(); ?>
								  <?php	$key = 'listid'; ?>
  							  <?php $url = $this->item->getResource()->getDataValue('meta_url',false); ?>
  							  <?php if ($url) : ?>
  							  	<?php if (is_array($url)) $url = reset($url); ?>
  							  	<?php if (is_array($url['url'])) $url = reset($url['url']); ?>
  							  	<?php if (is_string($url)) $url = array('url'=>$url,'text'=>'external system'); ?>
  							  	<a href="<?php echo $url['url']; ?>" title="view online resource">
  							  		<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/tag-label-arrow.png" alt="more information on this resource" />
  							  		<span class="text">view resource in <?php echo $url['text']; ?></span>
  							  	</a>
  							  <?php endif; ?>
								<?php if(!$this->readOnly): ?>
  							  		<a href="<?php echo $this->url(array('controller'=>'item','action'=>'remove','itemid'=>$this->item->getId(),'id'=>$this->item->getTrunkId())); ?>" title="remove from list"><img alt='remove "<?php echo $title; ?>" from "<?php echo $this->item->getList()->getDataValue('title'); ?>"' src='<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/minus-circle.png' /><span class="text">remove item from list</span></a>
  								<?php endif;?>
							</div>
  							
				  			<h3 id="resource">Resource information</h3>
  							<?php foreach($this->item->getResource()->getData() as $key => $data): ?>
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
		  										<?php elseif ($data['type']=='link'): ?>
		  											<?php if (is_string($data['value'])): ?>
															<a href="<?php echo $data['value']; ?>"><?php echo $data['value']; ?></a>
														<?php elseif (is_array($data['value'])): ?>
		  										
		  												<ul>
																<?php foreach($data['value'] as $url2): ?>
																	<li>
																		<?php if (is_string($url2)) : ?>
																			<a href="<?php echo $url2; ?>"><?php echo $url2; ?></a>
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
														<?php elseif(is_string($data['value']['url'])): ?>
															<a href="<?php echo $data['value']['url']; ?>"><?php if (!empty($data['value']['text'])) echo $data['value']['text']; else echo $data['value']['url'] ?></a>	
														<?php endif; ?>
													<?php elseif (is_array($data['value'])) :?>
														<p><?php echo join(", ",$data['value']); ?></p>
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
			  			</div>

							<?php if($this->item->getResource() instanceof List8D_Model_Resource_Journal || $this->item->getResource() instanceof List8D_Model_Resource_JournalArticle ): ?>
			  			<div class="info-box">
								<h4>View Resources</h4>
								<div class="info">
									<a href="<?php echo List8D_Model_Metatron_KentSerialsSolutions::createSSLink($this->item); ?>">View resources</a>
								</div>
							</div>
			  			<?php endif; ?>


  						<a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$this->item->getTrunkId())); ?>#<?php echo $this->item->isList() ? "list" : "item" ; ?>_<?php echo $this->item->getId(); ?>" class="close" title="close and return to list"><img src="<?php echo $this->baseURl(); ?>/themes/gaia/images/icon-close.gif" alt="collapse" /></a>
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
	  			<h3>Other places this resource appears</h3>
					<div class="info-box">
						<?php $items = $this->item->getResource()->getItems(array($this->item->getId())); ?>
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