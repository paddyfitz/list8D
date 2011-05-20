<html>
<head>
  <title>IMS Basic Learning Tools Interoperability</title>
</head>
<body style="font-family:sans-serif">
<img src="http://www.php.net/images/logos/php-med-trans-light.gif" align="right"/>
<p><b>IMS BasicLTI PHP Consumer</b></p>
<p>This is a very simple reference implementaton of the LMS side (i.e. consumer) for IMS BasicLTI.</p>
<?php

require_once("misc.php");
require_once("ims-blti/blti_util.php");

  $cur_url = curPageURL();
  $org_id = $_REQUEST["org_id"];
  if ( ! $org_id ) $org_id = "lmsng.school.edu";
  $org_desc = $_REQUEST["org_desc"];
  if ( ! $org_desc ) $org_desc = "University of School (LMSng)";
  $key = $_REQUEST["key"];
  if ( ! $key ) $key = "12345";
  $org_secret = $_REQUEST["org_secret"];
  $secret = $_REQUEST["secret"];
  if ( ! $secret && ! $org_secret ) $secret = "secret";

  $endpoint = $_REQUEST["endpoint"];
  if ( ! $endpoint ) $endpoint = str_replace("lms.php","tool.php",$cur_url);
  if ( ! $oauth_consumer_secret ) $oauth_consumer_secret = "secret";
  $urlformat = $_REQUEST["format"];
  $urlformat = ( $urlformat != 'XML' );
  $lmspw = $_REQUEST["lmspw"];
  $lmspw = ( $lmspw != 'Resource' );
  // To keep roundtrips from adding backslashes to double quotes
  $xmldesc = str_replace("\\\"","\"",$_REQUEST["xmldesc"]);
  if ( ! $xmldesc ) $xmldesc = $default_desc;

  echo("<form method=\"post\">\n");
  echo("<p><select name=\"format\" onchange=\"this. form.submit();\">\n");
  echo("<option value=\"URL\">URL plus Secret</option>\n");
  if ( $urlformat ) {
    echo("<option value=\"XML\">XML Descriptor</option>\n");
  } else {
    echo("<option value=\"XML\" selected=\"selected\">XML Descriptor</option>\n");
  }
  echo("</select>");
  echo("<fieldset><legend>Add New BasicLTI Resource</legend>\n");
  if ( $urlformat ) {
    echo("Launch URL: <input size=\"60\" type=\"text\" name=\"endpoint\" value=\"$endpoint\">\n");
  } else {
    echo("XML BasicLTI Resource Descriptor: <br/> <textarea name=\"xmldesc\" rows=\"10\" cols=\"80\">".htmlspecialchars($xmldesc)."</textarea>\n");
  }
  echo("<br/>Key: <input type\"text\" name=\"key\" value=\"$key\">\n");
  echo("<br/>Secret: <input type\"text\" name=\"secret\" value=\"$secret\">\n");
  echo("<br/><input type=\"submit\" value=\"Submit\">\n");
  echo("</fieldset><p>");
/*
  echo("<fieldset><legend>LMS Administrator Data</legend>\n");
  echo("LMS name: <input type\"text\" name=\"org_desc\" value=\"$org_desc\">\n");
  echo("<br/>LMS key: <input type\"text\" name=\"org_id\" value=\"$org_id\">\n");
  echo("<br/>LMS secret: <input type\"text\" name=\"org_secret\" value=\"$org_secret\">\n");
  echo("<br/>If both a resource secret and LMS secret are entered - the LMS secret is used.\n");
  echo("</fieldset>");
  echo("<p><input type=\"submit\" value=\"Submit\">\n");
*/
  echo("<p><a href=\"lms.php\">Default Values</a>\n");
  echo("<a href=\"cc.php\">Upload a Common Cartridge</a>\n");
  echo("</form>");
  echo ("<p>Note that if you are launching to tool.php, it \n");
  echo("accepts a \"12345/secret\" as a valid resource key/secret.\n");
  echo("and lmsng.school.edu/secret as a valid LMS key/secret.\n");
  echo("<hr>");

  if ( ! $lmspw ) unset($org_id);

  if ( $urlformat ) {
    $parms = getLMSDummyData();
  } else {
    $cx = launchInfo($xmldesc);
    $endpoint = $cx["launch_url"];
    if ( ! $endpoint ) {
      echo("<p>Error, did not find a launch_url or secure_launch_url in the XML descriptor</p>\n");
      exit();
    }
    $custom = $cx["custom"];
    $parms = array_merge($custom, getLMSDummyData());
  }

  // Add oauth_callback to be compliant with the 1.0A spec
  $parms["oauth_callback"] = "about:blank";

  $parms = signParameters($parms, $endpoint, "POST", $key, $secret, $org_secret, $org_id, $org_desc);

  $content = postLaunchHTML($parms, $endpoint, true);
  print($content);

?>
<hr>
<p>
Note: Unpublished drafts of IMS Specifications are only available to 
IMS members and any software based on an unpublished draft is subject to change.
Sample code is provided to help developers understand the specification more quickly.
Simply interoperating with this sample implementation code does not 
allow one to claim compliance with a specification.
<p>
<a href=http://www.imsglobal.org/toolsinteroperability2.cfm>IMS Learning Tools Interoperability Working Group</a> <br/>
<a href="http://www.imsglobal.org/ProductDirectory/directory.cfm">IMS Compliance Detail</a> <br/>
<a href="http://www.imsglobal.org/community/forum/index.cfm?forumid=11">IMS Developer Community</a> <br/>
<a href="http:///www.imsglobal.org/" class="footerlink">&copy; 2009 IMS Global Learning Consortium, Inc.</a> under the Apache 2 License.</p>
