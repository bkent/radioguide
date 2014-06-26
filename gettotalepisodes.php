<?php
function gettotalepisodes($inputtitle)
{
	if ($_SESSION["db_name"] = "radio_4")
		$url = "http://www.bbc.co.uk/radio4/programmes/genres/drama/current.json";
	else
	    $url = "http://www.bbc.co.uk/radio4extra/programmes/genres/drama/current.json";
		
	$json = file_get_contents($url);
	$data = json_decode($json,true);

	foreach($data as $category_slice) {	
		foreach($category_slice['programmes'] as $program) {
			$title = $program['title'];
			$pid = $program['pid'];
			
			if ($title == $inputtitle)
			{
				$outcome = gettotalepisodesfrompid($pid);
				if ($outcome > 0)
					return $outcome;
				else
					return -1;
					// here (within this else) get the child titles by looking up the
					// json from the schedule: http://www.bbc.co.uk/radio4extra/programmes/schedules/next_week.json
					// (this_week is also available - just in case the search of next week fails).
				
				//break;
				
				//return $pid;
			}
			
		}
	}
	return -1;
}

function gettotalepisodesfrompid($pid1)
{
	$url1 = "http://www.bbc.co.uk/programmes/$pid1.json";
	$json1 = file_get_contents($url1);
	$data1 = json_decode($json1,true);
		foreach($data1 as $programme) {
			$expected_child_count = $programme['expected_child_count'];
			return $expected_child_count;
		}
}
?>