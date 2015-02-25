<html>
<head>
<meta name="HandheldFriendly" content="true" />
<meta name="MobileOptimized" content="320" />
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scaleable=no, width=device-width" />
</head>
<body>
<?php

$pid = @$_POST["pid"];
 
//echo "<form method='post' action='http://www.bbc.co.uk/programmes/$pid' id='autosubmit' name='autosubmit' target='_blank'>";
//echo "</form>";

// none of the below works any more - the bbc has changed the json interface...

// update 25/02/2015 - this view seems to be back...

 $url = "http://www.bbc.co.uk/programmes/$pid/episodes/player.json";
 
 $json = file_get_contents($url);
 
 $singleepisode = false;
 
 
 if ($json == "")
 {
	$url = "http://www.bbc.co.uk/programmes/$pid.json";
 	$json = file_get_contents($url);
	if ($json == "")
	{
		echo "Nothing found (yet)";
		exit;
	}
	$singleepisode = true;
 }
 
 $data = json_decode($json,true);
 
//echo "<pre>";
//print_r($data);
//exit;

if (!$singleepisode)
{ 
	foreach($data['episodes'] as $episodes) {
		$title = str_replace(' ', '_',$episodes['programme']['title']);
		$position = $episodes['programme']['position'];
		$series = str_replace(' ', '_',$episodes['programme']['programme']['title']);
		$total = str_replace(' ', '_',$episodes['programme']['programme']['expected_child_count']);
		$brand = str_replace(' ', '_',$episodes['programme']['programme']['programme']['title']);
		$pid = $episodes['programme']['pid'];
		if ($brand != "")
			echo $brand . "_-_" . $series . "_-_" . $position . "_of_" . $total . "_" . $title . " pid: ";
		else
			echo $series . "_-_" . $position . "_of_" . $total . "_" . $title . " pid: ";
		echo $pid . "<br />";
	}
}
else
{
	foreach($data['programme'] as $programmes) {
		$title = str_replace(' ', '_',$programmes['title']);
		if (strlen($title) > 1)
		    echo "Single episode! " . $pid .  "<br />" . $title;
	}
}
// paste this between body and html when uncommenting above code
//<script language=JavaScript>document.autosubmit.submit();</script>
?>
</body>

</html>