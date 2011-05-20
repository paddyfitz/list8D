<?php $this->headLink()->appendStylesheet('home.css','all'); ?>
<?php $this->headLink()->appendStylesheet('home-ie7.css','all','lte IE 7'); ?>

<div class="center">
	<div class="header">
		<?php echo $this->logo(); ?>
		<?php if (isset($this->instituition)): ?>
			<h1><?php echo $this->instituition; ?></h1>
		<?php endif; ?>
		<?php if (isset($this->applicationName)): ?>
			<h2><?php echo $this->applicationName; ?></h2>
		<?php endif; ?>
	</div>
	<div class="advanced-search-form">
		<?php $this->advancedSearchForm->setAction($this->url(array('controller'=>'list','action'=>'search'))); ?>
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
		<?php echo $this->advancedSearchForm; ?>
		
	</div>
	<form action="<?php echo $this->url(array('controller'=>'list','action'=>'search')); ?>" method="get" id="search-form" >
		<input class="search" type="text" value="<?php if (isset($this->q)) echo $this->q; else echo "module code or title"; ?>" name="q" id="q" />
		<input class="submit" type="submit" value="Search" />
	</form>
	<script type="text/javascript">
		$().ready(function(){
			q = $("#q");
			if (q.val()=='module code or title')
				q.addClass('default');
			$("#q").focus(function() {
				if (q.val()=='module code or title') {
					q.val("").removeClass("default");
				}
			});
			$("#q").blur(function() {
				if (q.val()=='') {
					q.val("module code or title").addClass("default");
				}
			});
		});
	</script>
	
	<div class="intro-links">
		<div class="search-help">
			<div class="top"></div>
			<div class="content">
				<h3>Search for lists</h3>
				<p>Enter either the module code or title for a list. Clicking on a result will show you the latest version, you can then change to previous years.</p>
			</div>
			<div class="bottom"></div>
		</div>
		<div class="right">
			<div class="advanced-search clearfix" id="advanced-search-link">
				<a class="icon icon-search" href="<?php echo $this->url(array('controller'=>'list','action'=>'search')); ?>">advanced search</a>
				<script type="text/javascript">
					$("#advanced-search-link").find(".icon-search").click(function() {
						$(" .advanced-search-form").css({
							'opacity':0,
							'height':62
						}).animate({
							'height': "250",
							'opacity': "1"
						});
						$("#search-form").css({
							'position':'absolute',
							'top':102
						}).fadeOut();
						$(this).parent().animate({
							'height':0,
							'opacity':0
						});
						$(".search-help").animate({
							'margin-top': -35
						});
						$(".search-help .content p").html("Enter at least one value to search by. Convener will currently search logins and surnames.");
						return false;
					});
				</script>
			</div>
			<div class="browse">
				<!--<h3>Browse</h3>-->
				<p>Browse by tags such as department or faculty.</p>
				<a class="icon icon-direction" href="<?php echo $this->url(array('controller'=>'list','action'=>'browse')); ?>">browse lists</a>
			</div>
			<div class="admin">
				<!--<h3>Staff admin</h3>-->
				<p>Admin area for staff to maintain lists.</p>
				<a class="icon icon-gear" href="<?php echo $this->baseUrl(); ?>/admin">admin lists</a>
			</div>
		</div>
	</div>
</div>