<ul class="list-items reference">
  <?php foreach($this->list->getChildren() as $child): ?>
  	<?php if ($child->getDataValue("is_published")): ?>
  		<?php if($child->isList()): ?>
  			<?php echo $this->partial("list-item-nested-list.tpl.php",array('list'=>$child)); ?>
  		<?php else: ?>
	  		<li class='item'>
	  			<div class="flags clearfix">
						<div class="icon type type-<?php echo $child->getResource()->getType(); ?>"><?php echo $child->getResource()->getTypeName(); ?></div>
						
						
						<?php if ($child->getDataValue('core_text')): ?>
						  <div class="icon core-text">Core text</div>
						<?php endif; ?>
						
						<?php if ($child->getDataValue('recommended_for_purchase')): ?>
						  <div class="icon recommended-for-purchase">Recommended for purchase</div>
						<?php endif; ?>
						
						<?php if ($child->getDataValue('scan_request') == "scan_requested"): ?>
							<div class="icon scan-request">Scan Requested</div>
						<?php elseif ($child->getDataValue('scan_request') == "scan_available"): ?>
							<div class="icon scan-available">Scan Available in Moodle</div>
						<?php elseif ($child->getDataValue('scan_request') == "scan_in_progress"): ?>
							<div class="icon scan-in-progress">Scan in Progress</div>
						<?php endif; ?>
						
						<?php if($child->getResource() instanceof List8D_Model_Resource_Journal || $child->getResource() instanceof List8D_Model_Resource_JournalArticle ): ?>
			<div class="links">
			  <a href="<?php echo List8D_Model_Metatron_KentSerialsSolutions::createSSLink($child->getResource()); ?>" title="view online resource" class='icon icon-globe-green-arrow'>view online resource</a>
			</div>
	  			<?php elseif (is_string($child->getResource()->getDataValue('url',false))) : ?>
						<div class="links">
							<a href="<?php $displayurl = $child->getResource()->getDataValue('url',false); if(substr($displayurl,0,4) == "http"){echo $displayurl;}else{echo "http://".$displayurl;} ?>" title="view online resource" class='icon icon-globe-green-arrow' target="_blank">view online resource</a>
						</div>
	  			<?php elseif (is_array($child->getResource()->getDataValue('url',false)) && count($child->getResource()->getDataValue('url',false))) : ?>
	  				<div class="links">
  						<?php $links = $child->getResource()->getDataValue('url',false); $links2 = $links; ?>
				  		<?php $firstLink = array_shift($links2); ?>
	  					<span class="icon icon-globe-green-arrow">View online resource</span>
	  					<div class="content">
	  						<div class="links">
									<ul>
								  	<?php foreach($child->getResource()->getDataValue('url',false) as $link): ?>
								  		<li>
								  			<?php echo $link['label']; ?>
									  		<ul>
								  				<?php if (is_array($link['url'])): ?>
								  					<?php foreach($link['url'] as $link2): ?>
									  					<li><a href="<?php if(substr($link2['url'],0,4) == "http"){echo $link2['url'];}else{echo "http://".$link2['url'];} ?>" target="_blank"><?php echo $link2['text']; ?></a></li>
								  					<?php endforeach; ?>
								  				<?php else: ?>
								  					<li><a href="<?php if(substr($link['url'],0,4) == "http"){echo $link['url'];}else{echo "http://".$link['url'];} ?>" target="_blank">
								  						<?php if (!empty($link['text'])): ?>
								  							<?php echo $link['text']; ?>
										  				<?php else: ?>
											  				<?php echo $link['url']; ?>
											  			<?php endif; ?>
								  					</a></li>
								  				<?php endif; ?>								  						
								  			</ul>
								  		</li>
								  	<?php endforeach; ?>
								  </ul>
								</div>
							</div>
	  				</div>
	  			<?php endif; ?>
	  				
	  				
	  				<?php if ($child->getResource()->getDataValue('meta_url',false)) : ?>
	  					<div class="metadata-links">
						
	  						<?php 
	  							$links = $child->getResource()->getDataValue('meta_url',false);
	  							if (!empty($links['Voyager'])) {
	  								$link = $links['Voyager'];
	  							} else {
	  								$link = array_shift($links);
	  							}
	  						?>
	  						
	  						<a href="<?php echo $link['url']; ?>" class="icon icon-tag-label-arrow" target="_blank">view more information</a>
							</div>
						<?php endif; ?>
						
					</div>
					
					
					
					
	  			<div class="reference"><?php echo $this->reference($child); ?></div>
	  			<?php if ($child->getDataValue("public_notes")): ?>
						<p class="student-notes"><strong>Notes:</strong> <?php echo $child->getDataValue("public_notes"); ?></p>
				<?php endif; ?>

                

	  			
					
	  		</li>
	  	<?php endif; ?>
	  <?php endif; ?>
  <?php endforeach; ?>
</ul>