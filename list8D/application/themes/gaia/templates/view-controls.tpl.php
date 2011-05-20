<div class="view-tools">
  
  <ul class="view-list">
  	<li class="small"><a href="<?php echo $this->url(array("view"=>'small')); ?>"<?php if ($this->view=='small') echo " class='active'"; ?>>small</a></li>
  	<li class="medium"><a href="<?php echo $this->url(array("view"=>'medium')); ?>"<?php if ($this->view=='medium') echo " class='active'"; ?>>medium</a></li>
  	<li class="large"><a href="<?php echo $this->url(array("view"=>'large')); ?>"<?php if ($this->view=='large') echo " class='active'"; ?>>large</a></li>
  </ul>

</div>
