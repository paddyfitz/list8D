<?php 

$years = $this->list->getAlternateYears();
$current = $this->list->getDataValue("year");
?>
<?php /*if(count($years) > 1) : ?>
	<div id="alternate-years">
		<ul>
			<?php foreach($years as $id => $year) : ?>
				<li<?php
				if($current == $year) { echo ' class="active"'; }
				?>><a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$id)); ?>"><?php echo $year; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; */ 
?>

<?php if ($current != '2009'): ?>
<div class="flashMessage clearfix info ">
	<img src="/themes/gaia/images/info-icon-small.png">
	<h3>This is <?php echo $current ?>'s list</h3>
	<p>We've removed the year tabs to clean up the interface, and by default will take you 2009's list, but it looks like you've come directly here on this occasion.</p>
	<?php foreach($years as $id => $year) : ?>
		<?php if ($year == '2009'): ?>
			<ul class="actions"><li class="btn with-icon"><a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$id)); ?>" class="button icon icon-arrow">Take me to 2009's list</a></li></ul>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
<?php endif; ?>