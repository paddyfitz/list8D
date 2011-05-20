<p>If you're using the sample data, try starting with a search for 'public'</p>

<?php echo $this->action('searchWidget','list'); ?>

<h2>Testing stuff</h2>
<p>This stuff will be messy...</p>
<?php
foreach(array("kentvoyager", "url", "amazon", "googlebooks", "tag", "log", "user", "acl" ) as $i ) {

echo "<a href='$i'>$i</a><br>";
}
