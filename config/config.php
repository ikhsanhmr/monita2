<?php

define('DB_NAME', 'newmonita');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
// define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');

function db_connect()
{
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or
		die(mysql_error());
	mysql_select_db(DB_NAME, $link) or die(mysql_error());
	return $link;
}

function db_select($query)
{
	db_connect();
	$result = mysql_query($query);
	$res_array = array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$res_array[] = $row;
	}
	return $res_array;
}

function db_select_column($query)
{
	db_connect();
	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$res_array = $row[0];
	}
	return $res_array;
}

function db_insert($query)
{
	$a = db_connect();
	$result = mysql_query($query, $a);
	return mysql_error($a);
	//return mysql_affected_rows($a);
}

function db_check_exists($query)
{
	$a = db_connect();
	$result = mysql_query($query, $a);
	return mysql_num_rows($result);
}
