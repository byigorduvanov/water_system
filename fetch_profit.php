<?php
include_once 'vendor/connect.php';
global $conn;

function fetch_profit($conn, $user_id, $year)
{
	$sql = "SELECT months.price_for_month, months.payment_status, meters.date
			FROM meters
			INNER JOIN months ON meters.id = months.id_meters
			WHERE meters.id_user = ? AND YEAR(meters.date) = ?";

	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ii", $user_id, $year);
	$stmt->execute();

	$result = $stmt->get_result();
	$data = array();

	while ($row = $result->fetch_assoc()) {
		$data[] = $row;
	}

	return $data;
}



if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
$id = $_SESSION["id"];

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$data = fetch_profit($conn, $id, $year);
echo json_encode($data);