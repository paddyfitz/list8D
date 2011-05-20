<h2>My Lists</h2>
<?php if ($this->user->isAllowed("list","edit")): ?>
	<p>You have access to edit all lists in the system. To look for a specific list, please use the search function.</p>
	<h2>Lists on which you have been explicitly tagged:</h2>
<?php endif;?>
<?php echo $this->partial('my-lists.tpl.php',array('lists'=>$this->user->getMyLists())); ?>

<?php if (count($this->user->getRecentLists())): ?>
	<h2>Recent Lists</h2>
	<?php echo $this->partial('recent-lists.tpl.php',array('lists'=>$this->user->getRecentLists())); ?>
<?php endif; ?>