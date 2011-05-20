<html>
<head>
  <title>IMS Basic Learning Tools Interoperability</title>
</head>
<body style="font-family:sans-serif">
<img src="http://www.php.net/images/logos/php-med-trans-light.gif" align="right"/>
<p><b>IMS BasicLTI PHP Sample Code</b></p>
<p>
This is sample code which to support the IMS BasicLTI and IMS Common Cartridge Specifications. 
There are examples of the following in this source tree:
<ul>
<li><a href="lms.php">How the Learning Management System authors and launches a BasicLTI resource.</a>
(i.e. a BasicLTI Consumer)
</li>
<li><a href="tool.php">An external tool which supports BasicLTI</a>
(i.e. a BasicLTI Producer) - Note that this tool will fail when launched with a GET request.  It requires
a POST request with propertly signed OAuth data.
<li><a href="adlist/index.htm">A fully function Basic LTI Tool</a>
which implements a simple classified ad application.  Complete with administration
interface and Database (requires MySql Setup).
<?php
   require_once("misc.php");

   $manifest = zip_open_and_read_entry("basiclti-sample-cartridge-01.zip", "imsmanifest.xml");
   if ( $manifest ) {
     echo('<li><a href="cc.php">How the Learning Management System launches a BasicLTI resource from within an IMS Common Cartridge</a></li>');
     echo("\n");
   } else {
     echo("<!-- Common Cartridge Support disabled because of missing zip_open() -->\n");
   }
?>
</li>
<li>You can <a href="dist.zip">download the source to this web site</a>
 so you can use the code for yourself.
</ul>
<p>
A simple IMS Common Cartridge with a BasicLTI descriptor
is <a href="basiclti-sample-cartridge-01.zip">provided here</a>.
Safari seems to auto-extract this file regardless of the file suffix. 
<hr>
<p>
Note: Unpublished drafts of IMS Specifications are only available to IMS members and any software based on
an unpublished draft is subject to change.
Sample code is provided to help developers understand the specification more quickly.
Simply interoperating with this sample implementation code does not
allow one to claim compliance with a specification.
<p>
<a href=http://www.imsglobal.org/toolsinteroperability2.cfm>IMS Learning Tools Interoperability Working Group</a> <br/>
<a href="http://www.imsglobal.org/ProductDirectory/directory.cfm">IMS Compliance Detail</a> <br/>
<a href="http://www.imsglobal.org/community/forum/index.cfm?forumid=11">IMS Developer Community</a> <br/>
<a href="http:///www.imsglobal.org/" class="footerlink">&copy; 2009 IMS Global Learning Consortium, Inc.</a> under the Apache 2 License.</p>

