<h1> <?php echo $this->title ?> </h1>

<?php if ($this->success == 1) {	?>
<p> User added! Add another?</p>
<?php } ?>

<ul>
<?php
foreach ($this->tag->getUsers() as $user) {
	echo "<li>" . $user->getLogin() . ": " . $user->getDisplayName() . "</li>";
}
?>
</ul>

<?php
	echo $this->form;
?>