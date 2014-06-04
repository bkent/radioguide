<html>
<head>
<meta name="HandheldFriendly" content="true" />
<meta name="MobileOptimized" content="320" />
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scaleable=no, width=device-width" />
</head>
<?php

 $pid = @$_POST["pid"];

 $url = "http://www.bbc.co.uk/programmes/$pid.json";
 
 $json = file_get_contents($url);
 
 $data = json_decode($json,true);
 
//echo "<pre>";
//print_r($data);
//exit;
 
//foreach($data as $category_slice) {	
    foreach($data as $programme) {
        //echo "Title: " . $program['title'] . "<br />";
		$expected_child_count = $programme['expected_child_count'];
		echo "Estimated episodes: $expected_child_count";
	}
//}

?>
</html>