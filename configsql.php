<?php


if ($_SERVER['SERVER_NAME'] == "lansard.ch") {
    include "../../configuserlogin.php";
}
if ($_SERVER['SERVER_NAME'] == "albinton.fr") {
    include "configuserlogin.php";
}
define('DB_SERVER', bdserver);
define('DB_USERNAME', bduser);
define('DB_PASSWORD', bdpassword);

// $dbconnect = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
define('dbconnect', mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME));
if (dbconnect === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
