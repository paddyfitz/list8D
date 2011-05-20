<li class='item clearfix book resource'>
	
	<div class="cover">
	  <?php if ($this->item->getResource()->getDataValue('Amazon_thumbnailUrl_medium')): ?>
	  	<img src="<?php echo $this->item->getResource()->getDataValue('Amazon_thumbnailUrl_medium'); ?>" title="cover for <?php echo $this->item->getResource()->getDataValue('title'); ?>" class='cover' />
	  <?php else: ?>
	  	<div class="no-cover-art"></div>
	  
	  <?php endif; ?> 
	</div>
	
	<div class="details">
		<div class="flags clearfix">
			<?php if ($this->item->getResource()->getDataValue('isbn') || $this->item->getResource()->getDataValue('isbn10') || $this->item->getResource()->getDataValue('isbn13')): ?>
				<div class="icon type type-<?php echo $this->item->getResource()->getType(); ?>"><?php echo $this->item->getResource()->getTypeName(); ?></div>
			<?php endif; ?>
			
			<?php if ($this->item->getDataValue('core_text')): ?>
			  <div class="icon core-text">Core text</div>
			<?php endif; ?>
			
			<?php if ($this->item->getDataValue('recommended_for_purchase')): ?>
			  <div class="icon recommended-for-purchase">Recommended for purchase</div>
			<?php endif; ?>
		</div>
		
		<h5 class="published_data_title">
		  <?php if ($this->item->getResource()->getDataValue('publication_date')): ?>
		  	<?php echo $this->item->getResource()->getDataValue('publication_date'); ?>
		  <?php endif; ?>
			<?php if ($this->item->getResource()->getDataValue('title')): ?>
				<strong><?php echo $this->item->getResource()->getDataValue('title'); ?></strong>
			<?php endif; ?>
		</h5>
		
		<?php if ($this->item->getResource()->getDataValue('authors')): ?>
		  <h6 class="authors">			
		  	<?php echo $this->item->getResource()->getDataValue('authors'); ?>
		  </h6>
		<?php endif; ?>
		
		<?php if ($this->item->getResource()->getDataValue('publisher')): ?>
		  <div class="published-by">			
		  	published by 
		  	<span class="publisher">			
		  		<?php echo $this->item->getResource()->getDataValue('publisher'); ?>
		  	</span>
		  </div>
		<?php endif; ?>
		
		 
		<?php if ($this->item->getDataValue('part')): ?>
		  <div class="section-to-read">			
		  	Section to read:  
		  	<span class="section-to-read">			
		  		<?php echo $this->item->getDataValue('part'); ?>
		  	</span>
		  </div>
		<?php endif; ?>
		
		<?php if ($this->item->getDataValue('public_notes')): ?>
		  <div class="student-notes">			
		  	Notes for students:  
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
		
		
		<?php if ($this->item->getResource()->getDataValue('KentVoyager_url')): ?>
		  <a class="view-in-catalogue" href="<?php echo $this->item->getResource()->getDataValue('KentVoyager_url'); ?>">view<?php if (!empty($this->item->getResource()->title)) echo "  <span class='title'>\"".$this->item->getResource()->title."\"</span>"; ?> in the library catalogue</a>
		<?php endif; ?>
	</div>
</li>