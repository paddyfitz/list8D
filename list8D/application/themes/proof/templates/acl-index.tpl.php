<h1> Access Control Test </h1>
	
Username: <?php	echo $_SERVER['REMOTE_USER']; ?>
<br />
List: <?php echo $this->list->getTitle(); ?>
<br />
Allowed? <?php echo ($this->aclresult ? "yes :)" : "no :(")?>

<br>

