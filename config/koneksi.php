<?php
//require_once "parser-php-version.php";
// koneksi ke mysql




$mysqli = new mysqli("localhost", "root", "root", "newmonita");

// Check connection
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli->connect_error;
  exit();
}
