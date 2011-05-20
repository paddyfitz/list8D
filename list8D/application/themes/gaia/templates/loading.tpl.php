<div id="content" class="loading">
	<h2><?php echo $this->heading; ?></h2>
	<a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'])); ?>?load=1">Click here to continue loading</a>
</div>
<script type="text/javascript">
	<?php if (isset($this->loadingHeading)): ?>
		$('#content h2').text("<?php echo $this->loadingHeading; ?>");
	<?php endif; ?>
	$('#content a').remove();
	$('#content').css('background','url(<?php echo $this->baseUrl(); ?>/themes/gaia/images/throbber-large.gif) 5px 15px no-repeat').css('padding-left','93px');
	var url = '<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'])); ?>?load=1';
	if(window.location.hash) {
		url = url + window.location.hash;
	}
	$().ready(function(){window.location=url;});
</script>