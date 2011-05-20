<?php 


$listid = $_REQUEST['id'];

// create a new cURL resource
$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, "http://list8d.spod.cx/api?method=getListByCode&code=BI304");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// grab URL and pass it to the browser
$output = curl_exec($ch);
$listid = json_decode($output);
#var_dump($listid);

$l_id = $listid->response[0];

curl_setopt($ch, CURLOPT_URL, "http://list8d.spod.cx/api?method=getListById&listid=" . $l_id);
$output = curl_exec($ch);
$list = json_decode($output);

#var_dump($list);

echo "<h1>" . $list->response->title . "</h1>";
foreach ((array) $list->response->children as $child) {
	$t = $child->resource->title;
	$a = $child->resource->authors;
	$u = $child->resource->url;
	if ($t != "") {
		print "<li><a href='$u'>$t</a> <em>$a</em></li>";
	}
}

// close cURL resource, and free up system resources
curl_close($ch);
