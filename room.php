<?php
include 'vendor/connect.php';


$id = $_SESSION["id"];
global $id;
if (!isset($_SESSION["user_name"])) {
	header("Location: autorization.php");
	exit();
} else {
	global $conn;
	$id = $_SESSION["id"];
	$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
	$row = mysqli_fetch_assoc($result);
}


$success_message = '';
$errors = array();
$errors_html = [];
if (isset($_POST["do_save"])) {
	$address = mysqli_real_escape_string($conn, $_POST["address"]);
	$counter = mysqli_real_escape_string($conn, $_POST["counter"]);
	$mobile_phone = mysqli_real_escape_string($conn, $_POST["mobile_phone"]);
	$first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
	$last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
	$middle_name = mysqli_real_escape_string($conn, $_POST["middle_name"]);

	$query = "UPDATE users SET address='$address', counter='$counter', mobile_phone='$mobile_phone', first_name='$first_name', last_name='$last_name', middle_name='$middle_name' WHERE id=$id";
	mysqli_query($conn, $query);
	header("Location: room.php");
	$success_message = 'Дані оновлено';
}

if (isset($_POST["do_meters"])) {
	if (trim($_POST['new_meter']) == '') {
		$errors[] = 'Введіть показник';
	}

	// Check if the counter is not empty
	if (trim($_POST['counter']) == '') {
		$errors_html[] = 'Потрібен лічильник, ви можете додати його, натиснувши на <a class="ml-auto h-100 popup-link" href="#popup"><img src="img/date__checkbox.png" class="ml-auto h-100" alt="edit_my_data"></a>';
	}
	// Get the previous meter value
	$meters_table = meters_table();
	$old_user_meter = !empty($meters_table) ? end($meters_table)['meters'] : 0;
	$new_meter = mysqli_real_escape_string($conn, $_POST["new_meter"]);

	// Check if the new meter value is greater than the previous one
	if ($new_meter <= $old_user_meter) {
		$errors[] = 'Нове значення лічильника має бути більшим за попереднє';
	}
	// Merge both error arrays
	$all_errors = array_merge($errors, $errors_html);
	if (empty($all_errors)) {
		$data_maters = mysqli_real_escape_string($conn, $_POST["data_maters"]);
		$query = "INSERT INTO `meters` (`id`, `id_user`, `meters`, `date`, `date_update`, `id_operator`) VALUES (NULL, '$id', '$new_meter', '$data_maters', NULL, NULL);";
		mysqli_query($conn, $query);

		// header("Location: room.php");
		$meters_price = meters_price();
		$success_message = 'Показник подано';
	}
}



function meters_table()
{
	global $conn;

	$id = $_SESSION["id"];
	$sql = "SELECT * FROM `meters` WHERE id_user=$id ";


	$result = mysqli_query($conn, $sql);
	$meters_table = mysqli_fetch_all($result, MYSQLI_ASSOC);
	return $meters_table;
}

//обрахунок останніх показників
global $difference;
global $value3;
global $value4;
function meters_price()
{
	global $conn;
	$id = $_SESSION["id"];
	global $difference;
	global $value3;
	global $value4;
	$sql = "SELECT * FROM meters WHERE id_user = $id ORDER BY meters DESC LIMIT 2";
	$result = $conn->query($sql);
	if ($result->num_rows == 2) {
		$row1 = $result->fetch_assoc();
		$row2 = $result->fetch_assoc();
		$value1 = $row1["meters"];
		$value2 = $row2["meters"];

		// Визначення різниці між значеннями
		$difference = ($value1 - $value2) * 32.5;

		// Повернення курсора на початок результатів запиту
		$result->data_seek(0);

		// Взяття третього рядка з результатів запиту
		$row3 = $result->fetch_assoc();
		$value3 = $row3["date"];

		// Повернення курсора на початок результатів запиту
		$result->data_seek(0);

		// Взяття третього рядка з результатів запиту
		$id_meters = $result->fetch_assoc();
		$value4 = $id_meters["id"];

		// Встановлення статусу попередніх несплачених нарахувань як "прострочений"
		$sql_overdue = "UPDATE months SET payment_status='прострочений' WHERE id_user = $id AND payment_status='не сплачено'";
		$conn->query($sql_overdue);

		// Додавання нового запису з нарахуваннями
		$sql = "INSERT INTO months (price_for_month, id_meters, id_user,payment_status) VALUES ($difference, $value4, $id, 'не сплачено')";
		$conn->query($sql);
	}
}

function get_latest_charges_and_status()
{
	global $conn;
	$id = $_SESSION["id"];
	$sql = "SELECT m.price_for_month, m.payment_status, me.date FROM months m JOIN meters me ON m.id_meters = me.id WHERE m.id_user = $id ORDER BY m.id_meters DESC LIMIT 1";
	$result = mysqli_query($conn, $sql);
	if ($result === false) {
		// Handle error - the query failed
	}
	$row = mysqli_fetch_assoc($result);
	if ($row === null) {
		// Handle error - no data was returned
	}
	return $row;
}
$latest_charges_data = get_latest_charges_and_status();
$latest_charges = isset($latest_charges_data['price_for_month']) ? $latest_charges_data['price_for_month'] : 0;
$payment_status = isset($latest_charges_data['payment_status']) ? $latest_charges_data['payment_status'] : 'не сплачено';
$date_of_indicator = isset($latest_charges_data['date']) ? $latest_charges_data['date'] : '';

function pay_latest_charge()
{
	global $conn;
	$id = $_SESSION["id"];
	$sql = "UPDATE months SET payment_status = 'сплачено' WHERE id_user = $id AND payment_status = 'не сплачено' ORDER BY id_meters DESC LIMIT 1";
	$result = mysqli_query($conn, $sql);
	if ($result === false) {
		// Handle error - the query failed
	}
}

if (isset($_POST['pay_latest_charge'])) {
	pay_latest_charge();
	// Refresh the page to update the payment status
	header("Location: " . $_SERVER['REQUEST_URI']);
	exit();
}

// Include 'generate_receipt.php' only if payment_status is 'не сплачено' and latest_charges is not null
if ($payment_status === 'не сплачено' && $latest_charges !== null && $latest_charges > 0) {
	require_once('vendor/generate_receipt.php');
}

?>
<!doctype html>
<html lang="uk">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description"
		content="Особистий кабінет користувача для автоматизованої системи подачі показників води в Гусятині. Ведіть облік та сплачуйте за послуги з особистого кабінету.">
	<meta name="keywords"
		content="Гусятин, водоканал, подача показників води, облік показників, особистий кабінет, автоматизована система">
	<title>Особистий кабінет користувача</title>
	<link rel="shortcut icon" type="image/png" href="img/favicon.png">
	<link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css">
</head>

<body>
	<div class="hidden">
		<?php
		include 'fetch_data.php';
		include 'fetch_profit.php';
		?>
	</div>
	<!--    /*scroll to top*/-->
	<div class="wrapper_main">

		<!-- контент -->
		<div class="wrapper">
			<header class="room-head">
				<div class="container">
					<div class="row">
						<div class="col-sm-2 col-5 col-lg-2">
							<a class="navbar-brand" href="index.php">
								<img src="img/logo.png" alt="main logo" class="">
								<div class=""></div>
							</a>
						</div>
						<div class="col-lg-8 col-sm-6 col-6 hide__590">
							<div class="info">
								<p><span>32,5 грн</span><br>ціна на січень 2023 за 1 м3</p>
							</div>
						</div>
						<form action="#" method="post" class="col-sm-2 col-6 col-lg-2">
							<a href="vendor/logout.php" class="btn btn-primary my_btn btn_room">Вихід </a>
						</form>
					</div>
				</div>
			</header>
			<div class="container">
				<div class="row">
					<div class="col">
						<div class="col-12">
							<div class="info-hidde">
								<p><span>32.5 грн</span><br>ціна на січень 2023 за 1 м3</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="banner">
				<div class="container" id="scroll-container">
					<div class="banner-item" id="scroll-text">
						Передача показників води повинна здійснюватися з 1 по 3 число кожного місяця
					</div>
				</div>
			</div>
			<div class="container">
				<div class="row">
					<div class="col">
						<div class="tab">
							<button class="tablinks" data-tab="tab1" onclick="openTab(event, 'tab1')">
								<span class="icon-text d-flex">
									<i class="fas fa-info-circle"></i>
									<span class="tablinks__text">Мої дані</span>
								</span>
							</button>
							<button class="tablinks" data-tab="tab2" onclick="openTab(event, 'tab2')">
								<span class="icon-text d-flex">
									<i class="fas fa-tachometer-alt"></i>
									<span class="tablinks__text">Показники</span>
								</span>
							</button>
							<button class="tablinks" data-tab="tab3" onclick="openTab(event, 'tab3')">
								<span class="icon-text d-flex">
									<i class="fas fa-tasks"></i>
									<span class="tablinks__text">Нарахування</span>
								</span>
							</button>
							<button class="tablinks" data-tab="tab4" onclick="openTab(event, 'tab4')">
								<span class="icon-text d-flex">
									<i class="fas fa-money-bill-wave-alt"></i>
									<span class="tablinks__text">Оплата</span>
								</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<section class="room-date">
				<div class="container">
					<div class="row">
						<div class="col">
							<div class="tab-content" id="product-content">
								<!--                            <div class="tab-pane fade " id="description" role="tabpanel" aria-labelledby="description-tab">-->
								<div id="tab1" class="tabcontent">
									<div class="row">
										<div class="col-lg-6 col-md-11 col-sm-12">
											<div id="error-messages" class="mt-3">
												<?php
												if (!empty($all_errors)) {
													echo '<div class="alert alert-danger mt-2" role="alert">';
													foreach ($all_errors as $error) {
														echo '<p>' . $error . '</p>';
													}
													echo '</div>';
												}

												if (isset($_POST["do_save"]) || isset($_POST["do_meters"])) {
													if (empty($all_errors) && !empty($success_message)) {
														echo '<div class="alert alert-success mt-2" role="alert">';
														echo '<p>' . $success_message . '</p>';
														echo '</div>';
													}
												}
												?>
											</div>
											<div class="information">
												<div class="information-user bg-style d-flex">
													<div class="user-icon">
														<i class="fas fa-user-circle"></i>
													</div>
													<div class="user-name">
														<h3 class="pb-2">
															<?= $row['last_name']; ?>
															<?= $row['first_name']; ?>
														</h3>
														<p>
															<?= $row['address']; ?>
														</p>
													</div>
												</div>
												<div class="information-meters bg-style d-flex">
													<div class="info__meter mb-4">
														<h4 class="title__meter">Подати показник</h4>
														<p class="caption__meter">Прийом показників здійснюється
															кожного
															місяця з 1 по 3
															число
															(включно)</p>

													</div>
													<div class="">
														<form action="#" method="POST">
															<input name="do_meters_save" type="number"
																placeholder="Введіть Ваш показник"
																class="form-control use-form room__form">
															<button type="button" class="popup-link btn btn-primary my_btn btn_room"
																data-target="#popup_meters" name="open_do_meters">Ввести</button>
														</form>
													</div>
												</div>
												<div class="about__pay bg-style d-flex">
													<div class="about__info">
														<p class="caption__about">станом
															<?php if ($payment_status == 'не сплачено') { ?>
																<?= $date_of_indicator ?>
															<?php } ?>
														</p>
														<!-- Only display the link to generate receipt if payment_status is 'не сплачено' -->
														<?php if ($payment_status === 'не сплачено'): ?>
															<a class="caption__about generate__receipt  btn btn-primary my_btn btn_room mt-4"
																href="?generate_receipt=1" target="_blank">
																<i class="fas fa-print pr-2"></i>Сформувати квитанцію
															</a>
														<?php endif; ?>
													</div>
													<div class="about__price ml-auto">
														<p class="about__price__info pb-1" id="charges-info">
															<?php if ($payment_status == 'не сплачено') { ?>
																<?= $latest_charges ?> грн
															<?php } ?>
														</p>
													</div>

												</div>
												<div class="information-pays bg-style"></div>
											</div>
										</div>
										<div class="col-lg-6 col-md-11">
											<div id="result_form" class="my__date bg-style ">
												<div class="d-flex">
													<p class="title__date">МОЇ ДАНІ</p>
													<a class="ml-auto h-100 popup-link" href="#popup">
														<img src="img/date__checkbox.png" class="ml-auto h-100" alt="edit_my_data">
													</a>
												</div>
												<div class="row">
													<div class="col-6 date__user">
														<p class="date__user__text">Прізвище</p>
														<p>Ім’я</p>
														<p class="date__user__text">По батькові</p>
														<p>Адреса</p>
													</div>
													<div class="col-6 info__user">
														<p class="date__user__text">
															<?= $row['last_name']; ?>
														</p>
														<p>
															<?= $row['first_name']; ?>
														</p>
														<p class="date__user__text">
															<?= $row['middle_name']; ?>
														</p>
														<p style="line-height: 23px;">
															<?= $row['address']; ?>
														</p>
													</div>
													<div class="col-12">
														<hr class="date__hr">
													</div>
												</div>
												<div class="row">
													<div class="col-6 date__user">
														<p>Особовий рахунок</p>
														<p class="date__user__text ">Номер лічильника</p>
														<p>E-mail</p>
														<p class="date__user__text ">Мобільний телефон</p>

													</div>
													<div class="col-6 info__user__date">
														<p>
															<?= $row['personal_account']; ?>
														</p>
														<p class="date__user__text">
															<?= $row['counter']; ?>
														</p>
														<p>
															<?= $row['email']; ?>
														</p>
														<p class="date__user__text">
															<?= $row['mobile_phone']; ?>
														</p>

													</div>
													<div class="col-12">
														<hr class="date__hr">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!--                            <div class="tab-pane fade" id="characteristics" role="tabpanel" aria-labelledby="characteristics-tab">-->
								<div id="tab2" class="tabcontent">
									<div class="room-meters room-meters__top">
										<p class="table__top">Показники</p>
									</div>
									<form>
										<select id="year-select" name="year" class="select__meters">
											<?php
											$years = mysqli_query($conn, "SELECT DISTINCT YEAR(date) as year FROM meters WHERE id_user=$id ORDER BY year DESC");
											while ($year_select = mysqli_fetch_assoc($years)) {
												echo '<option value="' . $year_select['year'] . '">' . $year_select['year'] . '</option>';
											}
											?>
										</select>
									</form>
									<table class="table table-bordered table-room">
										<thead class="table-room__top">
											<tr>
												<th scope="col">№ лічильника</th>
												<th scope="col">Дата</th>
												<th scope="col">Показник</th>
												<th scope="col">Джерело</th>
											</tr>
										</thead>
										<!-- Create an empty tbody that will be filled by the fetchTableData function -->
										<tbody class="table-row" id="table-row">
										</tbody>
									</table>
									<nav aria-label="..." class="">
										<ul class="pagination pagination-sm justify-content-center" id="page-select">
											<li class="page-item active " data-value="1">
												<a class="page-link" href="room.php?page=1">1</a>
											</li>
											<li class="page-item" data-value="2">
												<a class="page-link" href="room.php?page=2">2</a>
											</li>
										</ul>
									</nav>
									<div class="chart-container" style="position: relative;">
										<canvas id="histogram" class="diagram"></canvas>
									</div>
								</div>
								<!--                            <div class="tab-pane fade" id="opinion" role="tabpanel" aria-labelledby="opinion-tab">-->
								<div id="tab3" class="tabcontent">
									<div class="room-meters room-meters__top">
										<p class="table__top">Нарахування</p>
									</div>
									<select id="year-select_profit" name="year_profit" class="select__meters profit">
										<?php
										$years = mysqli_query($conn, "SELECT DISTINCT YEAR(date) as year FROM meters WHERE id_user=$id ORDER BY year DESC");
										while ($year_select = mysqli_fetch_assoc($years)) {
											echo '<option value="' . $year_select['year'] . '">' . $year_select['year'] . '</option>';
										}
										?>
									</select>

									<table class="table table-bordered table-room">
										<thead class="table-room__top">
											<tr>
												<th scope="col">Період</th>
												<th scope="col">Борг на поч. міс.</th>
												<th scope="col">Нараховано</th>
												<th scope="col">Сплачено</th>
											</tr>
										</thead>
										<tbody class="table-row">

										</tbody>
									</table>
									<div class="chart-container" style="position: relative;">
										<canvas id="profitChart" class="diagram"></canvas>
									</div>
								</div>
								<!--                            <div class="tab-pane fade" id="pay" role="tabpanel" aria-labelledby="pay-tab">-->
								<div id="tab4" class="tabcontent p-3">
									<h2 class="text-center title__date">Виберіть спосіб оплати Нарахування</h2>
									<p class="mt-3">Оплата за допомогою квитинації</p>
									<a class="caption__about generate__receipt btn btn-primary my_btn btn_room mt-4 d-block ml-0"
										href="?generate_receipt=1" target="_blank">
										<i class="fas fa-print pr-2"></i>Сформувати квитанцію
									</a>
									<p class="mt-5">Онлайн оплата</p>
									<a class="d-block mt-2 generate__receipt btn btn-primary my_btn " target="_blank"
										href="https://www.privat24.ua/rd/send_qr/liqpay_static_qr/payment_2323275452.dc8331f859cfb244c5ee136d630831409ff4c8ae28d42775a70c7858a1d61fe6">Оплата</a>
									<p class="mt-5">Тестова Оплата(В розробці)</p>
									<form action="#" method="POST">
										<button class="btn btn-warning my_btn mt-2 mb-5" type="submit"
											name="pay_latest_charge">Оплатити останній рахунок</button>
									</form>
								</div>

							</div>
						</div>
					</div>
				</div>
			</section>
			<div id="popup" class="popup">
				<div class="po popup__body">
					<div class="popup__content">
						<a href="#" class="popup__close close-popup">X</a>
						<div class="popup__title">Редагуйте свої дані</div>
						<form id="ajax_form" method="POST" class="register-form popup__text">
							<div class="row">
								<div class="col">

									<!-- Address -->
									<label for="address">Ваша адрес</label>
									<input id="address" type="text" name="address"
										value="<?= htmlspecialchars($row['address']); ?>" class="form-control form__registr"
										placeholder="Адрес">

									<!-- Counter -->
									<label for="counter">Номер лічильника</label>
									<input id="counter" type="text" name="counter"
										value="<?= htmlspecialchars($row['counter']); ?>" class="form-control form__registr"
										placeholder="Лічильник">

									<!-- Mobile Phone -->
									<label for="mobile_phone">Ваш номер</label>
									<input id="mobile_phone" type="text" name="mobile_phone"
										value="<?= htmlspecialchars($row['mobile_phone']); ?>" class="form-control form__registr"
										placeholder="Номер">

									<!-- First Name -->
									<label for="first_name">Ваше ім’я</label>
									<input id="first_name" type="text" name="first_name"
										value="<?= htmlspecialchars($row['first_name']); ?>" class="form-control form__registr"
										placeholder="Введіть ім’я">

									<!-- Last Name -->
									<label for="last_name">Ваше прізвище</label>
									<input id="last_name" type="text" name="last_name"
										value="<?= htmlspecialchars($row['last_name']); ?>" class="form-control form__registr"
										placeholder="Введіть прізвище">

									<!-- Middle Name -->
									<label for="middle_name">Ваше по батькові</label>
									<input id="middle_name" type="text" name="middle_name"
										value="<?= htmlspecialchars($row['middle_name']); ?>" class="form-control form__registr"
										placeholder="Введіть по батькові">

									<!-- Save button -->
									<button class="btn btn-primary my_btn btn_register mt-5" id="btn" type="submit"
										name="do_save">Зберегти</button>

								</div>
							</div>
						</form>

					</div>
				</div>
			</div>
			<div id="popup_meters" class="popup">
				<div class="po popup__body">
					<div class="popup__content">
						<a href="#" class="popup__close close-popup">X</a>
						<div class="popup__title">Подача показника</div>
						<form method="POST" class="register-form popup__text">
							<div class="row">
								<div class="col">
									<label for="counter_form_meters">№ Лічильника</label>
									<?php $meters_table = meters_table(); ?>
									<input id="counter_form_meters" type="text" name="counter"
										value="<?= htmlspecialchars($row['counter']); ?>" class="form-control form__registr"
										placeholder="Лічильник" readonly>
									<label for="old_user_meter">Попередній показник</label>
									<input id="old_user_meter" type="number" name="old_user_meter"
										value="<?= htmlspecialchars(end($meters_table)['meters']); ?>"
										class="form-control form__registr" placeholder="Показник" readonly>
									<label for="new_meter">Ввести показник</label>
									<input id="new_meter" type="number" name="new_meter" value=""
										class="form-control form__registr" placeholder="Новий показник">
									<label for="data_maters">Дата</label>
									<input type="date" class="form-control form__registr" name="data_maters" id="data_maters"
										value="<?php echo date('Y-m-d'); ?>" readonly>

									<button class="btn btn-primary my_btn btn_register mt-5" type="submit"
										name="do_meters">Ввести</button>
								</div>
							</div>
						</form>

					</div>
				</div>
			</div>

			<a class="gotopbtn" href="#"> <i class="fas fa-arrow-up"></i> </a>
		</div>
		<script src="js/scroll_top.js"></script>
		<script src="js/main.js"></script>
		<script src="js/fetch_profit.js"></script>
		<?php
		include('footer.php')
			?>