<li class='item clearfix book resource'>

	<div class="cover">
	  <?php if ($this->item->getResource()->getDataValue('Amazon_thumbnailUrl_medium')): ?>
	  	<img src="<?php echo $this->item->getResource()->getDataValue('Amazon_thumbnailUrl_medium'); ?>" title="cover for <?php echo $this->item->getResource()->getDataValue('title'); ?>" class='cover' />
	  <?php elseif($this->item->getResource()->getDataValue('url')): ?>
		  <?php echo $this->urlThumb($this->item->getResource()->getDataValue('url')); ?>
	  <?php else: ?>
	  	<div class="no-cover-art"></div>
	  
	  <?php endif; ?> 
	</div>
	
	<div class="details">
		<div class="flags clearfix">
			<div class="icon type type-<?php echo $this->item->getResource()->getType(); ?>"><?php echo $this->item->getResource()->getTypeName(); ?></div>
			<?php if ($this->item->getDataValue('core_text')): ?>
			  <div class="icon core-text">Core text</div>
			<?php endif; ?>
			
			<?php if ($this->item->getDataValue('recommended_for_purchase')): ?>
			  <div class="icon recommended-for-purchase">Recommended for purchase</div>
			<?php endif; ?>
			
		</div>
		
		<h5 class="published_data_title">
		  <?php if ($this->item->getResource()->getDataValue('title')): ?>
				<strong><?php echo $this->item->getResource()->getDataValue('title'); ?></strong>
			<?php endif; ?>
		</h5>
		
		<?php if ($this->item->getResource()->getDataValue('authors')): ?>
		  <h6 class="authors">			
		  	<?php echo $this->item->getResource()->getDataValue('authors'); ?>
		  </h6>
		<?php endif; ?>
		
		<?php if ($this->item->getResource()->getDataValue('publisher') || $this->item->getResource()->getDataValue('publication_date')): ?>
		  <div class="published-by">			
		  	published 
		  	<?php if ($this->item->getResource()->getDataValue('publication_date')): ?>
			  	<span class="publisher">			
			  		<?php echo $this->item->getResource()->getDataValue('publication_date'); ?>
			  	</span>
			  <?php endif; ?>
			  
		  	<?php if ($this->item->getResource()->getDataValue('publisher')): ?>
			  	by 
			  	<span class="publisher">			
			  		<?php echo $this->item->getResource()->getDataValue('publisher'); ?>
		  		</span>
		  	<?php endif; ?>
		  </div>
		<?php endif; ?>
		
		 
		<?php if ($this->item->getDataValue('part') || $this->item->getDataValue('part_title')): ?>
		  <div class="section-to-read">			
		  	Section to read:  
		  	<span class="section-to-read">			
		  		<?php if ($this->item->getDataValue('part_title')): ?>
		  			<?php echo $this->item->getDataValue('part_title'); ?><?php if ($this->item->getDataValue('part')) echo ", "; ?>
		  		<?php endif; ?>
		  		
		  		<?php if ($this->item->getDataValue('part')): ?>
			  		<?php echo $this->item->getDataValue('part'); ?>
			  	<?php endif; ?>
			  	<?php if ($this->item->getDataValue('part_author')): ?>
			  		by <?php echo $this->item->getDataValue('part_author'); ?>
			  	<?php endif; ?>
			  	
		  	</span>
		  </div>
		<?php endif; ?>
		
		<?php if ($this->item->getDataValue('public_notes')): ?>
		  <div class="student-notes">			
		  	Notes:  
		  	<span class="student-notes">			
		  		<?php echo $this->item->getDataValue('public_notes'); ?>
		  	</span>
		  </div>
		<?php endif; ?>
		
		<?php if ($this->item->getResource()->getDataValue('isbn10') || $this->item->getResource()->getDataValue('isbn13')): ?>
		  <div class="isbn">			
		  	ISBN:
		  	<?php if ($this->item->getResource()->getDataValue('isbn10')): ?>
		  		<span class="isbn10">			
		  			<?php echo $this->item->getResource()->getDataValue('isbn10'); ?>
		  		</span>
		  	<?php endif; ?>
		  	<?php if ($this->item->getResource()->getDataValue('isbn10') && $this->item->getResource()->getDataValue('isbn13')): ?>
		  		<?php echo ", "; ?>
		  	<?php endif; ?>
		  	<?php if ($this->item->getResource()->getDataValue('isbn13')): ?>
		  		<span class="isbn13">			
		  			<?php echo $this->item->getResource()->getDataValue('isbn13'); ?>
		  		</span>
		  	<?php endif; ?>
		  </div>
		<?php endif; ?>
		
		<?php if ($this->item->getResource()->getDataValue('issn')): ?>
		  <div class="issn">
		  	ISSN: 
		  	<span class="issn">			
		  		<?php echo $this->item->getResource()->getDataValue('issn'); ?>
		  	</span>
		  </div>
		<?php endif; ?>
		
		
		<?php if ($this->item->getResource()->getDataValue('meta_url',false)) : ?>
	  	<div class="metadata-links">
	  		<?php $links = $this->item->getResource()->getDataValue('meta_url',false); ; ?>
	  		
	  		<?php if (count($links)===1): ?>
	  		  <?php $preffix = "view more information in "; ?>
				<?php else: ?>
				  <h5>View more information:</h5>
				<?php endif; ?>
				
				<?php foreach($links as $link): ?>
				  <a href="<?php echo $link['url']; ?>" class="icon icon-tag-label-arrow"><?php echo $preffix; ?><?php echo $link['text']; ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		
		<?php if($this->item->getResource() instanceof List8D_Model_Resource_Journal || $this->item->getResource() instanceof List8D_Model_Resource_JournalArticle ): ?>
			<div class="links">
			  <a href="<?php echo List8D_Model_Metatron_KentSerialsSolutions::createSSLink($this->item); ?>" title="view online resource" class='icon icon-globe-green-arrow'>view online resource</a>
			</div>
	  <?php elseif (is_string($this->item->getResource()->getDataValue('url',false))) : ?>
			<div class="links">
				<a href="<?php echo $this->item->getResource()->getDataValue('url',false); ?>" title="view online resource" class='icon  icon-globe-green-arrow'>view online resource</a>
			</div>
	  <?php elseif (is_array($this->item->getResource()->getDataValue('url',false)) && count($this->item->getResource()->getDataValue('url',false))) : ?>
	  	<div class="links">
	  		<?php $links = $this->item->getResource()->getDataValue('url',false); $links2 = $links; ?>
		  	<?php $firstLink = array_shift($links2); ?>
	  		<?php if (is_string($firstLink['url'])): ?>
		  		<a href="<?php echo $firstLink['url']; ?>">
	  		<?php endif; ?>
						<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/globe-green-arrow.png" alt="view online resource" />
	  		<?php if (is_string($firstLink['url'])): ?>	  		  	
			  	</a>
			  <?php endif; ?>
	  		<div class="links">
					<ul>
				  	<?php foreach($this->item->getResource()->getDataValue('url',false) as $link): ?>
				  		<li>
				  			<?php echo $link['label']; ?>
					  		<ul>
				  				<?php if (is_array($link['url'])): ?>
				  					<?php foreach($link['url'] as $link2): ?>
					  					<li><a href="<?php echo $link2['url']; ?>"><?php echo $link2['text']; ?></a></li>
				  					<?php endforeach; ?>
				  				<?php else: ?>
				  					<li><a href="<?php echo $link['url']; ?>">
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
	  <?php endif; ?>

		
	</div>
</li>