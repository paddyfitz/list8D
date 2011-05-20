<?php if (isset($this->nested) && $this->nested) echo "<li class='item list'>"; ?>

<h3>
  <?php if (!empty($this->list->code)) echo $this->list->code; ?>
  <?php if (!empty($this->list->code) && !empty($this->list->title)) echo " - "; ?>
  <?php if (!empty($this->list->title)) echo $this->list->title; ?>



<?php if (!empty($this->list->public_notes)): ?>
	, Notes: <?php echo $this->list->public_notes; ?></p>
<?php endif; ?>
</h3>


<?php if (!empty($this->list->children)): ?>
	<?php if (!isset($this->nested) || !$this->nested): ?>
		<h4>Resources</h4>
	<?php endif; ?>
		
	<ul class="list-items compressed">
	
  	<?php foreach($this->list->children as $child): ?>
			<li class='item'>
  			<?php if ($child->type=='Book' || $child->type=='Legacy') : ?>
  				<img src="../images/book.png" alt="book" />
  				<?php if (!empty($child->resource->authors)) echo $child->resource->authors.", "; ?>
  				<?php if (!empty($child->resource->publicationDate)) echo $child->resource->publicationDate.", "; ?> 
  				<?php if (!empty($child->resource->title)) echo $child->resource->title.", "; ?> 
  				<?php if (!empty($child->resource->publisher)) echo $child->resource->publisher.", "; ?> 
  					
  				
  			<?php elseif ($child->type == "Uri"): ?>
  				<img src="../images/globe-green-chain.png" alt="url" />	
  				<a href="<?php echo $child->resource->url; ?>" title='follow link <?php if (!empty($child->resource->title)) echo $child->resource->title; ?> in your browser'>
	  				<?php if (!empty($child->resource->title)) echo $child->resource->title; else $this->child->resource->url; ?>
  				</a>
  			<?php elseif ($child->type == "List"): ?>
  				<?php $this->render('reading-list-compressed-list.tpl.php',array('list'=>$child,'nested'=>true)); ?>
  			<?php endif; ?>
  			
  			<?php if (!empty($child->resource->part)) echo $child->resource->part.","; ?> 
  			<?php if (!empty($child->resource->public_note)) echo "note: ".$child->resource->public_note.", "; ?> 
  			<?php if (!empty($child->resource->url)): ?>
  				<a href="<?php echo $child->resource->url; ?>" title='follow link <?php if (!empty($child->resource->title)) echo $child->resource->title; ?> in your browser'>
		  			<img src="../images/arrow-000.png" alt='follow link <?php if (!empty($child->resource->title)) echo $child->resource->title; ?> in your browser' />
		  		</a>
	  		<?php endif; ?>
  			<?php if (!empty($child->resource->KentVoyager_url)): ?>
  					<a class="view-in-catalogue" href="<?php echo $child->resource->KentVoyager_url; ?>" title='view<?php if (!empty($child->resource->title)) echo $child->resource->title; ?> in the library catalogue'>
  						<img src="../images/book-arrow.png" alt='view<?php if (!empty($child->resource->title)) echo $child->resource->title; ?> in the library catalogue' />
  					</a>
  				<?php endif; ?>
  			<?php if (!empty($child->core_text) && $child->core_text): ?>
				  <img src="../images/exclamation.png" alt="important" title="important resource" />
				<?php endif; ?>
				
				<?php if (!empty($child->recommended_for_purchase) && $child->recommended_for_purchase): ?>
				  <img src="../images/money_dollar.png" alt="important" title="recommend for purchase" />
				<?php endif; ?>
  		</li>
  	<?php endforeach; ?>	
  </ul>
  
<?php elseif (!isset($this->nested) || !$this->nested) : ?>

  <p>This list is currently empty. If the course conveyer is not using list8D the list may exist in another place. If you can't find the reading list please contact course administrator.</p>
<?php endif; ?>

<?php if (isset($this->nested) && $this->nested) echo "</li>"; ?>