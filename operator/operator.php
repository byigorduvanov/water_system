<?php
require_once '../vendor/connect.php';
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

// Create new user
if (isset($_POST['create_user'])) {
	$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, middle_name, mobile_phone, home_phone, email, password, role, personal_account, address, counter) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("sssssssssss", $_POST['first_name'], $_POST['last_name'], $_POST['middle_name'], $_POST['mobile_phone'], $_POST['home_phone'], $_POST['email'], $_POST['password'], $_POST['role'], $_POST['personal_account'], $_POST['address'], $_POST['counter']);
	$stmt->execute();
}

// Search for user
$user_search_result = [];
if (isset($_POST['search_user'])) {
	$stmt = $conn->prepare("SELECT * FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR personal_account LIKE ?");
	$like_term = '%' . $_POST['search_term'] . '%';
	$stmt->bind_param("ssss", $like_term, $like_term, $like_term, $like_term);
	$stmt->execute();
	$user_search_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Update user
if (isset($_POST['update_user'])) {
	$stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, middle_name=?, mobile_phone=?, home_phone=?, email=?, password=?, role=?, personal_account=?, address=?, counter=? WHERE id=?");
	$stmt->bind_param("sssssssssssi", $_POST['first_name'], $_POST['last_name'], $_POST['middle_name'], $_POST['mobile_phone'], $_POST['home_phone'], $_POST['email'], $_POST['password'], $_POST['role'], $_POST['personal_account'], $_POST['address'], $_POST['counter'], $_POST['user_id']);
	$stmt->execute();
}

// Update meter
if (isset($_POST['update_meter'])) {
	$stmt = $conn->prepare("UPDATE meters SET meters=?, date_update=?, operator_update=? WHERE id=?");
	$stmt->bind_param("ssii", $_POST['meters'], $_POST['date_update'], $id, $_POST['meter_id']);
	$stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>operator</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
	<link rel="stylesheet" href="../css/all.css">
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/main.css">
</head>

<body>
	<h1>сторінка оператора</h1>
	<a href="../index.php">головна</a>

	<a href="../vendor/logout.php" class="btn btn-primary my_btn btn_room">Вихід </a>

	<!-- Create user form -->
	<form method="post">
		<h2>Create User</h2>
		<input type="text" name="first_name" placeholder="First Name">
		<input type="text" name="last_name" placeholder="Last Name">
		<input type="text" name="middle_name" placeholder="Middle Name">
		<input type="text" name="mobile_phone" placeholder="Mobile Phone">
		<input type="text" name="home_phone" placeholder="Home Phone">
		<input type="text" name="email" placeholder="Email">
		<input type="text" name="password" placeholder="Password">
		<input type="text" name="role" placeholder="Role">
		<input type="text" name="personal_account" placeholder="Personal Account">
		<input type="text" name="address" placeholder="Address">
		<input type="text" name="counter" placeholder="Counter">
		<input type="submit" name="create_user" value="Create User">
	</form>

	<!-- Search user form -->
	<form method="post">
		<h2>Search User</h2>
		<input type="text" name="search_term" placeholder="Search Term">
		<input type="submit" name="search_user" value="Search User">
	</form>

	<!-- Search results -->
	<?php if (!empty($user_search_result)): ?>
		<h2>Search Results</h2>
		<?php foreach ($user_search_result as $user): ?>
			<div>
				<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
				<button
					onclick="document.getElementById('update_user_form_<?php echo $user['id']; ?>').style.display='block'">Edit
					User</button>
			</div>
			<!-- Update user form -->
			<form method="post" id="update_user_form_<?php echo $user['id']; ?>" style="display: none;">
				<h2>Update User</h2>
				<input type="text" name="first_name" placeholder="First Name" value="<?php echo $user['first_name']; ?>">
				<input type="text" name="last_name" placeholder="Last Name" value="<?php echo $user['last_name']; ?>">
				<input type="text" name="middle_name" placeholder="Middle Name" value="<?php echo $user['middle_name']; ?>">
				<input type="text" name="mobile_phone" placeholder="Mobile Phone" value="<?php echo $user['mobile_phone']; ?>">
				<input type="text" name="home_phone" placeholder="Home Phone" value="<?php echo $user['home_phone']; ?>">
				<input type="text" name="email" placeholder="Email" value="<?php echo $user['email']; ?>">
				<input type="text" name="password" placeholder="Password" value="<?php echo $user['password']; ?>">
				<input type="text" name="role" placeholder="Role" value="<?php echo $user['role']; ?>">
				<input type="text" name="personal_account" placeholder="Personal Account"
					value="<?php echo $user['personal_account']; ?>">
				<input type="text" name="address" placeholder="Address" value="<?php echo $user['address']; ?>">
				<input type="text" name="counter" placeholder="Counter" value="<?php echo $user['counter']; ?>">
				<input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
				<input type="submit" name="update_user" value="Update User">
			</form>
		<?php endforeach; ?>
	<?php endif; ?>

	<!-- Update meter form -->
	<form method="post">
		<h2>Update Meter</h2>
		<input type="text" name="meters" placeholder="Meters">
		<input type="text" name="date_update" placeholder="Date Update">
		<input type="hidden" name="meter_id" placeholder="Meter ID">
		<input type="submit" name="update_meter" value="Update Meter">
	</form>

	<footer>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/bootstrap.bundle.min.js"></script>
	</footer>
</body>

</html>