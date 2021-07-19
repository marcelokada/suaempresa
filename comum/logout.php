<?php
session_start();
require_once("logout.html");

$_SESSION = array();
unset($_SESSION['idUsuario']);
unset($_SESSION['aut']);

?>