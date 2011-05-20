<li class='item clearfix url'>

	<div class="cover">
	  <?php
			$thumboo_api = "50be95df663d62cf93bc285444e37660";
			$thumboo_url = $this->child->resource->url;
			$thumoo_params = "u=".urlencode("http://".$_SERVER["HTTP_HOST"].
			$_SERVER["REQUEST_URI"])."&su=".urlencode($thumboo_url)."&c=small&api=".$thumboo_api;
			@readfile("http://counter.goingup.com/thumboo/snapshot.php?".$thumoo_params); ?>
	</div>
	<div class="details">
		<div class="flags clearfix">
			<div class="icon type type-url">URL</div>
			<?php if (!empty($this->child->core_text) && $this->child->core_text): ?>
			  <div class="icon core-text">Important resource</div>
			<?php endif; ?>
			
			<?php if (!empty($this->child->recommended_for_purchase) && $this->child->recommended_for_purchase): ?>
			  <div class="icon recommended-for-purchase">Recommended for purchase</div>
			<?php endif; ?>
		</div>
		
		<h5 class="title">
		  <?php if (!empty($this->child->resource->title)) echo $this->child->resource->title; ?>
		</h5>
		
		<?php if (!empty($this->child->resource->url)): ?>
		  <h6 class="url">			
		  	<?php echo $this->child->resource->url; ?>
		  </h6>
		<?php endif; ?>
		
		
		<?php if (!empty($this->child->resource->url)): ?>
		  <a class="follow-link" href="<?php echo $this->child->resource->url; ?>">follow link <?php if (!empty($this->child->resource->title)) echo "  <span class='title'>\"".$this->child->resource->title."\"</span>"; ?> in your browser</a>
		<?php endif; ?>
	</div>
<li>