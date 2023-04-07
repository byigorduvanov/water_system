<?php
require 'vendor/connect.php';

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$year = isset($_GET['year']) ? $_GET['year'] : '';
$limit = 6;
$offset = $limit * ($page - 1);

function page_select_number($limit, $page, $year)
{
	global $conn;

	$id = $_SESSION["id"];
	$offset = $limit * ($page - 1);
	$yearCondition = $year ? "AND YEAR(meters.date) = $year" : "";
	$sql = "SELECT meters.*, users.counter FROM `meters` 
            INNER JOIN users ON meters.id_user = users.id
            WHERE meters.id_user = $id $yearCondition
            LIMIT $limit OFFSET $offset";

	$result = mysqli_query($conn, $sql);
	$meters_table = mysqli_fetch_all($result, MYSQLI_ASSOC);
	return $meters_table;
}


$page_select_number = page_select_number($limit, $page, $year);
echo json_encode($page_select_number);