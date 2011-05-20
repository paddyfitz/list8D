<!-- content -->
<div id="content">
	<table class="users" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th class='usernames'>Username</th>
				<th class='displayNames'>Display name</th>
				<th class='actions'>Actions</th>
			</tr>
		</thead>
		<tbody>
			
			<?php if ($this->users) : ?>
				<?php $i=0; foreach ($this->users as $user): $i++; ?>
					<tr <?php if(!($i%2)) echo "class='even'"; else echo "class='odd'"; ?>>
						<td><a href="<?php echo $this->url(array('controller'=>'user','action'=>'view','id'=>$user->getId())); ?>"><?php echo $user->getLogin(); ?></a></td>
						<td><a href="<?php echo $this->url(array('controller'=>'user','action'=>'view','id'=>$user->getId())); ?>"><?php echo $user->getDisplayName(); ?></a></td>
						<td>
							<a class='icon icon-edit' href="<?php echo $this->url(array('controller'=>'user','action'=>'edit','id'=>$user->getId())); ?>">edit</a>
							<a class='icon icon-delete' href="<?php echo $this->url(array('controller'=>'user','action'=>'delete','id'=>$user->getId())); ?>">delete</a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan='4'>Sorry no users could be found that matched your search.</td>
				</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan='4'><?php echo $this->pager(); ?></th>
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
  			<h3>Find users</h3>

 				<div class="info-box">
					<?php echo $this->findUserForm; ?>
 				</div>

 				<div id="users-last-name-filter" class="info-box">
 					<h4>Filter by username beginning...</h4>
					<ul>
						<?php if (isset($this->filter)): ?>
							<li><a href="<?php echo $this->url(array('controller'=>'user','filterusername'=>null,'page'=>null)); ?>">Remove filter (<?php echo $this->filter; ?>)</a></li>
						<?php else: ?>
							<?php foreach(array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z') as $value): ?>
								<li><a href="<?php echo $this->url(array('controller'=>'user','filterusername'=>$value,'page'=>null)); ?>"><?php echo strtoupper($value); ?></a></li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
 				</div>
 				
 				<div class="info-box">
 					<span class='box-link'>
	 					<a class='icon icon-create' href="<?php echo $this->url(array('controller'=>'user','action'=>'edit','id'=>null)); ?>">create new user</a>
	 				</span>
 				</div>
 				

  		</div>
  	</div>
  </div>