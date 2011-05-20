<div id="list_<?php echo $this->list->getId(); ?>" class="list trunk-list">
	<h4>Details</h4>
	<?php echo $this->renderData($this->list); ?>
	<?php echo $this->a("edit list data",array("controller"=>"data",'action'=>'edit','id'=>null,'listid'=>$this->list->getId(),'destination'=>$this->getDestination())); ?>
	<h4>Tags</h4>
	<ul class="tags">
	<?php
		$tmids = $this->list->getTagIds();
		foreach ((array) $this->list->getAllTags() as $tag) {
			echo  "<li>";
			echo $this->a($tag->getNameSpace() . ":" . $tag->getTagName(),
				array('controller'=>'tag','action'=>'view', 'id'=>$tag->getId()));
			if (in_array($tag->getId(), $tmids)) {
				echo "  ";
				echo $this->a("x",
					array("controller"=>"tag",'action'=>'listdel', 'id'=>null,'tagid'=>$tag->getId(), 'listid'=>$this->list->getId()));
			}		
			echo "</li>";
	
		}
		echo $this->tagform;
	?>
	</ul>
	
	
	<h4>Items</h4>
	<?php echo $this->renderListItems($this->list); ?>
	<br/>
	<?php echo $this->a("add item to list", array('controller'=>'list','action'=>"additem")); ?> 	<br/>
	<?php echo $this->a("add item to list and then specify where", array('controller'=>'list','action'=>"additem",'position'=>'ask')); ?> 	<br/>
	<?php echo $this->a("duplicate list", array('action'=>"duplicate",'controller'=>"list")); ?><br/>
	<?php echo $this->a("sort list desc by title recusive", array('action'=>"sort",'by'=>"title",'direction'=>'asc','recusive'=>true)); ?><br/>
</div>