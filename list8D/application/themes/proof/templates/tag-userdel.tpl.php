<h1> <?php echo $this->title ?> </h1>

<?php if ($this->success == 1) {	?>
<p> Tag removed!</p>
<?php } ?>

<ul>
	<p>Remove
<?php echo $this->usermap->getUser()->getLogin();?>
		from <?php echo $this->usermap->getTag()->getTagname(); ?> ?
	</p>
</ul>

<?php
	echo $this->confirmForm;
?>