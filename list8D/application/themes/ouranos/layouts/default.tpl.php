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
					<script type="text/javascript">
						$().ready(function(){
							var q = $("[name=q]");
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
<li>
<a href="#facultylink#">#facultytitle#</a> / <a href="#schoollink#">#schooltitle#</a> / <a href="#stagelink#">#stagetitle#</a>
</li>
					</ul>
				</div>
			</div>
			
			<?php if (isset($this->title) && $this->displayTitle): ?>
				<h3><?php echo $this->title; ?></h3>
			<?php endif; ?>
			<?php if (isset($this->list)):?>
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
				</h2>
			<?php endif;?>
			<?php echo $this->flashMessages(); ?>
			<?php echo $this->layout()->content; ?>
				
			<!-- footer -->
			<div id="footer">
				<div class="instituion-notice">
					<div class="holder">
<p></p>
					</div>
				</div>
				
				<div class="copyright-license">
				  <p class="copyright">
				  	Content Copyright &copy; 2010 <?php echo $this->instituition; ?><br/>
				  	Design Copyright &copy; 2010 University of Kent<br/>
				  	Icons Copyright &copy; 2010 Yusuke Kamiyamane
				  </p>
				  <p class="license">
				  	Zend is licensed under the <a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html">New BSD License</a><br/>
				  	Fugue Icons are licensed under the <a href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 license</a>
				  </p>
				</div>
			
			</div>	
		</div>	
		<?php echo $this->inlineScript(); ?>
	</body>
</html>
