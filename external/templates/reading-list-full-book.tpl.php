<li class='item clearfix book'>

	<div class="cover">
	  <?php if (!empty($this->child->resource->Amazon_thumbnailUrl_medium)): ?>
	  	<img src="<?php echo $this->child->resource->Amazon_thumbnailUrl_medium; ?>" title="cover for <?php echo $this->child->resource->title; ?>" class='cover' />
	  <?php else: ?>
	  	<div class="no-cover-art"></div>
	  
	  <?php endif; ?> 
	</div>
	
	<div class="details">
		<div class="flags clearfix">
			<?php if (isset($this->child->resource->isbn) || isset($this->child->resource->isbn10) || isset($this->child->resource->isbn13)): ?>
				<div class="icon type type-book">Book</div>
			<?php endif; ?>
			
			<?php if (!empty($this->child->core_text) && $this->child->core_text): ?>
			  <div class="icon core-text">Core text</div>
			<?php endif; ?>
			
			<?php if (!empty($this->child->recommended_for_purchase) && $this->child->recommended_for_purchase): ?>
			  <div class="icon recommended-for-purchase">Recommended for purchase</div>
			<?php endif; ?>
		</div>
		
		<h5 class="published_data_title">
		  <?php if (!empty($this->child->resource->publicationDate)): ?>
		  	<?php echo $this->child->resource->publicationDate; ?>
		  <?php endif; ?>
			<?php if (!empty($this->child->resource->title)): ?>
				<strong><?php echo $this->child->resource->title; ?></strong>
			<?php endif; ?>
		</h5>
		
		<?php if (!empty($this->child->resource->authors)): ?>
		  <h6 class="authors">			
		  	<?php echo $this->child->resource->authors; ?>
		  </h6>
		<?php endif; ?>
		
		<?php if (!empty($this->child->resource->publisher)): ?>
		  <div class="published-by">			
		  	published by 
		  	<span class="publisher">			
		  		<?php echo $this->child->resource->publisher; ?>
		  	</span>
		  </div>
		<?php endif; ?>
		
		 
		<?php if (!empty($this->child->part)): ?>
		  <div class="section-to-read">			
		  	Section to read:  
		  	<span class="section-to-read">			
		  		<?php echo $this->child->part; ?>
		  	</span>
		  </div>
		<?php endif; ?>
		
		<?php if (!empty($this->child->public_notes)): ?>
		  <div class="student-notes">			
		  	Notes for students:  
		  	<span class="student-notes">			
		  		<?php echo $this->child->public_notes; ?>
		  	</span>
		  </div>
		<?php endif; ?>
		
		<?php if (!empty($this->child->resource->isbn10) || !empty($this->child->resource->isbn13)): ?>
		  <div class="isbn">			
		  	ISBN:
		  	<?php if (!empty($this->child->resource->isbn10)): ?>
		  		<span class="isbn10">			
		  			<?php echo $this->child->resource->isbn10; ?>
		  		</span>
		  	<?php endif; ?>
		  	<?php if (!empty($this->child->resource->isbn10) && !empty($this->child->resource->isbn13)): ?>
		  		<?php echo ", "; ?>
		  	<?php endif; ?>
		  	<?php if (!empty($this->child->resource->isbn13)): ?>
		  		<span class="isbn13">			
		  			<?php echo $this->child->resource->isbn13; ?>
		  		</span>
		  	<?php endif; ?>
		  </div>
		<?php endif; ?>
		
		<?php if (!empty($this->child->resource->issn)): ?>
		  <div class="issn">
		  	ISSN: 
		  	<span class="issn">			
		  		<?php echo $this->child->resource->issn; ?>
		  	</span>
		  </div>
		<?php endif; ?>
		
		
		<?php if (!empty($this->child->resource->KentVoyager_url)): ?>
		  <a class="view-in-catalogue" href="<?php echo $this->child->resource->KentVoyager_url; ?>">view<?php if (!empty($this->child->resource->title)) echo "  <span class='title'>\"".$this->child->resource->title."\"</span>"; ?> in the library catalogue</a>
		<?php endif; ?>
	</div>
<li>