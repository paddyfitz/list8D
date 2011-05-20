<p>Please select the type of resource you would like to add.</p>
<ul>
    <?php foreach($this->types as $type): ?>
        <li><?php echo $this->a($type->getTypeName(),array('type'=>$type->getType())); ?></li>
    <?php endforeach; ?>

</ul>