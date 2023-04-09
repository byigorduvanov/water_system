<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
$conn = mysqli_connect("localhost", "root", "", "water_system_db");
mysqli_set_charset($conn, 'utf8'); 
if (!$conn) {
	die('Error connect to DataBase');
}