<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../vendor/connect.php';
if (!isset($_SESSION["operator_name"])) {
	header("Location: ../autorization.php");
	exit();
} else {
	$id = $_SESSION["id"];
	$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
	if ($stmt === false) {
		die($conn->error);
	}
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
}

if (isset($_POST['create_user'])) {
	$role = 'user'; // Setting role as 'user' by default
	$password = md5($_POST['password']); // Encrypt password
	$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, middle_name, mobile_phone, home_phone, email, password, role, personal_account, address, counter) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("sssssssssss", $_POST['first_name'], $_POST['last_name'], $_POST['middle_name'], $_POST['mobile_phone'], $_POST['home_phone'], $_POST['email'], $password, $role, $_POST['personal_account'], $_POST['address'], $_POST['counter']);
	$stmt->execute();
}


// Search for user
$user_search_result = [];
$user = [];
if (isset($_POST['search_user'])) {
	$query_parts = [];
	$params = [];
	$types = "";

	if (!empty($_POST['search_term1'])) {
		$query_parts[] = "first_name LIKE ?";
		$params[] = '%' . $_POST['search_term1'] . '%';
		$types .= "s";
	}

	if (!empty($_POST['search_term2'])) {
		$query_parts[] = "last_name LIKE ?";
		$params[] = '%' . $_POST['search_term2'] . '%';
		$types .= "s";
	}

	if (!empty($_POST['search_term3'])) {
		$query_parts[] = "email LIKE ?";
		$params[] = '%' . $_POST['search_term3'] . '%';
		$types .= "s";
	}

	if (!empty($_POST['search_term4'])) {
		$query_parts[] = "personal_account LIKE ?";
		$params[] = '%' . $_POST['search_term4'] . '%';
		$types .= "s";
	}

	if (!empty($_POST['search_term5'])) {
		$query_parts[] = "counter LIKE ?";
		$params[] = '%' . $_POST['search_term5'] . '%';
		$types .= "s";
	}

	if (!empty($query_parts)) {
		$stmt = $conn->prepare("SELECT * FROM users WHERE " . implode(" OR ", $query_parts));
		$stmt->bind_param($types, ...$params);
		$stmt->execute();
		$user_search_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	}
	$meters_for_user = [];
	if (!empty($user_search_result)) {
		foreach ($user_search_result as $user) {
			$stmt = $conn->prepare("SELECT m.id as meter_id, m.meters, m.date, mo.price_for_month FROM meters m JOIN months mo ON m.id=mo.id_meters WHERE m.id_user = ? ORDER BY m.date DESC");
			$stmt->bind_param("i", $user['id']);
			$stmt->execute();
			$meters_for_user[$user['id']] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		}
	}

	if (isset($_POST['update_meter'])) {
		date_default_timezone_set('Europe/Kiev');
		$current_date = date("Y-m-d H:i:s");
		$stmt = $conn->prepare("UPDATE meters SET meters=?, date_update=?, id_operator=? WHERE id=?");
		$stmt->bind_param("ssii", $_POST['meters'], $current_date, $id, $_POST['meter_id']);
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		} else {
			echo $stmt->affected_rows; // Will print out the number of affected rows
		}



	}

	if (isset($_POST['update_meter_amount'])) {
		$stmt = $conn->prepare("UPDATE months SET price_for_month=? WHERE id_meters=?");
		$stmt->bind_param("si", $_POST['price_for_month'], $_POST['meter_id']);
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}

	}
}

if (isset($_POST['update_user'])) {
	$password = md5($_POST['password']); // Encrypt password
	$stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, middle_name=?, mobile_phone=?, home_phone=?, email=?, password=?, role=?, personal_account=?, address=?, counter=? WHERE id=?");
	$stmt->bind_param("sssssssssssi", $_POST['first_name'], $_POST['last_name'], $_POST['middle_name'], $_POST['mobile_phone'], $_POST['home_phone'], $_POST['email'], $password, $_POST['role'], $_POST['personal_account'], $_POST['address'], $_POST['counter'], $_POST['user_id']);
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
	<link rel="stylesheet" href="css/style.css">
</head>

<body>
	<div class="container mt-5">
		<h1 class="text-center">Operator Page</h1>
		<a href="../index.php" class="btn btn-secondary my-3">Home</a>

		<a href="../vendor/logout.php" class="btn btn-primary my-3">Logout</a>

		<!-- Create user form -->
		<div class="card my-3">
			<div class="card-header">
				<h2>Create User</h2>
			</div>
			<div class="card-body">
				<form method="post">
					<input type="text" name="first_name" placeholder="First Name">
					<input type="text" name="last_name" placeholder="Last Name">
					<input type="text" name="middle_name" placeholder="Middle Name">
					<input type="text" name="mobile_phone" placeholder="Mobile Phone">
					<input type="text" name="home_phone" placeholder="Home Phone">
					<input type="text" name="email" placeholder="Email">
					<input type="text" name="password" placeholder="Password">
					<input type="text" name="personal_account" placeholder="Personal Account">
					<input type="text" name="address" placeholder="Address">
					<input type="text" name="counter" placeholder="Counter">
					<input type="submit" name="create_user" value="Create User">
				</form>
			</div>
		</div>

		<!-- Search user form -->
		<div class="card my-3">
			<div class="card-header">
				<h2>Search User</h2>
			</div>
			<div class="card-body">
				<form method="post">
					<input type="text" name="search_term1" placeholder="First Name">
					<input type="text" name="search_term2" placeholder="Last Name">
					<input type="text" name="search_term3" placeholder="Email">
					<input type="text" name="search_term4" placeholder="Personal Account">
					<input type="text" name="search_term5" placeholder="Counter">
					<input type="submit" name="search_user" value="Search User" class="btn btn-primary">
				</form>
			</div>
		</div>

		<div class="card my-3">
			<div class="card-header">
				<?php if (isset($user['first_name']) && isset($user['last_name'])): ?>
					<h2>Meters for
						<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
					</h2>
				<?php else: ?>
					<h2>Meters for User</h2>
				<?php endif; ?>

			</div>
			<div class="card-body">
				<?php if (isset($user['id']) && isset($meters_for_user[$user['id']])): ?>
					<table>
						<tr>
							<th>Meter</th>
							<th>Amount</th>
							<th>Edit Meter</th>
							<th>Edit Amount</th>
						</tr>
						<?php foreach ($meters_for_user[$user['id']] as $meter): ?>
							<tr>
								<td>
									<?php echo $meter['meters']; ?>
								</td>
								<td>
									<?php echo $meter['price_for_month']; ?>
								</td>
								<td>
									<form method="post">
										<input type="text" name="meters" placeholder="Meters">
										<input type="hidden" name="meter_id" value="<?php echo $meter['meter_id']; ?>">
										<input type="submit" name="update_meter" value="Update Meter">
									</form>
								</td>
								<td>
									<form method="post">
										<input type="text" name="price_for_month" placeholder="Amount">
										<input type="hidden" name="meter_id" value="<?php echo $meter['meter_id']; ?>">
										<input type="submit" name="update_meter_amount" value="Update Amount">
									</form>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				<?php else: ?>
					<p>No meters found for this user.</p>
				<?php endif; ?>
			</div>
		</div>
		<!-- Search results -->
		<?php if (!empty($user_search_result)): ?>
			<div class="card my-3">
				<div class="card-header">
					<h2>Search Results</h2>
				</div>
				<div class="card-body">
					<?php foreach ($user_search_result as $user): ?>
						<div>
							<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
							<button
								onclick="document.getElementById('update_user_form_<?php echo $user['id']; ?>').style.display='block'">
								Edit User
							</button>
						</div>
						<!-- Update user form -->
						<form method="post" id="update_user_form_<?php echo $user['id']; ?>" style="display: none;" class="my-3">
							<h2>Update User</h2>
							<input type="text" name="first_name" placeholder="First Name" value="<?php echo $user['first_name']; ?>">
							<input type="text" name="last_name" placeholder="Last Name" value="<?php echo $user['last_name']; ?>">
							<input type="text" name="middle_name" placeholder="Middle Name"
								value="<?php echo $user['middle_name']; ?>">
							<input type="text" name="mobile_phone" placeholder="Mobile Phone"
								value="<?php echo $user['mobile_phone']; ?>">
							<input type="text" name="home_phone" placeholder="Home Phone" value="<?php echo $user['home_phone']; ?>">
							<input type="text" name="email" placeholder="Email" value="<?php echo $user['email']; ?>">
							<input type="text" name="password" placeholder="Password" value="<?php echo $user['password']; ?>">
							<input type="text" name="role" placeholder="Role" value="<?php echo $user['role']; ?>">
							<input type="text" name="personal_account" placeholder="Personal Account"
								value="<?php echo $user['personal_account']; ?>">
							<input type="text" name="address" placeholder="Address" value="<?php echo $user['address']; ?>">
							<input type="text" name="counter" placeholder="Counter" value="<?php echo $user['counter']; ?>">
							<input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
							<input type="submit" name="update_user" value="Update User" class="btn btn-warning">
						</form>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>


	</div>

	<footer>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/bootstrap.bundle.min.js"></script>
	</footer>
</body>

</html>