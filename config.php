<?php
$dbhost = "yourhostname";
$dbuser = "yourusername";
$dbpass = "yourdatabasepassword";
$dbname = "yourdatabasename";
if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{

	die("Failed to connect!");
}
?>