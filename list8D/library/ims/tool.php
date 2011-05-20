<?php 
// Load up the Basic LTI Support code
require_once 'ims-blti/blti.php';

// Initialize, all secrets are 'secret', do not set session, and do not redirect
$context = new BLTI("secret", false, false);
?>
<html>
<head>
  <title>list8D LTI gateway</title>
</head>
<body style="font-family:sans-serif">
<?php

if ( $context->valid ) {
	$ch = curl_init();

	$course = $context->getCourseName();

	if ($course=="SI182") {
		$course = "CL304";
		echo "<small>Defaulting to CL304</small>";
	}
	if ($course=="") {
		$course = "CL304";
		echo "<small>Defaulting to CL304</small>";
	}
	
	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, "http://rosie.dev.list8d.org/api?method=getListByCode&code=$course");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// grab URL and pass it to the browser
	$output = curl_exec($ch);
	$listid = json_decode($output);

	$l_id = $listid->response[0];

	curl_setopt($ch, CURLOPT_URL, "http://rosie.dev.list8d.org/api?method=getListById&listid=" . $l_id);
	$output = curl_exec($ch);
	$list = json_decode($output);


	echo "<h1>" . $list->response->title . "</h1>";
	foreach ((array) $list->response->children as $child) {
		$t = $child->resource->title;
		$a = $child->resource->authors;
		$u = $child->resource->url;
		if ($t != "") {
			if ($u != "") {
			print "<li><a href='$u'>$t</a> <em>$a</em></li>";
			} else {
			print "<li>$t <em>$a</em></li>";
			}
		}
	}

	// close cURL resource, and free up system resources
	curl_close($ch);

    print "<pre>\n";
    print "Context Information:\n\n";
    print $context->dump();
    print "</pre>\n";
} else {
    print "<p style=\"color:red\">Could not establish context: ".$context->message."<p>\n";
}
print "<pre>\n";
print "Raw POST Parameters:\n\n";
foreach($_POST as $key => $value ) {
    print "$key=$value\n";
}
print "</pre>";

?>
