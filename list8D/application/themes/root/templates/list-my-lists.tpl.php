<?php if(count($this->searchResults)): ?>
<?php echo $this->renderLists($this->searchResults); ?>
<?php else: ?>
<p>We could not find any lists belonging to you.</p>
<?php endif; ?>