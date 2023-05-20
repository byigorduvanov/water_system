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
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
	<link rel="stylesheet" href="../css/all.css">
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/main.css">
</head>

<body>
	<h1>сторінка адміна</h1>
	<a href="../index.php">головна</a>
	<a href="../vendor/logout.php" class="btn btn-primary my_btn btn_room">Вихід </a>




	<footer>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/bootstrap.bundle.min.js"></script>
	</footer>
</body>

</html>