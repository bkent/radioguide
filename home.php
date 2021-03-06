<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" href="http://benkent.servehttp.com/radioguide/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="http://benkent.servehttp.com/radioguide/favicon.ico" type="image/x-icon" />
<meta name="HandheldFriendly" content="true" />
<meta name="MobileOptimized" content="320" />
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scaleable=no, width=device-width" />
<title>Radioguide</title>
<link rel="stylesheet" href="radioguide.css">
</head>
<?php
include "../db/functions.php";
include "listepisodes.php";
//Check if user is legitimately logged in
session_start();

if (!isset($_SESSION["valid_user"]))
{
	// User not logged in, redirect to login page
	header("Location: index.php");
	exit();
}

if (!isset($_SESSION["db_name"]))
    $dbname = "radio_4_extra"; // just in case
else
    $dbname = $_SESSION["db_name"];

$datetoday = date('d/m/Y');

$q = @$_POST["q"];
$pagenum = @$_POST["p"];

if (!isset($_POST["p"]))
{
	$pagenum  = 1;
}

echo "<body OnLoad=";?>"<? echo "document.form.q.focus()";?>"<? echo "><div align='left'>
	  <div class='searchbar'>Logged in as <a href='userstats.php'>". $_SESSION["short_name"] ."</a>&nbsp;(". $dbname .")</div>
      <div class='searchbar'><form name='form' id='form' method='post' action='home.php'><input type='text' name='q' id='q' value='$q' size='30' />
      <input type='submit' name='Search' value='Search' /></form></div>
	  <div class='searchbar'><form method='post' action='home.php' name='formreset'>
	  <input type='submit' name='reset' value='Reset Search' /></form></div>
	  <div class='searchbar'><form method='post' action='updateshow.php' name='changestation'>
	  <input type='submit' name='station' value='Change Station' />
	  <input type='hidden' name='action' value='station'/></form></div>
	  <div class='searchbar'><form name='formlogout' method='post' action='logout.php'>
      <input type='submit' name='logout' value='Log Out' /></form></div></div>
      <div class='maintabdiv'>
	  ";

$mysqli = iConnect("radioshows");

$qdata = "SELECT id, title, short_synopsis, current, detecteddt, download_status, currentepisode, episodes, episode_lookup, frequency, series_pid
  FROM ". $dbname ."
  WHERE (title LIKE '%$q%'
  OR short_synopsis LIKE '%$q%')";
  
  if ($q == "") // nothing is being searched
  {
	$qdata .= " AND (download_status <> 'I' AND download_status <> 'F' OR download_status IS NULL) AND current='Y'";
  }

// make sure newly-detected shows go to the top.
$qdata .= " ORDER BY download_status, detecteddt";

$data = $mysqli->query($qdata); 

//echo "$qdata"; 
  
$num_rows = $data->num_rows;

//This is the number of results displayed per page
$page_rows = 50;

//This tells us the page number of our last page 
$last = ceil($num_rows/$page_rows);

//this makes sure the page number isn't below one, or more than our maximum pages 
if ($pagenum < 1) 
{ 
	$pagenum = 1; 
} 
elseif ($pagenum > $last) 
{ 
	$pagenum = $last; 
}   

//This sets the range to display in our query 
$max = " LIMIT " .($pagenum - 1) * $page_rows ."," .$page_rows;

$qdata .= $max;

// run the query again with the limit clause
$data = $mysqli->query($qdata); 

if ($num_rows > 0)
{
    $i = 1;
	while ($row = $data->fetch_array())
	{
	    if ($i % 2 == 0) {  // if i is an even number
            $trclass = "class='tabdataeven";
        }
		else {
		    $trclass="class='tabdataodd";
		}
		$id = $row['id'];
		$title = $row['title'];
		$short_synopsis = $row['short_synopsis'];
		$current = $row['current'];
		$detecteddt = $row['detecteddt'];
		$download_status = $row['download_status'];
		$series_pid = $row['series_pid']; 
		if ($download_status == "A")
		  $status = "Active";
		else
		  $status = "";
   	    $currentepisode = $row['currentepisode'];
		$episodes = $row['episodes'];
		$episode_lookup = $row['episode_lookup'];
		$frequency = $row['frequency'];
		
		$detecteddt = date('d/m/Y',strtotime($detecteddt));
	    		
		echo "<div class='tabrow'>";
			
		//echo "<div $trclass-wide'>$title &nbsp;&nbsp;&nbsp;&nbsp;<form name='form$i' action='episodeestimates.php' method='post'>
		echo "<div $trclass-wide'>$title &nbsp;&nbsp;&nbsp;&nbsp;<form name='form$i' action='http://www.bbc.co.uk/programmes/$series_pid'  method='post' target='_blank'>
		<input type='hidden' name='pid' value='$series_pid' />
		<input type='submit' name='button$i' value='$series_pid' />";
		$episodelistthml .= ListEpisodesOfParent($series_pid);
		//echo "</form>$episodelistthml</div>";
		echo "</form></div>";
		echo "<div $trclass-wide'>$short_synopsis</div>";
		echo "<div $trclass-narrow'><div class='formdiv'>Current: $current | Detected: $detecteddt |&nbsp;</div>";

		if ($frequency == "")
		{
			echo "<div class='formdiv'>
				<form method='post' action='updateshow.php' name='formd$i'>
				<input type='hidden' name='id' value='$id'/>
				<input type='hidden' name='action' value='freq'/>
				<input type='hidden' name='thedata' value='Daily'/>
				<input type='submit' name='day' value='Daily' />
				</form></div>";
			echo "<div class='formdiv'>
				<form method='post' action='updateshow.php' name='forms$i'>
				<input type='hidden' name='id' value='$id'/>
				<input type='hidden' name='action' value='freq'/>
				<input type='hidden' name='thedata' value='Saturdays'/>
				<input type='submit' name='sat' value='Saturdays' />
				</form></div>";		
		}
		else
		{
			echo "$frequency";
		}
		
		echo "</div>";
		echo "<div $trclass-narrow'><div class='formdiv'>Status: $status | Episode: $currentepisode </div>
				<div class='formdiv'>
				<form method='post' action='updateshow.php' name='forme$i'>
				<input type='hidden' name='id' value='$id'/>
				<input type='hidden' name='action' value='inc'/>
				<input type='hidden' name='thedata' value='$currentepisode'/>
				<input type='submit' name='add' value='+' />
				</form></div>
				<div class='formdiv'> of $episodes </div>
				 <div class='formdiv'>";
		if ($episode_lookup != "Y" && $episode_lookup != "M" && $download_status == "A")
		{
			echo "<form method='post' action='updateshow.php' name='formt$i'>
					<input type='hidden' name='id' value='$id'/>
					<input type='hidden' name='action' value='settot'/>
					<input type='text' name='thedata' value='1' size='2' />
					<input type='submit' name='add' value='Set total' />
					</form></div>
					";
			if ($episode_lookup == "N")
			{
				if ($episode_lookup != "M") // M denotes manually looked up
				{
					echo " ";
				}
				else
					echo "</div>";
			}
			else
			{
				echo " ";
			}
		}
		else
			echo "</div>";
		if ($download_status == "")  //new title
		{
			echo "<div class='formdiv'>
			<form method='post' action='updateshow.php' name='formi$i'>
			<input type='hidden' name='id' value='$id'/>
			<input type='hidden' name='action' value='I'/>
			<input type='submit' name='ignore' value='Ignore' />
			</form>
			<form method='post' action='updateshow.php' name='forma$i'>
			<input type='hidden' name='id' value='$id'/>
			<input type='hidden' name='action' value='A'/>
			<input type='hidden' name='thedata' value='$title'/>
			<input type='submit' name='active' value='Active' />
			</form>
			</div>";
		}
		if ($download_status == "A") 
		{
			echo "<div class='formdiv'><form method='post' action='updateshow.php' name='formi$i'>
			<input type='hidden' name='id' value='$id'/>
			<input type='hidden' name='action' value='F'/>
			<input type='submit' name='finish' value='Finished' />
			</form></div>";
		}
		
		echo "</div>";
		echo "</div>";
		
		$i++;
	}
	
	if ($num_rows==1)
		$results = "title";
	else
		$results = "titles";
	
	echo "</div><div class='maintabdiv'>";
	
	echo "<div align='center'>";

// First we check if we are on page one. If we are then we don't need a link to the previous page or the first page so we do nothing. If we aren't then we generate links to the first page, and to the previous page.
if ($pagenum == 1) 
{
	$firstbuttonenabled = "disabled='disabled'";
}
else
{
	$firstbuttonenabled = "";
}

echo " <div class='searchbar'><form name='formpagefirst' id='formpagefirst' method='post' action='home.php'>
<input type='submit' name='first' value='<<' $firstbuttonenabled />
<input type='hidden' name='q' value='$q' />
<input type='hidden' name='o' value='$ordby' />
<input type='hidden' name='p' value='1' /></form></div>";

$previous = $pagenum-1;

echo " <div class='searchbar'><form name='formpageprevious' id='formpageprevious' method='post' action='home.php'>
<input type='submit' name='previous' value='<' $firstbuttonenabled />
<input type='hidden' name='q' value='$q' />
<input type='hidden' name='o' value='$ordby' />
<input type='hidden' name='p' value='$previous' /></form></div>";

//This does the same as above, only checking if we are on the last page, and then generating the Next and Last links
if ($pagenum == $last) 
{
	$lastbuttonenabled = "disabled='disabled'";
}
else
{
	$lastbuttonenabled = "";
}

$next = $pagenum+1;

echo " <div class='searchbar'><form name='formpagenext' id='formpagenext' method='post' action='home.php'>
<input type='submit' name='next' value='>' $lastbuttonenabled />
<input type='hidden' name='q' value='$q' />
<input type='hidden' name='o' value='$ordby' />
<input type='hidden' name='p' value='$next' /></form></div>";

echo " <div class='searchbar'><form name='formpagelast' id='formpagelast' method='post' action='home.php'>
<input type='submit' name='last' value='>>' $lastbuttonenabled />
<input type='hidden' name='q' value='$q' />
<input type='hidden' name='o' value='$ordby' />
<input type='hidden' name='p' value='$last' /></form></div>";

echo "<div class='searchbar'>Page $pagenum of $last &nbsp;</div>";
	
	echo "<div class='searchbar'>| $num_rows $results
	</div></div>
	</div><div width='100%'>$episodelistthml</div></body></html>"; 
}

?>