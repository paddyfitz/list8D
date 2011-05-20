<h4> View User </h4>

<dl>
	<dt>Login</dt>
	<dd><?php echo $this->escape($this->user->getLogin()); ?></dd>

	<dt>Name</dt>
	<dd><?php echo $this->escape($this->user->getDisplayName()); ?></dd>

	<dt>Email Address</dt>
	<dd><?php echo $this->escape($this->user->getEmail()); ?></dd>

	<dt>Role</dt>
	<dd><?php echo $this->escape($this->user->getRole()->getRoleName()); ?></dd>

	<dt>Institution ID</dt>
	<dd><?php echo $this->escape($this->user->getInstitutionId()); ?></dd>
</dl>

<h4>Tags</h4>
<?php
	$tags = $this->user->getTags();
	if (empty($tags)) {
		?>
		<p>No tags were found for this user.</p>
		<?php
	} else {
		?>
		<ul>
		<?php
		foreach ((array) $tags as $tag) {
			?>
			<li><?php echo $this->escape($tag->getNamespace())?>:<?php echo $this->escape($tag->getTagName())?>
			<?php
		}
		?>
		</ul>
		<?php
	}
?>

<a href='<?php echo $this->url(array("controller"=>"user", 'action'=>'index', 'id'=>null)); ?>'>List Users</a>
