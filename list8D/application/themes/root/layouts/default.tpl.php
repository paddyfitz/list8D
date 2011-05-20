<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />

<title><?php echo $this->title ?></title>

<?php echo $this->headLink(); ?>
<?php echo $this->headScript(); ?>
<!-- add your meta tags here -->

<!--[if lte IE 7]>
<link href="css/patches/patch_layout.css" rel="stylesheet" type="text/css" />
<![endif]-->

</head>
<body>
  <div class="page_margins">
    <div class="page">
      <div id="header">
      	<h1><?php echo $this->instituition; ?></h1>
       	<h2><?php echo $this->applicationName; ?></h2>
      </div>
      <div id="nav">
        <!-- skiplink anchor: navigation -->
        <a id="navigation" name="navigation"></a>
        <div class="hlist">
          <!-- main navigation: horizontal list -->
          <ul>
            <li class="lists"><a href="<?php echo $this->baseUrl() ?>/">Lists</a>
            	<ul class="dlist">
            		<li>
            			<form action="<?php echo $this->baseUrl() ?>/list/search" method="post" enctype="application/x-www-form-urlencoded">
            				<label class="required" for="title">Search</label><br/>
										<input type="text" value="" id="title" name="title"/>
										<input type="submit" value="Search" id="submit" name="submit"/>
									</form>
								</li>
								<?php if (!empty($this->recentLists)): ?>
            		<li>Recently viewed lists
            			<ul>
            				<?php foreach($this->recentLists as $list): ?>
            					<li><?php echo $this->a($list->getTitle(), array('controller'=>'list','action'=>"view",'id'=>$list->getId())); ?></li>
            				<?php endforeach; ?>
            			</ul>
            		</li>
            		<?php endif; ?>
            	</ul>
            </li>
            <li><a href="<?php echo $this->baseUrl() ?>/log">Logs</a></li>
            <li><a href="<?php echo $this->baseUrl() ?>/log">Testing stuff</a>
            	<ul>
            		<?php
									foreach(array("kentvoyager", "url", "amazon", "googlebooks", "tag", "log", "user" ) as $i ) {
										echo "<li><a href='".$this->baseUrl()."/$i'>$i</a></li>";
									}
								?>
							</ul>
						</li>
          </ul>
        </div>
      </div>
      <div id="page-header">
      <h3><?php echo $this->title; ?></h3>
      </div>

      <div id="main">
      
       
       <?php echo $this->layout()->content; ?>
       
      </div>
      <div id="footer">
      	<div class="subcolumns">
          <div class="c50l">
            <div class="subcl">
	            Funded by <a href="http://www.jisc.ac.uk/">JISC</a><br/>
            	Funded and developed by the <a href="http://www.kent.ac.uk/">University of Kent</a>
            </div>
          </div>
          <div class="c50r">
            <div class="subcr">
            	Powered by <a href="http://www.list8d.org/">list8D</a><br/>
            	Built on <a href="http://framework.zend.com/">Zend Framework</a><br/>
            	Layout based on <a href="http://www.yaml.de/">YAML</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
	<?php echo $this->inlineScript(); ?>
</body>
</html>
