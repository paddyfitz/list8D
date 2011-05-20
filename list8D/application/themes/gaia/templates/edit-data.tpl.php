<?php 
if ($this->getDestination()==$this->url()) {
  $destination = $this->url(array('controller'=>'list','action'=>'view','id'=>$this->item->getTrunkId()),null,false,true,true)."#".$this->item->getAccessResourceType()."_".$this->item->getId();
} else {
  $destination = $this->getDestination(); 
} 

if ($searchAgain = $this->editDataForm->getElement("searchagain")) {
	$searchAgain=$searchAgain->setDecorators(array('ViewHelper'))->render();
	$this->editDataForm->removeElement("searchagain");
} else {
	$searchAgain = "";
}
$this->editDataForm->getElement('submit')->setDecorators(array(
  		'ViewHelper',
  		'Description',
  		'Errors',
  		array(array('data'=>'HtmlTag'), array('tag' => 'div','class'=>'submit','prepend'=>"<a href='{$destination}#".($this->item->isList() ? "list" : "item") ."_".$this->item->getId()."' class='cancel icon cancel-icon'>cancel</a>",'append'=>$searchAgain)),	
	  	));
	  	
if ($this->thenEdit) {
	
} else {

}
?>
<!-- content -->
<div id="content">
	<!-- box-holder -->

	<div id="box-holder" class="box-holder">	
		<div class="list-item <?php echo $this->item->getType(); ?> open">
			<div class="tl">
			  <div class="tr">
			  	<div class="tc">&nbsp;</div>
			  </div>
			</div>
			<div class="c">
			  <div class="cl">
			  	<div class="clc">
				  	<div class="type-icon">
  						<a class="move" href=""><span class="text">move "Economics"</span></a>
						</div>
			  		<div class="list-box-content">
			  			<?php if ($this->item->getType()=="Book" || $this->item->getType()=="BookChapter" || $this->item->getType()=="Legacy") : ?>
	  						<h2><strong><?php echo $this->item->getResource()->getDataValue("published"); ?></strong> <?php echo $this->item->getResource()->getDataValue("title"); ?>&nbsp;</h2>
  							<em class="details"><?php echo $this->item->getResource()->getDataValue("authors"); ?>&nbsp;</em>
  						<?php elseif ($this->item->getType()=="List"): ?>	
	  						<h2><strong><?php echo $this->item->getDataValue("title"); ?></strong>&nbsp;</h2>
  							<em class="details"><?php echo count($this->item->getChildren()); ?> item<?php if (count($this->item->getChildren())!==1) echo "s"; ?> on list&nbsp;</em>
  						<?php elseif ($this->item->getType()=="Url"): ?>	
	  						<h2><strong><?php echo $this->item->getResource()->getDataValue("title"); ?></strong>&nbsp;</h2>
  							<em class="details"><?php echo $this->item->getResource()->getDataValue("url"); ?>&nbsp;</em>
  						<?php endif; ?>
			  			<div class="box-content<?php if(!$this->item->isList() && $this->item->getResource()->getDataValue('Amazon_thumbnailUrl_large') || $this->item->getType() == 'Url') echo " content-img"; ?>">
			  				<?php if((!$this->item->isList() && $this->item->getResource()->getDataValue('Amazon_thumbnailUrl_large'))) : ?>
			  					<img class='img-book' src="<?php echo $this->item->getResource()->getDataValue('Amazon_thumbnailUrl_large'); ?>" alt="book cover" />
								<?php elseif($this->item->getType() == "Url"): ?>
									<?php
									$thumboo_api = "50be95df663d62cf93bc285444e37660";
									$thumboo_url = $this->item->getResource()->getDataValue("url");
									$thumoo_params = "u=".urlencode("http://".$_SERVER["HTTP_HOST"].
									$_SERVER["REQUEST_URI"])."&su=".urlencode($thumboo_url)."&c=medium&api=".$thumboo_api;
									$image = file_get_contents("http://counter.goingup.com/thumboo/snapshot.php?".$thumoo_params);
									$image = str_replace("<img ","<img class='img-url' ",$image);
									echo $image; ?>
			  				<?php endif; ?>
			  				
  							<?php echo $this->editDataForm; ?>
			  			</div>
			  			
			  			
  						<a href="<?php echo $destination; ?>#<?php echo $this->item->isList() ? "list" : "item" ; ?>_<?php echo $this->item->getId(); ?>" class="close"><img src="<?php echo $this->baseURl(); ?>/themes/gaia/images/icon-close.gif" alt="collapse" /></a>
			  		</div>
			  	</div>
			  </div>
			</div>
			<div class="b">
			  <div class="bl">
			  	<div class="br">&nbsp;</div>
			  </div>
			</div>
		</div>
	</div>
</div>

<!-- sidebar -->
<div id="sidebar">
<?php if ($this->currentUser->isAllowed('report','view')): ?>
	<?php if(!$this->item->isList()): ?>
	
	  <div class="sidebar-box">
	  	<div class="holder">
	  		<div class="frame">
	  			<h3>Other places this resource appears</h3>
					<div class="info-box">
						<?php $items = $this->item->getResource()->getItems(array($this->item->getId())); ?>
	  				<?php if (count($items)): ?>
	  					<ul class="lists">
			  				<?php foreach($items as $item): ?>
									<li><a href="<?php echo $this->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunk()->getId())); ?>#item_<?php echo $item->getId(); ?>"><strong><?php echo $item->getTrunk()->getTitle(); ?></strong><br/><?php echo $item->getTrunk()->getDataValue('code'); ?> - Item <?php echo $item->getId(); ?></a></li>
	  						<?php endforeach; ?>
	  					</ul>
						<?php else: ?>
		  				<p>This resource is on no other lists.</p>
	  				<?php endif; ?>
	  			
	  			</div>
	  		</div>
	  	</div>
		</div>
	<?php endif; ?>
<?php endif; ?>
</div>