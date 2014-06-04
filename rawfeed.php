<html>
<head>
<meta name="HandheldFriendly" content="true" />
<meta name="MobileOptimized" content="320" />
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scaleable=no, width=device-width" />
<link rel="icon" href="http://benkent.servehttp.com/radioguide/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="http://benkent.servehttp.com/radioguide/favicon.ico" type="image/x-icon" />
</head>
<?php
 $url = "http://www.bbc.co.uk/radio4extra/programmes/genres/drama/current.json";
 
 $json = file_get_contents($url);
 
 $data = json_decode($json,true);
 
//echo "<pre>";
//print_r($data);
//exit;
 
echo "<table border='1'><tr><th>Title</th><th>Synopsis</th><th>pid</th></tr>";

$i = 0;

foreach($data as $category_slice) {	
    foreach($category_slice['programmes'] as $program) {
        //echo "Title: " . $program['title'] . "<br />";
		$i++;
		$title = $program['title'];
		$short_synopsis = $program['short_synopsis'];
		$pid = $program['pid'];
		echo "<tr><td>$title</td><td>$short_synopsis</td><td>
		<form name='form$i' action='episodeestimates.php' method='post'>
		<input type='hidden' name='pid' value='$pid' />
		<input type='submit' name='button$i' value='Episode count' />
		</form></td></tr>";
	}
}

/*
echo "<table><tr><th>Title</th><th>Synopsis</th></tr>";
$programmes = $data->category_slice[2]->programmes;
foreach($programmes as $program){
   $title = $program->program->title;
   $short_synopsis = $program->short_synopsis;
   //do something with it
   echo "<tr><td>$title</td><td>$short_synopsis</td></tr>";
} 
//
echo "</table>";
*/


?>
</html>