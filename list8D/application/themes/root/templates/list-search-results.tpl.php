<?php if(count($this->searchResults)): ?>
<?php echo $this->renderLists($this->searchResults); ?>
<?php else: ?>
<p>Sorry there were no results.</p>
<?php endif; ?>