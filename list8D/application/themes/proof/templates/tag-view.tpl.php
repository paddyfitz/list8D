<h3> <?php echo $this->escape($this->tag->getNamespace() . ':' . $this->tag->getTagName()) ?> </h1>

<ul>
	<?php
	$me = $this->tag->getId();
	foreach ($this->tag->fetchAll() as $tag) {
		if ($tag->getParentId() == null) {
			echo $this->renderTagChildren($tag, $this, $me);
		}
	}
	?>
</ul>

<h4> Set new Parent </h4>
<?php
	echo $this->form;
?>

<h4> Users </h4>
<ul>
	<?php
	foreach ((array) $this->tag->getUsers() as $user) {
		echo "<li>";
		echo $this->a($user->getLogin() . ": " . $user->getDisplayName(),
		array("controller"=>"user","action"=>"view", "id"=>$user->getId()) );
		echo "  ";
		echo $this->a("x",
			array("controller"=>"tag",'action'=>'userdel', 'id'=>null,'tagid'=>$this->tag->getId(), 'userid'=>$user->getId()));

		echo "</li>";
	}
	?>
</ul>

<?php echo $this->a("Add user", array("controller"=>"tag","action"=>"adduser", 'id'=>null, 'tagid'=>$this->tag->getId())); ?>

<h4> Lists </h4>
<ul>
	<?php
	foreach ((array) $this->tag->getLists() as $list) {
		echo "<li>";
		echo $this->a($list->getTitle(), array("controller"=>"list","action"=>"view", "id"=>$list->getId()) );
		echo "</li>";
	}
	?>
</ul>

