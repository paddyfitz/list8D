<h1> URL Lookup form</h1>
<?php
	
	echo $this->form;
	
	$count = 1;
	
	if(!empty($this->url)) {
		echo '<h2>Results</h2>';
		//pre_dump($this->url);
		//foreach ($this->url as $r) {
		$r = $this->url;
			print "<h3>[$count] " . $r['title'] . "</h3>";
			if($r['thumbnailUrl']) {
				print "<a href='{$r['url']}'><img src='" . $r['thumbnailUrl'] . "' /></a><br>";
			}
			print "<ul>\n";
			print "  <li>Title: " . $r['title'] . "</li>";
			// print "  <li>Author: " . $r['authors'] . "</li>";
			// print "  <li>Edition: " . $r['edition'] . "</li>";
			// print "  <li>Date: " . $r['published'] . "</li>";
			// print "  <li>ASIN: " . $r['primaryKey'] . "</li>";
			print "  <li>Description: " . $r['description']. "</li>";
			print "  <li>Link: <a href='{$r['url']}'>link</a>". "</li>";
			print "</ul>";
			$count++;
		//}
	}
	?>

<a href="http://www.thumbshots.com" target="_blank" title="Thumbnails Previews by Thumbshots">URL Thumbnails powered by Thumbshots</a>
