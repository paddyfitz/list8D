<?php if ($this->pages>1): ?>
	<div class="pages clearfix">
  	<div class="page-info">Viewing page <?php echo $this->currentPage; ?> of <?php echo $this->pages; ?></div>
  	<div class="pager clearfix">
  		<?php if ($this->currentPage!==1): ?>
  		  <a class='icon first-page' href='?listId=<?php echo $this->listId; ?>'>first</a>
  		  <a class='icon previous-page' href='?listId=<?php echo $this->listId; ?>&page=<?php echo $this->currentPage - 1; ?>'>previous</a>
  		<?php endif; ?>
  		<?php for($i=0;$i<$this->pages;$i++): ?>	
  			<a class="page<?php if ($i==$this->currentPage -1) echo " current"; ?>" href='?listId=<?php echo $this->listId; ?>&page=<?php echo $i+1; ?>'><?php echo $i+1; ?></a> 
  		<?php endfor; ?>
  		<?php if ($this->currentPage!==$this->pages): ?>
  		  <a class='icon next-page' href='?listId=<?php echo $this->listId; ?>&page=<?php echo $this->currentPage + 1; ?>'>next</a>
  			<a class='icon last-page' href='?listId=<?php echo $this->listId; ?>&page=<?php echo $this->pages; ?>'>last</a>
  		<?php endif; ?>
  	</div>
  </div>
<?php endif; ?>