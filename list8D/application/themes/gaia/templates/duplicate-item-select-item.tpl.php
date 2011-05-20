<!-- content -->
<div id="content">
	<!-- box-holder -->
	<div id="box-holder" class="box-holder small-list select-duplicate">			
		<?php echo $this->partial('duplicate-item-select-item-items.tpl.php',array('list'=>$this->list,'theAction'=>$this->theAction)); ?>
	</div>
</div>

<!-- sidebar -->
<div id="sidebar">
	<!-- sidebar-box -->
  <div class="sidebar-box">
  	<div class="holder">
  		<div class="frame">
  			<h3>Select item to duplicate</h3>
	  		<div class="info-box info-box-2">
	  			<p>Please click on item or nested list you would like to duplicate with the list "<?php echo $this->list->getTitle(); ?>"</p>
	  			<p>If you are trying to duplicate a large nested list it may take some time please be patient.</p>
	  			<span class="box-link support"><a class="icon cancel-icon" href="<?php echo $this->url(array('controller'=>'list', 'action'=>'view', 'id'=>$this->params['id'])); ?>">cancel</a></span>
	  		</div>
  		</div>
  	</div>
  </div>
</div>