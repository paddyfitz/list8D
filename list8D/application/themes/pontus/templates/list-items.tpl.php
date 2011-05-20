<div <?php if (!$this->list->isNested()) echo 'id="list-items"'; ?> class="list-items<?php if ($this->list->isNested()) echo " nested"; else echo "" ?> not-moving">
	<?php if (count($this->list->getChildren())): ?>
 
 		<?php foreach($this->list->getChildren() as $item): ?>
 			<div class="add-item-here-wrapper">
 				<a href="<?php echo $this->baseUrl(); ?>/list/additem/id/<?php echo $this->list->getId(); ?>/position/<?php echo $item->getOrder(); ?>" title="add item to list here" class="add-item-here"><span class='text'>Add item here</span><span class='background'></span><span class='rule'></span></a>
 			</div>
 			<div id="<?php if ($item->isList()) echo "list_"; else { $resource = $item->getResource(); echo "item_"; } echo $item->getId(); ?>" class="list-item <?php echo $item->getType(); if ($item->isList()) echo " open "; else echo " not-list "; ?><?php if($item->getDataValue('part')) echo " part"; ?> sortable-item">
				
				<div class="list-box-content<?php if (!$item->getDataValue("is_published")) echo " unpublished"; ?>">
					
					
			    	<div class="small-list-info">
			    		<p>
			    		  <?php if ($item->isList()): ?>
			    				<?php echo $item->getDataValue("title"); ?>
			   				<?php else: ?>
			    				<?php echo $this->reference($item,$resource); ?>	
			    			<?php endif; ?>
			    			<?php if ($item->getDataValue("core_text")): ?>
			    			  <img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/star.png" alt="Core text" title="Core text" />	
			    			<?php endif; ?>
			    			<?php if ($item->getDataValue("recommended_for_purchase")): ?>
			    			  <img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/currency.png" alt="Recommended for purchase" title="Recommended for purchase" />				
			    			<?php endif; ?>
							<?php if ($item->getDataValue("private_notes") || $item->getDataValue("public_notes")): ?>
			    			   <img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/sticky-note-pin.png" alt="Notes" class="notes-icon" />
			    			<?php endif; ?>
			 				<?php if($item->getDataValue("scan_request") == "scan_requested"): ?>
								<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/camera.png" alt="Scan Requested" class="scan-requested" title="Scan Requested" />
							<?php elseif($item->getDataValue("scan_request") == "scan_available"): ?>
								<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/camera-moodle-blue.png" alt="Scan Available in Moodle" class="scan-available-moodle" title="Scan Available in Moodle" />
							<?php elseif($item->getDataValue("scan_request") == "scan_in_progress"): ?>
								<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/camera-waiting.png" alt="Scan in progress" class="scan-in-progress" title="Scan in progress" />
							<?php endif; ?>
			    		</p>
			    		<?php if ($item->getDataValue("private_notes") || $item->getDataValue("public_notes")): ?>
			    			<div class="notes">
			    				<?php if ($item->getDataValue("public_notes")): ?>
			    			  	<h4>Notes for students</h4>
			    			    <p><?php echo preg_replace("/[\n\r]{2}/","</p><p>",preg_replace("/[\n\r]/","<br/>",$item->getDataValue("public_notes"))); ?></p>
			  					<?php endif; ?>
			    			  <?php if ($item->getDataValue("private_notes")): ?>
			    			    <h4>Notes for librarians</h4>
			    			    <p><?php echo preg_replace("/[\n\r]/","</p><p>",preg_replace("/[\n\r]{2}/","<br/>",$item->getDataValue("private_notes"))); ?></p>
			    			  <?php endif; ?>
			  				</div>
			    		<?php endif; ?>
			    	</div>
			    
			    <div class="buttons">
			    	<?php if (!$item->isList()): ?>
			    		<a href="<?php echo $this->baseUrl(); ?>/item/view/id/<?php echo $item->getId(); ?>" title="open item"><img src="<?php echo $this->baseUrl(); ?>/themes/gaia/images/icon-open.gif" alt="open item" /></a>
			    	<?php else: ?>
			    		<a href="<?php echo $this->baseUrl(); ?>/item/view/listid/<?php echo $item->getId(); ?>" title="open item"><img src="<?php echo $this->baseUrl(); ?>/themes/gaia/images/icon-open.gif" alt="open item" /></a>
			    	<?php endif; ?>
			    	<?php if ($item->isList()) {
			    		$title = $item->getTitle(); 
			    		$key = 'listid'; 
			    	} else {
			    		$title = $resource->getTitle(); 
			    		$key = 'itemid'; 
			    	} ?>
					<?php if (!$this->readOnly): ?>
			    		<a href="<?php echo $this->url(array('controller'=>'item','action'=>'remove','id'=>$this->params['id'],$key=>$item->getId())); ?>" title="remove from list"><img alt='remove "<?php echo $title; ?>" from "<?php echo $item->getList()->getDataValue('title'); ?>"' src='<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/minus-circle.png' /></a>
			    		<a href="<?php echo $this->url(array('controller'=>'data','action'=>'edit',$key=>$item->getId(),'destination'=>array())); ?>" title="edit item"><img alt='edit "<?php echo $title; ?>"' src='<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/pencil.png' /></a>
			    	<?php endif; ?>
					<?php if (!$item->isList()) : ?>
			
			    		<?php if ($resource->getDataValue('meta_url',false)) : ?>
			    			<div class="meta_link">
			    				<?php $links = $resource->getDataValue('meta_url',false); $links2 = $links; ?>
			    		  	<a href="<?php $firstLink = array_shift($links2); echo $firstLink['url']; ?>">
			    		  		<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/tag-label-arrow.png" alt="view more resource information" />
			    		  	</a>
			    		  	<div class="links">
			  			  		<ul>
			  			  			<li>View more info on this resource in:
			  				  			<ul>
			  					  			<?php foreach($links as $link): ?>
			  					  				<li>
			  					  					<a href="<?php echo $link['url']; ?>"><?php echo $link['text']; ?></a>
			  					  				</li>
			  					  			<?php endforeach; ?>
			  				  			</ul>
			  				  		</li>
			  			  		</ul>
			  			  	</div>
			    		  </div>
			    		<?php endif; ?>
			    		
			    		<?php if($resource instanceof List8D_Model_Resource_Journal || $resource instanceof List8D_Model_Resource_JournalArticle ): ?>
			  	  		<a href="<?php echo List8D_Model_Metatron_KentSerialsSolutions::createSSLink($item); ?>" title="view online resource" class='link'><img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/globe-green-arrow.png" alt="view online resource" /></a>
			    		<?php elseif (is_string($resource->getDataValue('url',false))) : ?>
			    		  <a href="<?php $displayurl = $resource->getDataValue('url',false); if(substr($displayurl,0,4)=="http"){echo $displayurl;}else{echo "http://".$displayurl;} ?>" title="view online resource" class='link'><img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/globe-green-arrow.png" alt="view online resource" /></a>
			    		<?php elseif (is_array($resource->getDataValue('url',false)) && count($resource->getDataValue('url',false))) : ?>
			    		  <div class="link">
			    		  	<?php $links = $resource->getDataValue('url',false); $links2 = $links; ?>
			  	  		  	<?php $firstLink = array_shift($links2); ?>
			    		  		<?php if (is_string($firstLink['url'])): ?>
			  	  		  		<a href="<?php if(substr($firstLink['url'],0,4) == "http"){echo $firstLink['url'];}else{echo "http://".$firstLink['url'];} ?>">
			    		  		<?php endif; ?>
			  			  		  	<img src="<?php echo $this->baseUrl(); ?>/themes/root/images/fugue/globe-green-arrow.png" alt="view online resource" />
			    		  		<?php if (is_string($firstLink['url'])): ?>	  		  	
			  		  		  	</a>
			  		  		  <?php endif; ?>
			    		  	<div class="links">
			  			  		<ul>
			  			  			<?php foreach($resource->getDataValue('url',false) as $link): ?>
			  			  				<li>
			  			  					<?php echo $link['label']; ?>
			  				  				<ul>
			  			  						<?php if (is_array($link['url'])): ?>
			  			  							<?php foreach($link['url'] as $link2): ?>
			  				  						  <li><a href="<?php if(substr($link2['url'],0,4) == "http"){echo $link2['url'];}else{echo "http://".$link2['url'];} ?>"><?php echo $link2['text']; ?></a></li>
			  			  							<?php endforeach; ?>
			  			  						<?php else: ?>
			  			  						  <li><a href="<?php if(substr($link['url'],0,4)=="http"){echo $link['url'];}else{echo "http://".$link['url'];} ?>">
			  			  						  	<?php if (!empty($link['text'])): ?>
			  			  						  		<?php echo $link['text']; ?>
			  			  						  	<?php else: ?>
			  				  						  	<?php echo $link['url']; ?>
			  				  						  <?php endif; ?>
			  			  						  </a></li>
			  			  						<?php endif; ?>
			  			  						
			  			  					</ul>
			  			  				</li>
			  			  			<?php endforeach; ?>
			  			  		</ul>
			  			  	</div>
			    		  </div>
			    		<?php endif; ?>
			    		
			    	<?php endif; ?>
			    </div>
			    
			    <?php if($item->isList()): ?>
			    	<?php echo $this->partial("list-items.tpl.php",array("list"=>$item,"nested"=>true,"readOnly"=>$this->readOnly,'params'=>$this->params)); ?>
			    <?php endif; ?>
			    
			  </div> 
			  	<div class="type-icon">
			    <?php if ($item->isList()) $type = "list"; else $type = 'item'; ?>
				<?php if(!$this->readOnly): ?>
			    	<a href="<?php echo $this->url(array('controller'=>'list','action'=>'moveitem',$type."id"=>$item->getId())); ?>" class="move"><span class='text'>move "<?php echo $item->getTitle(); ?>"</span></a>
				<?php endif; ?>
			  </div>		
			</div>
  
  		
	 	<?php endforeach; ?>
		 <div class="add-item-here-wrapper">
		 	<a class="add-item-here end"  href="<?php echo $this->baseUrl(); ?>/list/additem/id/<?php echo $this->list->getId(); ?>/position/<?php echo count($this->list->getChildren()); ?>" title="add item to list here"><span class='text'>Add item here</span><span class='background'></span><span class='rule'></span></a>
		 </div>
	<?php else: ?>
  	<p class="no-items">There are no items on this list yet.</p>
	<?php endif; ?>
</div>