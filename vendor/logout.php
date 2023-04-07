<?php
include 'connect.php';
$_SESSION = [];
session_destroy();
header("Location: ../autorization.php");