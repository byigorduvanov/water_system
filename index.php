<?php
$header_class = 'about__offer';
include('header.php');
?>
<section class="about__offer">
	<!--    /*scroll to top*/-->
	<a class="gotopbtn" href="#"> <i class="fas fa-arrow-up"></i> </a>

	<div class="container" data-aos="fade-down">
		<div class="row">
			<div class="col">
				<div class="logo-mobile">
					<a href="index.php">
						<img src="img/logo-mobile.png" alt="main logo" class="">
					</a>
				</div>
				<div class="offer-title d-flex">
					<div class="col-lg-8 col-md-7 col-sm-12 offer">
						<h2 class="display-4 offer-head">Здійснюй подачу показників води легко та швидко, без жодних
							зусиль
						</h2>
						<p class="lead offer-desk">Система автоматизованої подачі показників, зекономить Ваш час та
							дозволить
							легко та швидко здійснювати оплату за надані послуги</p>

					</div>
					<div class="col-lg-4 col-md-5 col-sm-12 offer-img offer">
						<img src="img/main-image.png" alt="Подача показників води" class="desktop-img">
						<img src="img/tablet.png" alt="Подача показників води" class="tablet-img">
					</div>
				</div>
				<div class="col-12 offer__btn__block">
					<a class="btn btn-primary btn-lg my_btn offer-btn" href="autorization.php" role="button">Спробувати
						зараз</a>
				</div>
			</div>
		</div>
	</div>

</section>
<section data-aos="fade-up" class="permision" id="permision">
	<div class="container">
		<div class="row">
			<div class="col">
				<h2 class="permision-head">Переваги нашої системи</h2>
			</div>
		</div>
		<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4">
			<div class="col ">
				<div class="card h-100 card__pesmision">
					<img src="img/image_5.png" class="card-img-top permision-img rounded mx-auto d-block" alt="...">
					<div class="card-body">
						<h5 class="card-title permision-title">Зручна оплата </h5>
						<p class="card-text permision-text">Сплачуйте за послуги в будь-який час доби</p>
					</div>
				</div>
			</div>
			<div class="col ">
				<div class="card h-100 card__pesmision">
					<img src="img/image_6.png" class="card-img-top permision-img rounded mx-auto d-block" alt="...">
					<div class="card-body">
						<h5 class="card-title permision-title">Історія платежів</h5>
						<p class="card-text permision-text">Перегляд історії показників лічильників та перегляд звітів
						</p>
					</div>
				</div>
			</div>
			<div class="col ">
				<div class="card h-100 card__pesmision ">
					<img src="img/image_4.png" class="card-img-top permision-img rounded mx-auto d-block" alt="...">
					<div class="card-body">
						<h5 class="card-title permision-title">Повний контроль</h5>
						<p class="card-text permision-text">Контроль нарахувань та оплати по особовому рахунку</p>
					</div>
				</div>
			</div>
			<div class="col ">
				<div class="card h-100 card__pesmision">
					<img src="img/image_3.png" class="card-img-top permision-img rounded mx-auto d-block" alt="...">
					<div class="card-body">
						<h5 class="card-title permision-title">Особистий кабінет</h5>
						<p class="card-text permision-text">Перегляд та оплата рахунків за декількома адресами</p>
					</div>
				</div>
			</div>
		</div>

	</div>
</section>
<section class="registration" data-aos="zoom-in-up">
	<div class="container">
		<div class="row">
			<div class="col">
				<h2 class="register-head">Проста та швидка реєстрація</h2>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<div class="card-deck">
					<div class="card">
						<img src="img/image_7.png" class="card-img-top register-img rounded mx-auto d-block" alt="...">
						<div class="card-body">
							<h5 class="card-title register-title">Крок 1: <span>Зареєструйтеся</span></h5>
							<p class="card-text register-text">Введіть свою електронну адресу та пароль</p>

						</div>
					</div>
					<div class="card">
						<img src="img/image_8.png" class="card-img-top register-img rounded mx-auto d-block" alt="...">
						<div class="card-body">
							<h5 class="card-title register-title">Крок 2:<span> Підтвердіть свій email</span></h5>
							<p class="card-text register-text">Підтвердіть свої дані у поштовій скринці та перейдіть у
								свій
								особистий кабінет</p>

						</div>
					</div>
					<div class="card">
						<img src="img/image_9.png" class="card-img-top register-img rounded mx-auto d-block" alt="...">
						<div class="card-body">
							<h5 class="card-title register-title">Крок 3:<span> Додайте особовий рахунок</span></h5>
							<p class="card-text register-text">Введіть свій особовий рахунок, який знаходиться на
								квитанції</p>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col ">
				<form class="form justify-content-center d-flex offer__form">
					<div class="form-group m-0">
						<input type="email" class="form-control use-form tablet-form" id="quickEmail"
							placeholder="Введіть email">
					</div>
					<a href="registr.php" class="btn btn-primary my_btn form-btn"
						onclick="saveEmailToStorage()">Зареєструватися</a>
				</form>

			</div>
		</div>
	</div>
</section>
<script src="js/scroll_top.js"></script>
<script>
	function saveEmailToStorage() {
		const quickEmail = document.getElementById("quickEmail");
		localStorage.setItem("user_email", quickEmail.value);
	}
</script>
<?php
include('footer.php');
?>