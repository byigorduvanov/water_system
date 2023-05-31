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
	$role = 'user';
	$password = md5($_POST['password']);
	$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, middle_name, mobile_phone, home_phone, email, password, role, personal_account, address, counter) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("sssssssssss", $_POST['first_name'], $_POST['last_name'], $_POST['middle_name'], $_POST['mobile_phone'], $_POST['home_phone'], $_POST['email'], $password, $role, $_POST['personal_account'], $_POST['address'], $_POST['counter']);
	$stmt->execute();
}


// Пошук користувача
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
}

if (isset($_POST['update_user'])) {
	$stmt = $conn->prepare("SELECT role, password FROM users WHERE id = ?");
	$stmt->bind_param("i", $_POST['user_id']);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$role = $row['role'];
	$password = !empty($_POST['password']) ? md5($_POST['password']) : $row['password'];
	$stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, middle_name=?, mobile_phone=?, home_phone=?, email=?, password=?, role=?, personal_account=?, address=?, counter=? WHERE id=?");
	$stmt->bind_param("sssssssssssi", $_POST['first_name'], $_POST['last_name'], $_POST['middle_name'], $_POST['mobile_phone'], $_POST['home_phone'], $_POST['email'], $password, $role, $_POST['personal_account'], $_POST['address'], $_POST['counter'], $_POST['user_id']);
	$stmt->execute();
}

$meters_for_user = [];
$payment_status_for_user = [];
if (!empty($user_search_result)) {
	foreach ($user_search_result as $user) {
		$stmt = $conn->prepare("SELECT m.id as meter_id, m.meters, m.date, mo.price_for_month, mo.payment_status FROM meters m JOIN months mo ON m.id=mo.id_meters WHERE m.id_user = ? ORDER BY m.date DESC");
		$stmt->bind_param("i", $user['id']);
		$stmt->execute();
		$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		$meters_for_user[$user['id']] = $result;
		$payment_status_for_user[$user['id']] = $result;
	}
}

if (isset($_POST['submit_all'])) {
	date_default_timezone_set('Europe/Kiev');
	$current_date = date("Y-m-d H:i:s");

	foreach ($_POST['meters'] as $meter_id => $meter_value) {
		// Отримання поточного показника 
		$stmt = $conn->prepare("SELECT meters FROM meters WHERE id = ?");
		$stmt->bind_param("i", $meter_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$current_meter = $row['meters'];
		$stmt->close();

		if ($current_meter != $meter_value) {
			$stmt = $conn->prepare("UPDATE meters SET meters=?, date_update=?, id_operator=? WHERE id=?");
			$stmt->bind_param("ssii", $meter_value, $current_date, $id, $meter_id);
			if (!$stmt->execute()) {
				echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			$stmt->close();
		}
	}

	foreach ($_POST['price_for_month'] as $meter_id => $price_value) {
		// Отримання поточного price_for_month
		$stmt = $conn->prepare("SELECT price_for_month FROM months WHERE id_meters = ?");
		$stmt->bind_param("i", $meter_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$current_price = $row['price_for_month'];
		$stmt->close();

		if ($current_price != $price_value) {
			$stmt = $conn->prepare("UPDATE months SET price_for_month=? WHERE id_meters=?");
			$stmt->bind_param("si", $price_value, $meter_id);
			if (!$stmt->execute()) {
				echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			$stmt->close();

			$stmt2 = $conn->prepare("UPDATE meters SET date_update=?, id_operator=? WHERE id=?");
			$stmt2->bind_param("sii", $current_date, $id, $meter_id);
			if (!$stmt2->execute()) {
				echo "Execute failed for second query: (" . $stmt2->errno . ") " . $stmt2->error;
			}
			$stmt2->close();
		}
	}

	foreach ($_POST['payment_status'] as $meter_id => $status_value) {
		// Отримання поточного статусу
		$stmt = $conn->prepare("SELECT payment_status FROM months WHERE id_meters = ?");
		$stmt->bind_param("i", $meter_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$current_status = $row['payment_status'];
		$stmt->close();

		if ($current_status != $status_value) {
			$stmt = $conn->prepare("UPDATE months SET payment_status=? WHERE id_meters=?");
			$stmt->bind_param("si", $status_value, $meter_id);
			if (!$stmt->execute()) {
				echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			$stmt->close();

			$stmt2 = $conn->prepare("UPDATE meters SET date_update=?, id_operator=? WHERE id=?");
			$stmt2->bind_param("sii", $current_date, $id, $meter_id);
			if (!$stmt2->execute()) {
				echo "Execute failed for second query: (" . $stmt2->errno . ") " . $stmt2->error;
			}
			$stmt2->close();
		}
	}
}

?>
<!DOCTYPE html>
<html lang="uk">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>оператор</title>
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
		<h1 class="text-center">Сторінка оператора</h1>
		<a href="../index.php" class="btn btn-secondary my-3">Головна</a>

		<a href="../vendor/logout.php" class="btn btn-primary my-3">Вихід</a>

		<!-- Create user form -->
		<div class="card my-3">
			<div class="card-header">
				<h2>Створити користувача</h2>
			</div>
			<div class="card-body">
				<form method="post">
					<input type="text" name="first_name" placeholder="Ім'я">
					<input type="text" name="last_name" placeholder="Прізвище">
					<input type="text" name="middle_name" placeholder="Ім'я по батькові">
					<input type="text" name="mobile_phone" placeholder="Мобільний телефон">
					<input type="text" name="home_phone" placeholder="Домашній телефон">
					<input type="text" name="email" placeholder="Електронна пошта">
					<input type="text" name="password" placeholder="Пароль">
					<input type="text" name="personal_account" placeholder="Особистий рахунок">
					<input type="text" name="address" placeholder="Адреса">
					<input type="text" name="counter" placeholder="Лічильник">
					<input type="submit" name="create_user" value="Створити користувача">
				</form>
			</div>
		</div>

		<!-- Search user form -->
		<div class="card my-3">
			<div class="card-header">
				<h2>Пошук користувача</h2>
			</div>
			<div class="card-body">
				<form method="post">
					<input type="text" name="search_term1" placeholder="Ім'я">
					<input type="text" name="search_term2" placeholder="Прізвище">
					<input type="text" name="search_term3" placeholder="Електронна пошта">
					<input type="text" name="search_term4" placeholder="Особистий рахунок">
					<input type="text" name="search_term5" placeholder="Лічильник">
					<input type="submit" name="search_user" value="Пошук користувача" class="btn btn-primary">
				</form>
			</div>
		</div>

		<div class="card my-3">
			<div class="card-header">
				<?php if (isset($user['first_name']) && isset($user['last_name'])): ?>
					<h2>Показники для
						<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
					</h2>
				<?php else: ?>
					<h2>Показники для користувача</h2>
				<?php endif; ?>

			</div>
			<div class="card-body">
				<?php if (isset($user['id']) && isset($meters_for_user[$user['id']])): ?>
					<form method="post">
						<table>
							<tr>
								<th>Показники</th>
								<th>Сума</th>
								<th>Статус оплати</th>
								<th>Редагування Показників</th>
								<th>Редагувати суму</th>
								<th>Дата</th>
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
										<?php echo $meter['payment_status']; ?>
									</td>
									<td>
										<input type="text" name="meters[<?php echo $meter['meter_id']; ?>]"
											value="<?php echo $meter['meters']; ?>">
									</td>
									<td>
										<input type="text" name="price_for_month[<?php echo $meter['meter_id']; ?>]"
											value="<?php echo $meter['price_for_month']; ?>">
									</td>
									<td>
										<select name="payment_status[<?php echo $meter['meter_id']; ?>]">
											<option value="не сплачено" <?php echo ($meter['payment_status'] == 'не сплачено') ? 'selected' : ''; ?>>не сплачено</option>
											<option value="сплачено" <?php echo ($meter['payment_status'] == 'сплачено') ? 'selected' : ''; ?>>сплачено</option>
											<option value="в очікуванні" <?php echo ($meter['payment_status'] == 'в очікуванні') ? 'selected' : ''; ?>>в очікуванні</option>
											<option value="прострочений" <?php echo ($meter['payment_status'] == 'прострочений') ? 'selected' : ''; ?>>прострочений</option>
											<option value="часткова оплата" <?php echo ($meter['payment_status'] == 'часткова оплата') ? 'selected' : ''; ?>>часткова оплата</option>
											<option value="скасовано" <?php echo ($meter['payment_status'] == 'скасовано') ? 'selected' : ''; ?>>скасовано</option>
											<option value="відшкодовано" <?php echo ($meter['payment_status'] == 'відшкодовано') ? 'selected' : ''; ?>>відшкодовано</option>
											<option value="невдалий" <?php echo ($meter['payment_status'] == 'невдалий') ? 'selected' : ''; ?>>невдалий</option>
										</select>
									</td>
									<td>
										<?php echo $meter['date']; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
						<input type="submit" name="submit_all" value="Оновити">
					</form>
				<?php else: ?>
					<p>Для цього користувача не знайдено лічильників.</p>
				<?php endif; ?>
			</div>

			<!-- Search results -->
			<?php if (!empty($user_search_result)): ?>
				<div class="card my-3">
					<div class="card-header">
						<h2>Результати пошуку</h2>
					</div>
					<div class="card-body">
						<?php foreach ($user_search_result as $user): ?>
							<div>
								<?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
								<button
									onclick="document.getElementById('update_user_form_<?php echo $user['id']; ?>').style.display='block'">
									Редагувати користувача
								</button>
							</div>
							<!-- Update user form -->
							<form method="post" id="update_user_form_<?php echo $user['id']; ?>" style="display: none;" class="my-3">
								<h2>Редагувати користувача</h2>
								<input type="text" name="first_name" placeholder="Ім'я" value="<?php echo $user['first_name']; ?>">
								<input type="text" name="last_name" placeholder="Прізвище" value="<?php echo $user['last_name']; ?>">
								<input type="text" name="middle_name" placeholder="Ім'я по батькові"
									value="<?php echo $user['middle_name']; ?>">
								<input type="text" name="mobile_phone" placeholder="Мобільний телефон"
									value="<?php echo $user['mobile_phone']; ?>">
								<input type="text" name="home_phone" placeholder="Домашній телефон"
									value="<?php echo $user['home_phone']; ?>">
								<input type="text" name="email" placeholder="Електронна пошта" value="<?php echo $user['email']; ?>">
								<input type="text" name="password" placeholder="Пароль" value="<?php echo $user['password']; ?>">
								<input type="text" name="personal_account" placeholder="Особистий рахунок"
									value="<?php echo $user['personal_account']; ?>">
								<input type="text" name="address" placeholder="Адреса" value="<?php echo $user['address']; ?>">
								<input type="text" name="counter" placeholder="Лічильник" value="<?php echo $user['counter']; ?>">
								<input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
								<input type="submit" name="update_user" value="Оновити користувача" class="btn btn-warning">
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