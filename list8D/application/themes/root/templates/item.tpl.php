<?php echo $this->a("item's list: ".$this->item->getList()->getTitle(),array('controller'=>'list','id'=>$this->item->getList()->getId())); ?>

<?php if ($this->item->getResource()->getDataValue('Amazon_thumbnailUrl_large')) :?>
<img class="cover" src="<?php echo $this->item->getResource()->getDataValue('Amazon_thumbnailUrl_large'); ?>" alt="cover art for <?php echo $this->item->getTitle(); ?>"/>
<?php endif; ?>
<h4>Details</h4>
<?php echo $this->renderData($this->item); ?>
<?php echo $this->a("edit item data",array("controller"=>"data",'action'=>'edit','id'=>null,'itemid'=>$this->item->getId(),'destination'=>$this->getDestination())); ?>
<h4>Resource</h4>
<dl>
<?php
foreach ($this->item->getResource()->getData() as $k => $v) {
	echo "<dt>" . (!empty($v['title']) ? $v['title'] : $k)  . "</dt><dd>" . $v['value'] . "</dd>";
}
?>
</dl>




