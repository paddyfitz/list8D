<h4> Edit User </h4>

<?php if ($this->addsuccess == true) { ?>
<p> User <strong><?php echo $this->escape($this->addeduser) ?></strong> updated successfully.</p>
<?php } ?>

<a href='<?php echo $this->url(array("controller"=>"user", 'action'=>'index', 'id'=>null)); ?>'>List Users</a>

<?php echo $this->editUserForm; ?>