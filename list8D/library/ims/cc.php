<html>
<head>
  <title>IMS Basic Learning Tools Interoperability</title>
</head>
<body style="font-family:sans-serif">
<script type="text/javascript">
function hideshow(which){
if (!document.getElementById)
return
if (which.style.display=="block")
which.style.display="none"
else
which.style.display="block"
}
</script>
<div id="control">
<img src="http://www.php.net/images/logos/php-med-trans-light.gif" align="right"/>
<p><b>IMS BasicLTI PHP Common Cartridge Upload</b></p>
<p>This is a very simple IMS Common Cartridge Reader.</p>
<?php

  require_once("misc.php");

  $testman = zip_open_and_read_entry("basiclti-sample-cartridge-01.zip", "imsmanifest.xml");
  if ( ! $testman ) { 
    echo('<p style="color:red"><b>Common Cartridge upload will probably fail because this php does not have the zip_open() function available.<b></p>');
    echo("\n");
  }

  $cur_url = curPageURL();
  $lms = str_replace("cc.php","lms.php",$cur_url);
  $org_id = $_REQUEST["org_id"];
  if ( ! $org_id ) $org_id = "lmsng.school.edu";
  $org_secret = $_REQUEST["secret"];
  if ( ! $org_secret ) $org_secret = "secret";
  $file_name = $_FILES["file"]["tmp_name"];
?>
<form method="post" enctype="multipart/form-data">
<fieldset>
<legend>Select And IMS Common Cartridge file</legend>
<input type="file" name="file" size="50"/ ><br/>
<p>LMS Key: <input type"text" name="org_id" value="<?php echo($org_id); ?>">
<p>LMS Secret: <input type"text" name="secret" value="<?php echo($org_secret); ?>">
<input type="submit" value="Upload file">
<a href="<?php echo($lms); ?>">Back to LMS View</a>
</fieldset>
</form>
<?php
  if ( ! $file_name ) exit();
?>
<p><b>Processing Uploaded File...</b></p>
<pre>
<?php

  require_once("ims-blti/blti_util.php");

   $manifest = zip_open_and_read_entry($file_name, "imsmanifest.xml");
   if ( ! $manifest ) {
     echo("Unable to find imsmanifest.xml in zip file\n");
     exit();
   }
   echo("Parsing manifest...\n");

   // Parse the XML
  $xml = new SimpleXMLElement($manifest);
  if ( ! $xml ) {
     echo("Error parsing manifest XML\n");
     exit();
  }
  echo("Reading resources from manifest...\n");

  $resources = array();
  foreach ( $xml->resources[0]->resource as $resource) {
    $identifier = (string) $resource['identifier'];
    $type = (string) $resource['type'];
    $file = $resource->file;
    $href = (string) $file['href'];
    $resources[$identifier] = array('type' => $type, 'href' => $href);
  }
  echo("Loaded ".sizeof($resources)." resources...\n");
  echo("</pre>");

  $top_item = $xml->organizations[0]->organization[0]->item;
  $divs = "";
  
  function display_item($item, $level) {
    global $resources;
    global $file_name;
    global $divs;
    global $org_id;
    global $org_secret;
    $newlevel = $level+1;
    $ref = (string) $item['identifierref'];
    if ( $item->title[0] ) {
       for($i=0; $i < $level; $i++) echo ("&nbsp;");
       $type = $resources[$ref]['type'];
       $href = $resources[$ref]['href'];
       if ( $ref ) echo("<a href=\"javascript:reveal('$ref');\">");
       echo($item->title[0]."\n");
       if ( $ref ) echo("</a>\n");
       if ( $ref && $type && $href ) {
         $content = zip_open_and_read_entry($file_name, $href);
         $disp = "none";
         if ( $divs == "" ) $disp = "inline";
         $div = "<div id=\"$ref\" style=\"display:$disp\">";
         if ( $type == "webcontent" ) {
           // pass
         } else if ( $type == "imswl_xmlv1p0" ) {
           $xml = new SimpleXMLElement($content);
           $content = "<pre>\n" . htmlspecialchars($content). "</pre>";
           if ( $xml ) {
             $url = $xml->url[0];
             $href = (string) $url["href"];
             if ( $href ) {
               $content = "<iframe src=\"$href\" width=\"100%\" height=\"1200\"></iframe>\n";
             }
           }
         } else if ( $type == "imsbasiclti_xmlv1p0" ) {
           $cx = launchInfo($content);
           $endpoint = $cx["launch_url"];
           $custom = $cx["custom"];
           $parms = array_merge($custom, getLMSDummyData());
           $parms = signParameters($parms, $endpoint, "POST", false, false, $org_secret, $org_id, "Org Desc");

	   $content = postLaunchHTML($parms, $endpoint, true);
         } else {
            $content = "<pre>\n" . htmlspecialchars($content). "</pre>";
         }
         $div = $div . $content . "\n</div>\n";
         $divs = $divs . $div;
       }
       echo("<br/>");
       $newlevel = $level + 1;
    }
    foreach ($item->item as $sub_item) {
      display_item($sub_item, $newlevel);
   }

 }
?>
</div>
<script language="javascript">
function reveal(divname) {
<?php
   foreach($resources as $ref => $resource ) {
     echo ( "document.getElementById('$ref').style.display = 'none';\n");
   }
?>
  if ( document.getElementById(divname) ) 
    document.getElementById(divname).style.display = 'inline';
}
</script>
<p><b>Common Cartridge Navigator</b> <a href="javascript:hideshow(document.getElementById('control'))">(Toggle Loader)</a><p>
<script language="javascript">
document.getElementById('control').style.display = "none";
</script>
<div id="master">
<div id="nav" style="float:left; width: 400px; background-color:#828282;">
<?php
  display_item($top_item, 0);
?>
</div>
<div id="content" style="margin-left: 410px; background-color:#1E90FF;">
<?php echo($divs); ?>
</div>
</div>
<?php
  print_r($top_items);



?>
</pre>
