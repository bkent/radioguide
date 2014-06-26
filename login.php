<?php
session_start();
// dBase file
include "../db/functions.php";

	$is_ajax = $_REQUEST['is_ajax'];
	if(isset($is_ajax) && $is_ajax)
	{

 if (!$_POST['user_name'] || !$_POST['password'])
 	{
 	die("You need to provide a username and password.<p>Click <a href='index.php'>here</a> to return to the main login screen.</p>");
 	}
 
 $user_name=$_POST['user_name'];
 $password=$_POST['password'];
 
$mysqli = iConnect("radioshows");
 
 // To protect MySQL injection (more detail about MySQL injection)
$user_name = stripslashes($user_name);
$password = stripslashes($password);
$user_name = mysql_real_escape_string($user_name);
$password = mysql_real_escape_string($password);
 
$data = $mysqli->query("select * from users where user_name='$user_name' and password='$password' limit 1");
$row = $data->fetch_array();

$dbuser_name=$row['user_name'];
$dbpassword=$row['password'];
$short_name=$row['short_name'];
$user_id=$row['id'];
 
 if ( $user_name == $dbuser_name && $password == $dbpassword )
 	{
 	// Login good, create session variables
 	//$_SESSION["valid_id"] = $obj->id;
 	$_SESSION["valid_user"] = $_POST["user_name"];
 	$_SESSION["short_name"] = $short_name;
	$_SESSION["user_id"] = $user_id;
	// new session variable for the database name 
	$_SESSION["db_name"] = "radio_4_extra";
	
 	// Redirect to home page
	echo "success";
 	//Header("Location: home.php");
	//echo "<meta http-equiv='REFRESH' content='0;url=home.php'";
	//echo "Login Sucess" . $_SESSION["valid_user"];
 	}

}
 

?>