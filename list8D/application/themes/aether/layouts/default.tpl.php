<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title><?php echo $this->title ?></title>
		
		<?php echo $this->headLink(); ?>
		<?php echo $this->headScript(); ?>
		
	</head>
	<body>
		<div id="content">
			<?php echo $this->flashMessages(); ?>
			<?php if (isset($this->tabs)): ?>
				<ul class="tabs clearfix">
					<?php foreach($this->tabs as $key => $list): ?>
						<li<?php if($key == $this->selectedTab) echo " class='selected'"; ?>>
							<a href="<?php echo $this->url(array('controller'=>'lti','action'=>'list','custom_tabId'=>$key)); ?>">
								<?php echo $list->getDataValue('code'); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div class="tab-content">
				<?php echo $this->layout()->content; ?>
			</div>
		</div>
		
		<?php echo $this->inlineScript(); ?>
		
	</body>
</html>
