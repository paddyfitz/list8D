<!-- content -->
<div id="content">
	<table class="lists" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th class='codes'>Code</th>
				<th class='names'>Name</th>
				<th class='tags'>Tags</th>
				<th class='actions'>Actions</th>
			</tr>
		</thead>
		<tbody>
			
			<?php if ($this->lists) : ?>
				<?php $i=0; foreach ($this->lists as $list): $i++; ?>
					<tr <?php if(!($i%2)) echo "class='even'"; else echo "class='odd'"; ?>>
						<td><a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$list->getId())); ?>"><?php echo $list->getDataValue('code'); ?></a></td>
						<td><a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$list->getId())); ?>"><?php echo $list->getDataValue('title'); ?></a></td>
						<td><?php echo implode(', ',$list->getTags()); ?></td>
						<td>
							<a class='icon icon-edit' href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$list->getId())); ?>">edit</a>
							<a class='icon icon-delete' href="<?php echo $this->url(array('controller'=>'list','action'=>'delete','id'=>$list->getId())); ?>">delete</a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan='4'>Sorry no lists could be found that matched your search.</td>
				</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan='4'><?php echo $this->report->pager(); ?></th>
			</tr>
		</tfoot>
	</table>
</div>

<!-- sidebar -->
<div id="sidebar">

  <!-- sidebar-box -->
  <div class="sidebar-box">
  	<div class="holder">
  		<div class="frame">
  			<h3>Find lists</h3>
 				
 				<div class="info-box">
					<?php echo $this->findListsForm; ?>
 				</div>
 				

  		</div>
  	</div>
  </div>