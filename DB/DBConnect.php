<?php

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "tle-1";

// Create connection
$db = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";

?>