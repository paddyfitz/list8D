<?php

	/**
	 * Although this looks like logic its actually view settings
	 * so its ok for it to be here.
	 */
	
	
	$this->searchForm->setDecorators(array(
    'FormElements',
	  array(array('data'=>'HtmlTag'),array('tag'=>'table','class'=>'form-table')),
	  'Form',
	));
	foreach ($this->searchForm->getElements() as $key => $element) {
		if ($key == "submit") {
			$element->setDecorators(array(
	  		'ViewHelper',
	  		'Description',
	  		'Errors',
	  		array(array('data'=>'HtmlTag'), array('tag' => 'td','align'=>'center','colspan'=>2,'height'=>'65px','valign'=>'bottom','prepend'=>"<a href='{$this->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'type'=>null))}' class='cancel icon cancel-icon'>cancel</a>")),
	  	  array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
	  	));
		} else {
	  	$element->setDecorators(array(
	  		'ViewHelper',
	  		'Description',
	  		'Errors',
	  		array(array('data'=>'HtmlTag'), array('tag' => 'td','align'=>'left','valign'=>'top')),	  		
	  		array('Label', array('tag' => array('tag' => 'td','valign'=>'top','align'=>'left','class'=>'label'))),
				array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
	  	));
	  }
	}
	 
?>

<div id="content">
	<!-- add-item-box -->
	<div class="add-item-box">
	  <div class="tl"><div class="tr">&nbsp;</div></div>
	  <div class="c">
	  	<div class="cr">
	  		<div class="add-item-head">
	  			<h2 class="head-select">Select the type of item to add</h2>
	  			
	  			<?php if($this->currentAction == "create"): ?>
	  				<h2>Create a new item</h2>
	  			<?php else: ?>
	  			
		  			<?php if($this->currentType == "List"): ?>
			  			<h2>Enter list settings</h2>
		  			<?php else: ?>
			  			<h2>Search for the resource</h2>
		  			<?php endif; ?>
		  			
	  			<?php endif ?>
	  		</div>
	  		<div class="add-item-body">
	  			<ul class="type-of-item">
	  				<?php $i=0; foreach($this->contentTypes as $type): $i++; ?>
	  					<?php if ($type->getType() != 'List' || $this->currentAction != "create"): ?>
	  					<li class="<?php echo $type->getType(); ?><?php if ($type->getType()==$this->currentType) echo ' active'; ?>">
  							<a href="<?php echo $this->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'], 'type'=>$type->getType(), 'position' => $this->position)); ?>"<?php if($i==count($this->contentTypes)) echo " class='last'"; ?>>
  								<strong><?php echo $type->getTypeName(); ?></strong>
  								<span><?php echo $type->getExpanded(); ?></span>
  							</a>
  						</li>
  						<?php endif ?>
						<?php endforeach; ?>
	  			</ul>
	  			<div class="search-resource">
	  				<div class="holder">
	  						<?php if ($this->isPost && !$this->searchForm->isValid($_POST)): ?>
	  							<p class="errors">The values you submitted have errors, please check them and try again.</p>
	  						<?php endif; ?>
	  						<?php echo $this->searchForm; ?>

	  				</div>
	  			</div>
	  		</div>
	  	</div>
	  </div>
	  <div class="bc">
	  	<div class="bl">
	  		<div class="br">&nbsp;</div>
	  	</div>
	  </div>
	</div>
</div>



