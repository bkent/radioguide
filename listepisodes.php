<?php
function ListEpisodesOfParent($pid,$showepisodes) 
{
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

	$resultstring;
	$commaseparatedepisodes;

	if (!$singleepisode)
	{ 
                $i=0;
		foreach($data['episodes'] as $episodes) {
			$title = str_replace(' ', '_',$episodes['programme']['title']);
			$position = $episodes['programme']['position'];
			$series = str_replace(' ', '_',$episodes['programme']['programme']['title']);
			$total = str_replace(' ', '_',$episodes['programme']['programme']['expected_child_count']);
			$brand = str_replace(' ', '_',$episodes['programme']['programme']['programme']['title']);
			$pid = $episodes['programme']['pid'];
			if ($brand != "")
				$resultstring .= $brand . "_-_" . $series . "_-_" . $position . "_of_" . $total . "_" . $title . " pid: ";
			else
				$resultstring .= $series . "_-_" . $position . "_of_" . $total . "_" . $title . " pid: ";
			$resultstring .= $pid . "<br />";
			if ($i==0)
				$commaseparatedepisodes .= $pid;
			else
				$commaseparatedepisodes .= "," . $pid;
			$i++;
		}
	}
	else
	{
		foreach($data['programme'] as $programmes) {
			$title = str_replace(' ', '_',$programmes['title']);
			if (strlen($title) > 1)
				$resultstring .= "Single episode! (at least so far..) " . $pid .  "<br />" . $title;
		}
	}
	
        $resultstring .= "<br/>" . $commaseparatedepisodes;
	
	if ($showepisodes)
	//return $resultstring;
	return $commaseparatedepisodes . ",";

}
?>