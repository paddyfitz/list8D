<li class='item clearfix url resource'>
	<div class="cover">
	  <?php echo $this->urlThumb($this->item->getResource()->getDataValue('url')); ?>
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
		
		<h5 class="title">
		  <?php if ($this->item->getResource()->getDataValue('title')) echo $this->item->getResource()->getDataValue('title'); ?>
		</h5>
		
		<?php if ($this->item->getResource()->getDataValue('url')): ?>
		  <h6 class="url">			
		  	<?php echo $this->item->getResource()->getDataValue('url'); ?>
		  </h6>
		<?php endif; ?>
		
		
		<?php if ($this->item->getResource()->getDataValue('url')): ?>
		  <a class="follow-link" href="<?php echo $this->item->getResource()->getDataValue('url'); ?>">follow link <?php if (!empty($this->item->getResource()->title)) echo "  <span class='title'>\"".$this->item->getResource()->title."\"</span>"; ?> in your browser</a>
		<?php endif; ?>
	</div>
</li>