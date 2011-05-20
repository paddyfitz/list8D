<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $this->title ?></title>
		
		<?php echo $this->headLink(); ?>
		
		<!--[if lt IE 7]>
			<link rel="stylesheet" type="text/css" href="/themes/gaia/css/ie6.css" media="screen"/>
			<script type="text/javascript" src="/themes/gaia/js/png-fix.js"></script>
		<![endif]-->
			
		<?php echo $this->headScript(); ?>
		
	</head>
	<body>
		<!-- wrapper -->
		<div id="wrapper"><!-- header -->
			<div id="header">
				<!-- holder -->
				<div class="holder">
					<div class="logo">
						<?php echo $this->logo(); ?>
						<a class='home' href="<?php echo $this->baseUrl(); ?>/"><span class="hidden">go home</span></a>
						<h1><span class="text"><?php echo $this->instituition; ?></span></h1>
	  	     	<h2><?php echo $this->applicationName; ?></h2>
	  	    </div>
					<div class="account-box">
						<div class="inner">
								<?php if ($this->user && $this->user->getId()!==null): ?>
									<a class="account" href="<?php echo $this->url(array('controller'=>'user','action'=>'view','id'=>$this->user->getId())); ?>"><strong>Account:</strong> <span>
										<?php echo $this->user->getDisplayName(); ?>
									</span></a>
								<?php elseif($this->user): ?>
									<span class="account"><strong>Account:</strong> <span>
										auth' without account
									</span></span>
								<?php else: ?>
									<span class="account"><strong>Account:</strong> <span>
										not logged in
									</span></span>
								<?php endif; ?>
							<?php /*
							<?php if ($this->user): ?>
								<a href="/user/logout" class="logout">logout</a>
							<?php endif; ?>
							*/ ?>
							<a href="javascript:void(0);" onclick="window.open('/admin/help/index.html', 'help', 'width=800,height=400,menubar=no,location=no')" class="help" id="help-link">Help</a>
						</div>
					</div>
				</div>
				<!-- navigation -->
				<div class="navigation">
					<?php echo $this->partial('navigation.tpl.php',array('user'=>$this->user)); ?>
					<div class="search-form">
						<form action="<?php echo $this->url(array('controller'=>'list','action'=>'search','page'=>'1')); ?>" method="get" enctype="application/x-www-form-urlencoded">
							<div class="row">
								<label for="field-1">Find list</label>
								<span class="field"><input type="text" value="search lists by title or code" id="q" name="q" /></span>
								<script type="text/javascript">
																	
									search_box = $('#q')
									
									search_box.addClass("dull");
									search_box.focus(function() {
										if (search_box.attr('value')=="search lists by title or code") {
											search_box.removeClass("dull").attr("value","");
										}
									});
									search_box.blur(function() {
										if (search_box.attr('value')=="") {
											search_box.addClass("dull").attr("value","search lists by title or code");
										}
									});
								</script>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- main -->
			<div id="main">
				<!-- main-head -->
				<?php if(isset($this->title)): ?>
					<div class="main-head">
						<div class="head">
							<div class="head-info">
								<?php if(isset($this->list) && $this->list->getDataValue('code')): ?>
									<div class="module-code<?php if ($this->list->getDataValue('credits')) echo " module-code-".$this->list->getDataValue('credits'); ?>" title="<?php if (!$this->list->getDataValue('credits')) echo "module {$this->list->getDataValue('code')} (we could find any stage or credit information)"; else echo "module {$this->list->getDataValue('code')} ({$this->list->getDataValue('credits')} credits)"; ?>"><?php echo $this->list->getDataValue('code'); ?></div>

									<div class="head-info">
										<h1 class='with-code'>
											<?php echo $this->title; ?>
										</h1>
										<?php if ($this->list->getDataValue('convener') || $this->list->getDataValue('department') || $this->list->getDataValue('campus') || $this->list->getDataValue('week_beginning')): ?>
											<h2>
												<?php $s = false; ?>
												<?php if ($this->list->getDataValue('convener')): ?><?php if ($s) echo 'c'; else echo 'C'; $s=true; ?>onvened by 
													<strong>
														<?php $f=false; foreach($this->list->getDataValue('convener',false) as $user): ?>
															<?php if ($f) { echo ", "; } $f=true; ?>
															<?php echo $this->user($user); ?>

														<?php endforeach; ?>
													</strong>
												<?php endif; ?>
												<?php if ($this->list->getDataValue('department')): ?><?php if ($s) echo ', r'; else echo 'R'; $s=true; ?>un by the 	<strong><?php echo $this->list->getDataValue('department'); ?></strong> <?php endif; ?>
												<?php if ($this->list->getDataValue('campus')): ?><?php if ($s) echo 'a'; else echo 'Run a'; $s=true; ?>t <strong><?php echo $this->list->getDataValue('campus'); ?></strong> <?php endif; ?>
												<?php if ($this->list->getDataValue('week_beginning')): ?><?php if ($s) echo 's'; else echo 'S'; $s=true; ?>tarting <strong>week <?php echo $this->list->getDataValue('week_beginning'); ?></strong> <?php endif; ?>
											</h2>
										<?php endif; ?>
									</div> 
								<?php else: ?>
									<div class="head-info">
										<h1><?php echo $this->title; ?></h1>
									</div>
								<?php endif; ?>
								
							</div>
						</div>
						<?php echo $this->flashMessages(); ?>
						<!--<div class="view-tools">
							<div class="plus-minus">
								<a href="#">+</a>
								<a href="#">-</a>
							</div>
							<ul class="view-list">
								<li class="small"><a href="#">small</a></li>
								<li class="medium"><a href="#">medium</a></li>
								<li class="large"><a href="#" class="active">large</a></li>
							</ul>
						</div>-->
					</div>
				<?php endif;?>
				<!-- main-holder -->
				<div class="main-holder">
					<?php echo $this->layout()->content; ?>
				</div>
			</div>
			
		</div>
		<!-- footer -->
		<div id="footer">
			<div class="copyright">
				<div class="holder">
					<p>Copyright &copy; 2010 University of Kent. Icons Copyright © 2010 Yusuke Kamiyamane.<br/>
					list8D is licensed under the <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html">GNU General Public License v2</a>, Zend under the <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html">New BSD License</a> and Fugue Icons under the <a href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 license</a>.</p>
				</div>
			</div>
			<div class="footer-holder">
				<strong class="logo">
					<a href="http://www.kent.ac.uk"><img src="/themes/pontus/images/kent-logo.gif" alt="University of Kent" /></a>
					<a href="http://www.jisc.ac.uk"><img src="/themes/pontus/images/jisc-logo.gif" alt="JISC" /></a>
				</strong>
				<div class="footer-info">
					<div class="footer-row">
						<div class="footer-txt-column">
							<span>powered by</span>
						</div>
						<div class="footer-logo-column">
							<strong class="logo-3"><a href="http://www.list8d.org">list 8D</a></strong>
						</div>
					</div>
					<div class="footer-row footer-row-2">
						<div class="footer-txt-column">
							<span>built on</span>
						</div>
						<div class="footer-logo-column">
							<strong class="logo-4"><a href="http://framework.zend.com">zend framework</a></strong>
						</div>
					</div>
					<div class="footer-row footer-row-3">
						<div class="footer-txt-column">
							<span>icons from</span>
						</div>
						<div class="footer-logo-column">
							<strong class="pinvoke"><a href="http://www.pinvoke.com/">pinvoke</a></strong>
						</div>
					</div>
				</div>
			</div>
	
		</div>
		<?php echo $this->inlineScript(); ?>
	</body>
</html>
