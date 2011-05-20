<div id="content" class="with-list">
	<div class="view-controls">
		<a href="<?php echo $this->url(array("custom_view"=>'reference'), null, false, false, false); ?>" class="icon icon-ref">reference view</a>
	
		<a href="<?php echo $this->url(array("custom_view"=>null), null, false, false, false); ?>" class="icon icon-full-view">full view</a>
		<?php /*if ($this->user && $this->user->isAllowed($this->list,'edit')): ?>
			<a href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/admin/list/view/id/<?php echo $this->list->getId(); ?>" class="icon icon-edit">edit list</a>
		<?php endif;*/ ?>
	</div>
	
	<?php if ($this->list->getDataValue('public_notes')): ?>
		<p class="student-notes"><?php echo preg_replace("/[\n\r]/","</p><p>",preg_replace("/[\n\r]{2}/","<br/>",$this->list->getDataValue('public_notes'))); ?></p>
	<?php endif; ?>
	<?php if ($this->list->getChildren()): ?>
		<?php echo $this->partial('list-items.tpl.php',array('list'=>$this->list)); ?>
	<?php else: ?>
		<p>This Reading List website does not have any details for this list. Your module convenor/lecturer may have provided alternative information in <a href="https://moodle.kent.ac.uk">Moodle</a>. <?php if ($this->list->getDataValue('convener')): ?><p>If not, please contact your course convener, <?php $user = new List8D_Model_User(); $i=0; foreach($this->list->getDataValue('convener',false) as $convener) { $i++; $user->find($convener); echo "<a href=\"mailto:".$user->getEmail()."\">".$user->getDisplayName()."</a>";  if ($i!=1 && $i!=count($this->list->getDataValue('convener',false)) && $i!=count($this->list->getDataValue('convener',false))-1) echo ", "; else if ($i==count($this->list->getDataValue('convener',false))-1) echo " or "; } ?>.</p><?php endif; ?>
	<?php endif; ?>
</div>
