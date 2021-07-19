<?php
session_start();
require_once("logout.html");

$_SESSION = array();
session_destroy();
session_unset();

?>