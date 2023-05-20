<?php
require_once '../vendor/connect.php';

$id = $_SESSION["id"];
global $id;
if (!isset($_SESSION["operator_name"])) {
	header("Location: ../autorization.php");
	exit();
} else {
	$id = $_SESSION["id"];
	$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>operator</title>
</head>

<body>
	<h1>сторінка оператора</h1>
	<a href="../vendor/logout.php" class="btn btn-primary my_btn btn_room">Вихід </a>

</body>

</html>