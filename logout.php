<?php
/* File: logout.php
   Author: Justin Schwertmann */

session_start();
//Make sure the user intended to logout, not just visit the page url
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    session_destroy();
    header("Location: ./index.php");
    die;
}
else
{
    header("Location: ./index.php");
    // Whether the user is logged in or not, index.php will redirect to login or homepage
}