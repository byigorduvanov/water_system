<?php
require_once '../vendor/connect.php';

if (!isset($_SESSION["admin_name"])) {
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
	<title>admin</title>
</head>

<body>
	<h1>сторінка адміна</h1>
	<a href="../index.php">головна</a>
	<a href="../vendor/logout.php" class="btn btn-primary my_btn btn_room">Вихід </a>

</body>

</html>