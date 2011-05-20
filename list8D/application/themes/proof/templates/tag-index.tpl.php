<h1> Tags </h1>
	
<ul>
	<?php
	foreach ($this->tags as $tag) {
		if ($tag->getParentId() == null) {
			echo $this->renderTagChildren($tag, $this);
		}
	}
	?>
</ul>


<h4> Add new tag
<?php
	echo $this->form;
?>