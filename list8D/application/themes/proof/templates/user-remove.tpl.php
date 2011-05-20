<h4> Remove User </h4>

<?php if ($this->user->getId() != 0) { ?>
	<p>
	Please confirm that you wish to delete user <strong><?php echo $this->user->getLogin(); ?></strong>?
	</p>
	<?php echo $this->confirmForm; ?>
<?php } else { ?>
	<p>Ooops, that user doesn't seem to exist.	</p>
<?php } ?>
	<a href='<?php echo $this->url(array("controller"=>"user", 'action'=>'index', 'id'=>null)); ?>'>
		Cancel and return to user list?</a>
