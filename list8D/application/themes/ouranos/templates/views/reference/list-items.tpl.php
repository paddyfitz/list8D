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
					</div>
	  			<div class="reference"><?php echo $this->reference($child); ?></div>
	  			<?php if ($child->getDataValue("public_notes")): ?>
						<p class="student-notes"><strong>Notes:</strong> <?php echo $child->getDataValue("public_notes"); ?></p>
					<?php endif; ?>

	  			<?php if (is_string($child->getResource()->getDataValue('url',false))) : ?>
						<div class="links">
							<a href="<?php echo $child->getResource()->getDataValue('url',false); ?>" title="view online resource" class='icon icon-globe-green-arrow' target="_blank">view online resource</a>
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
									  					<li><a href="<?php echo $link2['url']; ?>" target="_blank"><?php echo $link2['text']; ?></a></li>
								  					<?php endforeach; ?>
								  				<?php else: ?>
								  					<li><a href="<?php echo $link['url']; ?>" target="_blank">
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
	  					<?php $link = array_shift($child->getResource()->getDataValue('meta_url',false)); ?>
	  					
	  					<a href="<?php echo $link['url']; ?>" class="icon icon-tag-label-arrow" target="_blank">view more information</a>
						</div>
					<?php endif; ?>
					
	  		</li>
	  	<?php endif; ?>
	  <?php endif; ?>
  <?php endforeach; ?>
</ul>