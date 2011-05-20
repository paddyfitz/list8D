<h1> Amazon Lookup form</h1>
<?php
	
	echo $this->form;
	
	//echo "<pre>";
	//echo htmlspecialchars($this->kentvoyager[0], ENT_QUOTES, "UTF-8");	
	//echo "</pre>";
	
	$count = 1;
	
	if(!empty($this->amazon)) {
		echo '<h2>Results</h2>';
	
		foreach ($this->amazon as $r) {
			print "<h3>[$count] " . $r['title'] . "</h3>";
			if($r['thumbnailUrl']) {
				print "<a href='{$r['url']}'><img src='" . $r['thumbnailUrl'] . "' /></a><br>";
			}
			print "<ul>\n";
			print "  <li>Title: " . $r['title'] . "</li>";
			print "  <li>Author: " . $r['authors'] . "</li>";
			print "  <li>Edition: " . $r['edition'] . "</li>";
			print "  <li>Date: " . $r['publication_date'] . "</li>";
			print "  <li>ASIN: " . $r['primaryKey'] . "</li>";
			print "  <li>Classhint: " . $r['classHint'] . "</li>";
			print "  <li>Description: " . $r['description']. "</li>";
			print "  <li>Link: <a href='{$r['url']}'>link</a>". "</li>";
			print "</ul>";
			$count++;
		}
	}
	?>
