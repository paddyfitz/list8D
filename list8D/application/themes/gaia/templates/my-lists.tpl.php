<?php if (count($this->lists)): ?>
	<ul class='my-lists lists'>
		<?php foreach($this->lists as $list): ?>
			<li class='list'>
				<?php echo $this->partial('lists-list.tpl.php',array('list'=>$list)); ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p>If you do not have access to any lists and you think you should, please contact <a href="mailto:readinglisthelp@kent.ac.uk">readinglisthelp@kent.ac.uk</a>.</p> 
<?php endif; ?>