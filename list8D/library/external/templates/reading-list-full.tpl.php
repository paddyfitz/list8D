
		
		<ul class="list-items">
	  	<?php foreach($this->list->children as $child): ?>
	  		<?php if ($child->type=='Book' || $child->type=='Legacy') : ?>
	 				<?php echo $this->render("reading-list-full-book.tpl.php",array('child'=>$child)); ?>
	  		<?php elseif ($child->type == "Uri"): ?>
	  				<?php echo $this->render("reading-list-full-uri.tpl.php",array('child'=>$child)); ?>
	  		<?php elseif ($child->type == "List"): ?>
	  				<?php echo $this->render("reading-list-full-nested-list.tpl.php",array('list'=>$child,'nested'=>true)); ?>
	  		<?php endif; ?>
	  	<?php endforeach; ?>
	  </ul>
	  
		