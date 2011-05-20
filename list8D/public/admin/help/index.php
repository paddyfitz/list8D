<?php

//help page index file

//get the path from the url
//echo "<pre>path = {$_GET['path']}</pre>";

//get the menu file and rip it up.
$menu = new SimpleXmlElement("menu.html", null, TRUE);
//$menu = $menu->body->ul;

if(empty($_GET['path'])) {
	$page = "index.html";
} else {
	$page = $_GET['path'];
}
if(substr($page, -4) != "html") {
	//echo "lo";
	$page .= ".html";
}
//var_dump($page);
try {
	@$contents = new SimpleXmlElement($page, null, TRUE);
} catch (Exception $e) {
	$contents = new SimpleXmlElement("notfound.html", null, TRUE);
}
$title = "".$contents->body->h1;
//unset($contents->body->h1);
//$body = $contents->xpath('/html/body');
//echo "<pre>".$menu->body->ul->asXml()."</pre>";

?>
<html>
	<head>
		<title>List8D help : <?php echo $title; ?></title>
		<style>
			body {
				height: 100%;
				width: 100%;
				overflow: hidden;
				margin: 0;
				padding: 0;
				font-family: Arial, sans;
				font-size: 62.5%;
			}
			h1 {
				margin: 0px;
				font-size:1.8em;
			}

			h2 {
				margin: 0px;
				font-size: 1.4em;
				margin-bottom: 10px;
			}
			h3 {
				font-size: 2.4em;
				margin-top: 0;
			}

			p {
				font-size: 1.4em;
				margin: 10px 0 10px;	
			}
			div#contents ul li {
				font-size: 1.4em;
			}
			div#contents ul li ul li,
			div#contents ul li ul li ul li
			{
				font-size: 1em;
			}
			a:link,
			a:active,
			a:hover,
			a:visited  {
				color: #005594;
			}
			a {
			}
			div#title {
				position: absolute;
				width: 100%;
				margin: 0;
				border-bottom: 1px solid #000;
				background-color: #fff;
			}
			div#menu {
				width: 20%;
				overflow: auto;
				float: left;
				font-size: 1.4em;
				padding-top: 10px;
				padding-left: 10px;
			}
			div#menu ul li {
				margin-top: 3px;
			}
			div#contents {
				width: 75%;
				float: right;
				overflow: auto;
				height: 100%;
				border-left: 1px solid #000;
				padding-left: 10px;
				padding-right: 10px;
				padding-top: 10px;
			}
		</style>
		<link rel="stylesheet" type="text/css" href="/admin/help/js/jquery.treeview.css" />
		<script type="text/javascript" src="/admin/js/jquery-1.4.1.pack.js"></script>
		<script type="text/javascript" src="/admin/help/js/jquery.treeview.pack.js"></script>
		<script>
			$(document).ready(function(){

				// first example
				$("#menu ul").treeview({
					persist: "location",
					collapsed: true,
					unique: true
				});
			});
		</script>
	</head>
	<body>
		<?php /* <div id="title"><h1>Reading Lists help : <?php echo $title; ?></h1></div> */?>
		<div id="menu"><?php echo $menu->body->ul->asXml(); ?></div>
		<div id="contents"><?php foreach($contents->body->children() as $child) {echo $child->asXml();} ?></div>
	
	
	</body>
</html>