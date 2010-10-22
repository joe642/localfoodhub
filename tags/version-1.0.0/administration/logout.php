<?php

session_start();

$Secure = 1;

include "../config.php";

$_SESSION['admin'] = '';
$_SESSION['admin_date'] = '';


header("Location:$BaseURL/home.php");

exit();

?>
