<?php

if ($_SERVER['SERVER_NAME'] == "lansard.ch") {
    define('DB_NAME', 'albinton');
}
if ($_SERVER['SERVER_NAME'] == "compta.albinton.fr") {
    define('DB_NAME', 'albin549889');
}

include "configsql.php";
