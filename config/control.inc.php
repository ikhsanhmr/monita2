<?php
$srv = "localhost";
$usr = "root";
$pwd = "";
$db = "newmonita";
$mysqli = new mysqli("localhost", "root", "", "newmonita");

// Check connection
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli->connect_error;
  exit();
}
