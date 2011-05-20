<?php
	/**
	 * Although this looks like logic its actually view settings
	 * so its ok for it to be here.
	 */
	$this->advancedSearchForm->setDecorators(array(
    'FormElements',
	  array(array('data'=>'HtmlTag'),array('tag'=>'table','class'=>'form-table')),
	  'Form',
	));
	foreach ($this->advancedSearchForm->getElements() as $key => $element) {
		if ($key == "submit") {
			$element->setDecorators(array(
	  		'ViewHelper',
	  		'Description',
	  		'Errors',
	  		array(array('data'=>'HtmlTag'), array('tag' => 'td','align'=>'right','colspan'=>2,'valign'=>'bottom','class'=>'submit')),
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
<div class="advanced-search-form">
	<?php echo $this->advancedSearchForm; ?>
</div>