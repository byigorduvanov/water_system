<?php
include 'vendor/connect.php';

$id = $_SESSION["id"];
global $id;
if (!empty($_SESSION["id"])) {
	global $conn;
	$id = $_SESSION["id"];
	$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
	$row = mysqli_fetch_assoc($result);
} else {
	header("Location: autorization.php");
}

$errors = array();
if (isset($_POST["do_save"])) {
	$address = $_POST["address"];
	$counter = $_POST["counter"];
	$mobile_phone = $_POST["mobile_phone"];
	$home_phone = $_POST["home_phone"];
	$query = "UPDATE users SET address='$address', counter='$counter', mobile_phone='$mobile_phone', home_phone='$home_phone' WHERE id=$id";
	mysqli_query($conn, $query);
	header("Location: room.php");
}
if (isset($_POST["do_meters"])) {
	if (trim($_POST['new_meter']) == '') {
		$errors[] = 'Введіть показник';
	}

	// Get the previous meter value
	$meters_table = meters_table();
	$old_user_meter = !empty($meters_table) ? end($meters_table)['meters'] : 0;
	$new_meter = $_POST["new_meter"];

	// Check if the new meter value is greater than the previous one
	if ($new_meter <= $old_user_meter) {
		$errors[] = 'The new meter value must be greater than the previous one';
	}

	if (empty($errors)) {
		$data_maters = $_POST["data_maters"];
		$query = "INSERT INTO `meters` (`id`, `id_user`, `meters`, `date`, `price`, `source`) VALUES (NULL, '$id', '$new_meter', '$data_maters', NULL, NULL);";
		mysqli_query($conn, $query);

		// header("Location: room.php");
		$meters_price = meters_price();
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

// function profit_table()
// {
// 	global $conn;
// 	global $id;
// 	$sql = "SELECT months.price_for_month, meters.date
// FROM meters
// INNER JOIN months ON meters.id = months.id_meters
// WHERE meters.id_user = $id";
// 	$result = mysqli_query($conn, $sql);

// 	$profit_table = mysqli_fetch_all($result, MYSQLI_ASSOC);
// 	return $profit_table;
// }

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

		$sql = "INSERT INTO months (price_for_month, id_meters, id_user) VALUES ($difference, $value4, $id)";
		$conn->query($sql);
	}
}


?>
<!doctype html>
<html lang="uk">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
	<a class="gotopbtn" href="#"> <i class="fas fa-arrow-up"></i> </a>
	<div class="wrapper">
		<header class="room-head">
			<div class="container">
				<div class="row">
					<div class="col-sm-2 col-5">
						<a class="navbar-brand" href="index.php">
							<img src="img/logo.png" alt="main logo" class="">
						</a>
					</div>
					<div class="col-lg-8 col-sm-6 col-6 hide__590">
						<div class="info">
							<p><span>32,5 грн</span><br>ціна на січень 2023 за 1 м3</p>
						</div>
					</div>
					<form action="#" method="post" class="col-sm-2 col-6 ">
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
		<section class="banner">
			<div class="container">
				<marquee direction="right" class="banner-item">Передача показників води повинна здійснюватися з 1 по 3 число
					кожного місяця</marquee>
			</div>
		</section>
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="tab">
						<button class="tablinks" data-tab="tab1" onclick="openTab(event, 'tab1')"><i
								class="fas fa-info-circle">
								<p class="tablinks__text">Мої дані</p>
							</i>
						</button>
						<button class="tablinks" data-tab="tab2" onclick="openTab(event, 'tab2')"><i
								class="fas fa-tachometer-alt">
								<p class="tablinks__text">Показники</p>
							</i></button>
						<button class="tablinks" data-tab="tab3" onclick="openTab(event, 'tab3')"><i class="fas fa-tasks">
								<p class="tablinks__text">Нарахування</p>
							</i></button>
						<button class="tablinks" data-tab="tab4" onclick="openTab(event, 'tab4')"><i
								class="fas fa-money-bill-wave-alt">
								<p class="tablinks__text">Оплата</p>
							</i></button>
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
										<div class="information">
											<div class="information-user bg-style d-flex">
												<div class="user-icon">
													<i class="fas fa-user-circle"></i>
												</div>
												<div class="user-name">
													<h3>
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
													<p class="caption__meter">Прийом показників здійснюється кожного місяця з 1 по 3
														число
														(включно)</p>

												</div>
												<div class="">
													<form action="#" method="POST">
														<input name="do_meters_save" type="number" placeholder="Введіть Ваш показник"
															class="form-control use-form room__form">
														<button type="button" class="popup-link btn btn-primary my_btn btn_room"
															data-target="#popup_meters" name="open_do_meters">Ввести</button>
													</form>
												</div>
											</div>
											<div class="about__pay bg-style d-flex">
												<div class="about__info">
													<p class="title__about">До сплати за воду</p>
													<p class="caption__about">станом
														<?= $value3 ?>
													</p>
												</div>
												<div class="about__price ml-auto">
													<p class="about__price__info">
														<?= $difference ?> грн
													</p>
												</div>
											</div>
											<?php
											if (isset($_POST['do_meters'])) {
												if (empty($errors)) {
													echo '<div class="alert alert-arrow d-flex rounded p-0 col-lg-12" role="alert">
                    <div class="alert-icon d-flex justify-content-center align-items-center text-white flex-grow-0 flex-shrink-0">
                        <i class="fa fa-check"></i>
                    </div>
                    <div class="alert-message d-flex align-items-center py-2 pl-4 pr-3">
                        Ваш показник прийнятий в обробку.
                    </div>
                    <a href="#" class="close d-flex ml-auto justify-content-center align-items-center px-3" data-dismiss="alert">
                        <i class="fas fa-times"></i>
                    </a>
                </div>';
												} else {
													echo '<div class="alert alert-arrow alert-arrow-danger d-flex rounded p-0 col-lg-12" role="alert">
        <div class="alert-icon d-flex justify-content-center align-items-center text-white flex-grow-0 flex-shrink-0">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="alert-message d-flex align-items-center py-2 pl-4 pr-3">
            Введіть показник.
        </div>
        <a href="#" class="close d-flex ml-auto justify-content-center align-items-center px-3" data-dismiss="alert">
            <i class="fas fa-times"></i>
        </a>
    </div>';
												}
											} ?>
											<div class="information-pays bg-style"></div>
										</div>
									</div>
									<div class="col-lg-6 col-md-11">
										<div id="result_form" class="my__date bg-style ">
											<div class="d-flex">
												<p class="title__date">МОЇ ДАНІ</p>
												<a class="ml-auto h-100 popup-link" href="#popup"><img src="img/date__checkbox.png"
														class="ml-auto h-100"></a>

											</div>
											<div class="row">
												<div class="col-6 date__user">
													<p class="date__user__text">Прізвище</p>
													<p>Ім’я</p>
													<p class="date__user__text">Адреса</p>
												</div>
												<div class="col-6 info__user">
													<p class="date__user__text">
														<?= $row['last_name']; ?>
													</p>
													<p>
														<?= $row['first_name']; ?>
													</p>
													<p class="date__user__text">
														<?= $row['address']; ?>
													</p>
												</div>
												<div class="col-12">
													<hr class="date__hr">
												</div>
											</div>
											<div class="row">
												<div class="col-6 date__user">
													<p>Номер лічильника</p>
													<p class="date__user__text ">E-mail</p>
													<p>Мобільний телефон</p>
													<p class="date__user__text">Домашній телефон</p>

												</div>
												<div class="col-6 info__user__date">
													<p>
														<?= $row['counter']; ?>
													</p>
													<p class="date__user__text">
														<?= $row['email']; ?>
													</p>
													<p>
														<?= $row['mobile_phone']; ?>
													</p>
													<p class="date__user__text">
														<?= $row['home_phone']; ?>
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
										<li class="page-item active " value="1"><a class="page-link" href="room.php?page=1">1</a>
										</li>
										<li class="page-item" value="2"><a class="page-link" href="room.php?page=2">2</a></li>
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
							<div id="tab4" class="tabcontent">
								4 панель</div>
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
					<form id="ajax_form" action="" method="POST" class="register-form popup__text">
						<div class="row">
							<div class="col">

								<!-- E-mail -->
								<label for="">Ваша адрес</label>
								<input type="text" name="address" value="<?= $row['address']; ?> "
									class="form-control form__registr" placeholder="Адрес">
								<label for="">Номер лічильника</label>
								<input type="text" name="counter" value="<?= $row['counter']; ?> "
									class="form-control form__registr" placeholder="Лічильник">
								<label for="">Ваш номер</label>
								<input type="text" name="mobile_phone" value="<?= $row['mobile_phone']; ?> "
									class="form-control form__registr " placeholder="Номер">
								<label for="">Ваш домашній номер</label>
								<input type="text" name="home_phone" value="<?= $row['home_phone']; ?> "
									class="form-control form__registr " placeholder="Номер домашній">
								<!-- Sign up button -->
								<button class="btn btn-primary my_btn btn_register mt-5" id="btn" type="submit"
									name="do_save">Зберегти</button>

							</div>
						</div>
					</form>
				</div>
			</div>

		</div>

	</div>
	<div id="popup_meters" class="popup">
		<div class="po popup__body">
			<div class="popup__content">
				<a href="#" class="popup__close close-popup">X</a>
				<div class="popup__title">Подача показника</div>
				<form action="#" method="POST" class="register-form popup__text">
					<div class="row">
						<div class="col">
							<label for="">№ Лічильника</label>
							<?php $meters_table = meters_table(); ?>
							<input type="text" name="counter" value="<?= $row['counter']; ?> "
								class="form-control form__registr" placeholder="Лічильник" readonly>
							<label for="">Попередній показник</label>
							<input type="number" name="old_user_meter" value="<?= end($meters_table)['meters']; ?>"
								class="form-control form__registr" placeholder="Показник" readonly>
							<label for="">Ввести показник</label>
							<input type="number" name="new_meter" value="" class="form-control form__registr"
								placeholder="Новий показник" required value="">
							<label for="data_maters">Дата</label>
							<input type="date" class="form-control form__registr" name="data_maters" id="data_maters"
								value="<?php echo date('Y-m-d'); ?>" readonly />

							<button class="btn btn-primary my_btn btn_register mt-5" type="submit"
								name="do_meters">Ввести</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php
	include('footer.php')
		?>