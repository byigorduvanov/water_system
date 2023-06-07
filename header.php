<!doctype html>
<html lang="uk">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description"
		content="Система автоматизованої подачі показників води в Гусятині. Здійснюйте легку подачу показників, ведіть облік та сплачуйте за послуги з особистого кабінету.">
	<meta name="keywords" content="Гусятин, водоканал, подача показників води, облік показників, автоматизована система">
	<title>Система обліку та подачі показників води</title>
	<link rel="shortcut icon" type="image/png" href="img/favicon.png">
	<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
	<!-- Bootstrap CSS -->
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css">
	<!-- reCAPTCHA -->
	<script src="https://www.google.com/recaptcha/api.js"></script>
</head>

<body>
	<div class="wrapper_main">
		<header class="<?php echo isset($header_class) ? $header_class : ''; ?>">
			<div class="container">
				<div class="row">
					<div class="col">
						<nav class="navbar navbar-expand-lg navbar-light mt-3">
							<a class="navbar-brand" href="index.php">
								<img src="img/logo.png" alt="main logo" class="logo ">
							</a>
							<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
								aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon <?php echo isset($burger_black) ? $burger_black : ''; ?> ">
								</span>
							</button>
							<div class="collapse navbar-collapse" id="navbarNav">
								<ul class="navbar-nav col-12 col-md-12 col-lg-7">
									<li class="nav-item ">
										<a class="nav-link" href="index.php#permision">Переваги </a>
									</li>

									<li class="nav-item">
										<a class="nav-link" href="#footer">Запитання</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="vendor/Умови договору.pdf" target="_blank">Документи</a>
									</li>
									<li class="nav-item">
										<a class="nav-link " href="#footer">Допомога</a>
									</li>
									<li class="nav-item own-btn">
										<a class="nav-link " href="autorization.php">Мій кабінет</a>
									</li>
									<li class="nav-item own-btn">
										<a class="nav-link " href="registr.php">Зареєструватися</a>
									</li>
								</ul>
								<a href="autorization.php" class="btn btn-primary my_btn btn_room hidde" role="button">Мій
									кабінет</a>
								<a href="registr.php" class=" btn btn-primary my_btn btn_register hidde">Зареєструватися</a>
							</div>
						</nav>

					</div>
				</div>
			</div>

		</header>