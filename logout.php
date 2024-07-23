<?php

// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

setcookie('ALB_CONNECT_USERNAME', '', time() - 365 * 24 * 3600, '/', '.albinton.fr', true, true);

// Redirect to login page
header('location: https://compta.albinton.fr/login.php');
exit;
