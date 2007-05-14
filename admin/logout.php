<?php
/* * Created on Feb 5, 2006 */
session_start();
$_SESSION['loginValidated'] = 0;
session_destroy();
header("Location: "."../");
exit;
?>
