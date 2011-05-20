<h1> <?php echo $this->title ?> </h1>

<?php if ($this->success == 1) {	?>
<p> Tag added! Add another?</p>
<?php } ?>


<?php
	echo $this->form;
?>