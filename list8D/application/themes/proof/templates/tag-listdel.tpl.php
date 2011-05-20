<h1> <?php echo $this->title ?> </h1>

<?php if ($this->success == 1) {	?>
<p> Tag removed!</p>
<?php } ?>

<ul>
	<p>Remove
<?php echo $this->tagmap->getTag()->getNamespace() . ':' .
	$this->tagmap->getTag()->getTagName();
?> from <?php echo $this->tagmap->getList()->getTitle(); ?> ?
	</p>
</ul>

<?php
	echo $this->confirmForm;
?>