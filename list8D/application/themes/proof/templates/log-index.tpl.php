<h4>Logs</h4>

<?php echo $this->form; ?>

<table>
	<tr>
		<th>id</th>
		<th>action</th>
		<th>table</th>
		<th>row_id</th>
		<th>changed</th>
		<th>user</th>
		<th>column</th>
		<th>value_from</th>
		<th>value_to</th>
	</tr>
	<?php //print_r($this->paginator) ?>
	<?php if (count($this->paginator)): ?>

	<?php foreach ($this->paginator as $item): ?>
  <tr>
		<td><?php echo $item['id'] ?></td>
		<td><?php echo $item['action'] ?></td>
		<td><?php echo $item['table'] ?></td>
		<td><?php echo $item['row_id'] ?></td>
		<td><?php echo $item['changed'] ?></td>
		<td><?php echo $item['user'] ?></td>
		<td><?php echo $item['column'] ?></td>
		<td><?php echo $item['value_from'] ?></td>
		<td><?php echo $item['value_to'] ?></td>
	</tr>
	<?php endforeach; ?>
	<?php endif; ?>

</table>

<?php echo $this->paginationControl($this->paginator,'Sliding','pagination.tpl.php'); ?>