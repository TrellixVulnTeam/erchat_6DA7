<?php
$db['db_host'] = "localhost";
$db['db_user'] = "root";
$db['db_pass'] = "";
$db['db_name'] = "erchat";

foreach ($db as $key => $value) {
    define(strtoupper(($key)), $value);
}

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$query = "SET NAMES utf8";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Connection failed: " . mysqli_connect_error());
}
