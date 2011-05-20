<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title><?php echo $this->title ?></title>
		
		<?php echo $this->headLink(); ?>
		<?php echo $this->headScript(); ?>
		
	</head>
	<body>

		<div class="center">
			<div class="header">
				<div class="title">
					<a href="/" title="home"><?php echo $this->logo(); ?></a>
					<h1><a href="/" title="home"><?php echo $this->instituition; ?></a></h1>
					<h2><a href="/" title="home"><?php echo $this->applicationName; ?></a></h2>
				</div>
				<div class="tools">
					<form action="<?php echo $this->url(array('controller'=>'front','action'=>'search'),null,false,true,true); ?>" method="get" id="search-form" >
						<input class="search" type="text" value="module code or title" name="q" id="q" />
						<input class="submit" type="submit" value="Search" />
					</form>
					<script type="text/javascript">
						$().ready(function(){
							q = $("[name=q]");
							if (q.eq(q.length).val()=='module code or title')

								q.addClass('default');
							q.focus(function() {
								if ($(this).val()=='module code or title') {
									$(this).val("").removeClass("default");
								}
							});
							q.blur(function() {
								if ($(this).val()=='') {
									$(this).val("module code or title").addClass("default");
								}
							});
						});
					</script>
					<ul class="links">
						<li class="advanced-search"><a href="<?php echo $this->url(array('action'=>'advancedsearch'),null,false,true,true); ?>" title="advanced search" class="icon icon-search">advanced search</a></li>
						<li class="browse"><a href="<?php echo $this->url(array('action'=>'browse'),null,false,true,true); ?>" title="browse" class="icon icon-direction">browse</a></li>
						<li class="admin"><a href="<?php echo $this->baseUrl(); ?>/admin" title="admin" class="icon icon-gear">admin</a></li>
					</ul>
				</div>
			</div>
			<?php if(isset($this->list) && $this->list->getDataValue('code') && $this->displayTitle): ?>
			  <div class="module-code<?php if ($this->list->getDataValue('credits')) echo " module-code-".$this->list->getDataValue('credits'); ?>" title="<?php if (!$this->list->getDataValue('stage') || !$this->list->getDataValue('credits')) echo "module {$this->list->getDataValue('code')} (we could find any stage or credit information)"; ?>"><?php echo $this->list->getDataValue('code'); ?></div>

			  <div class="head-info">
			  	<h3 class='with-code'>
			  		<?php echo $this->list->getTitle(); ?>
			  	</h3>
			  	<?php if ($this->list->getDataValue('convener') || $this->list->getDataValue('department') || $this->list->getDataValue('campus') || $this->list->getDataValue('week_beginning')): ?>
			  		<h4>
			  			<?php $s = false; ?>
			  			<?php if ($this->list->getDataValue('convener')): ?><?php if ($s) echo 'c'; else echo 'C'; $s=true; ?>onvened by <strong><?php $f=false; foreach($this->list->getDataValue('convener',false) as $user): ?><?php if ($f) { echo ", "; } $f=true; ?><?php echo $this->user($user); ?><?php endforeach; ?></strong><?php endif; ?><?php if ($this->list->getDataValue('department')): ?><?php if ($s) echo ', r'; else echo 'R'; $s=true; ?>un by the 	<strong><?php echo $this->list->getDataValue('department'); ?></strong> <?php endif; ?>
			  			<?php if ($this->list->getDataValue('campus')): ?><?php if ($s) echo 'a'; else echo 'Run a'; $s=true; ?>t <strong><?php echo $this->list->getDataValue('campus'); ?></strong> <?php endif; ?>
			  			<?php if ($this->list->getDataValue('week_beginning')): ?><?php if ($s) echo 's'; else echo 'S'; $s=true; ?>tarting <strong>week <?php echo $this->list->getDataValue('week_beginning'); ?></strong> <?php endif; ?>
			  		</h4>
			  	<?php endif; ?>
			  </div> 
			<?php elseif (isset($this->title) && $this->displayTitle): ?>
			  <div class="head-info">
			  	<h3><?php echo $this->title; ?></h3>
			  </div>
			<?php endif; ?>
			<?php echo $this->flashMessages(); ?>
			<?php echo $this->layout()->content; ?>
				
			<!-- footer -->
			<div id="footer">
				<div class="instituion-notice">
					<div class="holder">
						<?php echo $this->footerNote; ?>
					</div>
				</div>
				
				<div class="copyright-license">
				  <p class="copyright">
				  	Content Copyright &copy; 2010 <?php echo $this->instituition; ?><br/>
				  	Design Copyright &copy; 2010 University of Kent<br/>
				  	Icons Copyright &copy; 2010 Yusuke Kamiyamane
				  </p>
				  <p class="license">
				  	list8D is licensed under the <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html">GNU General Public License v2</a><br/>
				  	Zend is licensed under the <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html">New BSD License</a><br/>
				  	Fugue Icons are licensed under the <a href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 license</a>
				  </p>
				</div>
			
			</div>	
		</div>	
		<?php echo $this->inlineScript(); ?>
	</body>
</html>
