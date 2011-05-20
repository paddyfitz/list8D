<div id="error" class="<?php echo $this->class; ?>">
	<h2><?php echo $this->friendlyText; ?></h2>
	<h4><?php echo $this->code; ?>: <?php echo str_replace("\n", '<br />', htmlspecialchars($this->message, ENT_QUOTES, 'UTF-8')) ?></h4>
	<?php if ($this->description): ?>
		<p><?php echo $this->description; ?>
	<?php endif; ?>
	<?php if($this->supportEmailAddress): ?>
		<div class='support'>
			<h5>Get support</h5>
			<div class="more">
				<p>If you require support please email <a href="mailto:<?php echo $this->supportEmailAddress; ?>?subject=<?php echo $this->supportEmailSubject; ?>&body=<?php echo str_replace("+",' ',urlencode($this->supportEmailBody)); ?>"><?php echo $this->supportEmailAddress; ?></a><?php if (!empty($this->supportEmailBody)): ?>, copy and pasting the following into your email and adding any information that you feel would be helpful<?php endif; ?>.</p>
				<?php if (!empty($this->supportEmailBody)): ?><div class='body'><?php echo $this->supportEmailBody; ?></div><?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="tech">
		<h5>More technical information</h5>
		<div class="more">
			<h6>Error code:</h6>
			<p><?php echo $this->code; ?></p>
			<h6>Message:</h6>
			<p><?php echo str_replace("\n", '<br />', htmlspecialchars($this->message, ENT_QUOTES, 'UTF-8')) ?></p>
			<h6>URL:</h6>
			<p><?php echo $this->url; ?></p>
			<h6>Referring URL:</h6>
			<p><?php echo $this->referringUrl; ?></p>
			<h6>User agent:</h6>
			<p><?php echo $this->userAgent; ?></p>
			<?php if ($this->currentUser->isAllowed('stack-trace','view')): ?>
				<h6>Stack trace:</h6>
				<ol><li><?php echo implode('</li><li>', $this->traces) ?></li></ol>
			<?php endif; ?>
		</div>
	</div>
</div>
<script type="text/javascript">
	var error = $("#error").addClass('js');
	$('h5',error).click(function() {
		var e = $(this);
		e.siblings(".more").toggle();
		if (e.hasClass('open'))
			e.removeClass('open');
		else
			e.addClass('open');
	});
</script>

