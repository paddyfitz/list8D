<!-- content -->
<div id="content" class="<?php echo get_class($this->report); ?>">
	<?php if ($this->report->getMessage()): ?>
		<p><?php echo $this->report->getMessage(); ?></p>
	<?php endif; ?>
	<table class="lists" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<?php foreach ($this->report->getFields() as $key => $th): ?>
					<th class="<?php echo $key; ?>"><?php echo $th['label']; ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			
			<?php if ($this->report->getItems()) : ?>
				<?php $i=0; foreach ($this->report->getItems() as $tr): $i++; $class=""; ?>
					<?php if(!($i%2)) $class .= " even" ; else $class .= " odd"; ?>
					<?php if($i===1) $class .= " first"; ?>
					<?php if($i===count($this->report->getItems())) $class .= " last"; ?>
					<tr class='<?php echo $class; ?>'>
						<?php foreach($tr as $td): ?>
							<td>
								<?php echo $td; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan='<?php echo count($this->report->getFields()); ?>'>Sorry no <?php echo implode("s or ",$this->report->getItemTypes()); ?>s could be found that matched your search or filters.</td>
				</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan='<?php echo count($this->report->getFields()); ?>'><?php echo $this->pager($this->report->getPage(),$this->report->getPageCount(),get_class($this->report)."-page"); ?></th>
			</tr>
		</tfoot>
	</table>
</div>

<?php if (count($this->report->getFilters())): ?>
	<!-- sidebar -->
	<div id="sidebar">
	  <!-- sidebar-box -->
	  <div class="sidebar-box">
	  	<div class="holder">
	  		<div class="frame">
	  			<h3>Filters</h3>
	 				<?php foreach ($this->report->getFilters() as $filter): ?>
		 				<div class="info-box">
							<h4><?php echo $filter['title']; ?></h4>
							<?php echo $filter['value']; ?>
	 					</div>
	 				<?php endforeach; ?>
		 				
	
	  		</div>
	  	</div>
	  </div>
	</idv>
<?php endif; ?>