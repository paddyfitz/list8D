<?php 
// Load up the Basic LTI Support code
require_once realpath(dirname(__FILE__)) . '/../ims/ims-blti/blti.php';
require_once(realpath(dirname(__FILE__)) . "/list8D.class.php"); 
require_once(realpath(dirname(__FILE__)) . "/list8D_template.class.php"); 

// Initialize, all secrets are 'secret', do not set session, and do not redirect
$context = new BLTI("secret", false, false);
?>

<html>
<head>
  <title>list8D LTI gateway</title>
  <link rel="stylesheet" href="http://readinglists.pilot.kent.ac.uk/lti/css/list8D.css" type="text/css" />
</head>
<body style="font-family:sans-serif">
<?php

if ( $context->valid ) {
	
	$list8D = new list8D("readinglists.pilot.kent.ac.uk");

	$limit = 0;
	
	if (empty($_GET['page']) || !is_numeric($_GET['page'])) { 
		$offset = 0;	
		$currentPage = 1;
	} else {
		$offset = $limit * ($_GET['page']-1);
		$currentPage = $_GET['page'];
	}
	
	$course = $context->getCourseName();

	if ($course=="SI182") {
		$course = "CL304";
		echo "<small>Defaulting to CL304</small>";
	}
	if ($course=="") {
		$course = "CL304";
		echo "<small>Defaulting to CL304</small>";
	}
	
	$tabs = array(
		'PO505'=>'Public Administration in Britain',
		'EC304'=>'Economics - Mode A',
		'SE550'=>'The Anthropology of Gender',
		'HI360'=>'Making history',
	);
	
	$list8D->setLimit($limit);
	
	if (!empty($_GET['compressed']) && $_GET['compressed'] != 0) { 
		$list8D->setTemplate('compressed');
		$list8D->setLimit(false);
	} else {
		$list8D->setTemplate('full');
	}

	$list8D->setPage($currentPage);
	$list8D->setUnique('code',$course);
	
	if (!$list8D->getList($list8D->getTemplateVariables())) {
			throw new Exception("404 list not found");
	}
	
	try {
		$list = $list8D->render();	
	} catch (Exception $e) {
		throw new Exception("There was a problem getting the reading list: ".$e->getMessage());
	}
	
	echo $list;

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



