<h1> Users </h1>
<table border=1>
<tr><th>id</th><th>login</th><th>displayname</th><th>email</th>
	<th>inst-id</th><th>role</th></tr>

<?php 
	foreach ($this->users as $user) {
	  echo "<tr><td>";
      echo $this->escape($user->getId());
	  echo "</td><td>";
	  echo $this->escape($user->getLogin());
	  echo "</td><td>";
	  echo $this->escape($user->getDisplayName());
	  echo "</td><td>";
	  echo $this->escape($user->getEmail());
	  echo "</td><td>";
	  echo $this->escape($user->getInstitutionId());
	  echo "</td><td>";
	  echo $this->escape($user->getRole()->getRoleName());
	  echo "</td><td>";
	  echo "<a href='" .
	  $this->url(array("controller"=>"user","action"=>"view", 'id'=>$user->getId())) .
	  "'>view</a>";
	  echo " / ";
	  echo "<a href='" . 
	  $this->url(array("controller"=>"user","action"=>"edit", 'id'=>$user->getId())) .
	  "'>edit</a>";
	  echo " / ";
	  echo "<a href='" . 
	  $this->url(array("controller"=>"user","action"=>"remove", 'id'=>$user->getId())) .
	  "'>remove</a>";
	  echo "</td></tr>";
	}
?>

</table>
<a href='<?php echo $this->url(array("controller"=>"user","action"=>"add")); ?>'>Add new user</a>
