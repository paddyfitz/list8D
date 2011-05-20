<?php if (count($this->lists)): ?>
	<ul class='my-lists lists'>
		<?php foreach($this->lists as $list): ?>
			<li class='list'>
				<?php echo $this->partial('lists-list.tpl.php',array('list'=>$list)); ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p>You haven't view any lists yet.</p>
<?php endif; ?>