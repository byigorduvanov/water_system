<?php
$footer_class = 'hide-footer';
$burger_black = 'burger_black';
include('header.php');

require 'vendor/connect.php';
$errors = array();
if (!empty($_SESSION["id"])) {
	header("Location: room.php");
}
if (isset($_POST["submit"])) {
	global $conn;
	$first_name = $_POST["first_name"];
	$last_name = $_POST["last_name"];
	$email = $_POST["email"];
	$password = md5($_POST["password"]);
	$password_confirm = md5($_POST["password_confirm"]);
	$duplicate = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
	if (mysqli_num_rows($duplicate) > 0) {
		$errors['email'] = "Пошта вже зайнята";
	} else {
		if ($password == $password_confirm) {
			$query = "INSERT INTO `users` (`id`, `first_name`, `last_name`, `middle_name`, `mobile_phone`, `home_phone`, `email`, `password`, `role`, `token`, `address`, `counter`) 
VALUES (NULL, '$first_name', '$last_name', NULL, NULL, NULL, '$email', '$password', NULL, NULL, NULL, NULL);";
			mysqli_query($conn, $query);
			//            echo "<script> alert('Registration Successful'); </script>";
			header("Location: autorization.php");
		} else {
			$errors['password'] = "Паролі не співпадають";
		}
	}
}

?>

<div class="container flex-grow-1">
	<div class="logo-mobile mt-0">
		<a href="index.php">
			<img src="img/logo.png" alt="main logo" class="">
		</a>
	</div>
	<form action="registr.php" method="post" oninput="validatePassword()" class="register-form">
		<div class="row">
			<div class="col-lg-11 offset-lg-1">
				<p class="h4 pt-5 ">Реєстрація</p>
				<?php
				if (count($errors) > 0) {
					?>
					<div class="alert alert-danger text-center col-4">
						<?php
						foreach ($errors as $showerror) {
							echo $showerror;
						}
						?>
					</div>
					<?php
				}
				?>

				<!-- E-mail -->
				<div class="form-row ">
					<div class="col-lg-5">
						<label for="email">E-mail</label>
						<input type="email" name="email" id="email" value="<?php if (isset($_POST['email']))
							echo $_POST['email']; ?>" class="form-control form__registr" placeholder="E-mail" required>
					</div>
				</div>
				<!-- name -->
				<div class="form-row ">
					<div class="col-lg-5">
						<label for="defaultRegisterFormLastName">Введіть ім’я</label>
						<input type="text" name="first_name" value="<?php if (isset($_POST['first_name']))
							echo $_POST['first_name']; ?>" id="defaultRegisterFormLastName" class="form-control form__registr"
							placeholder="Введіть ім’я" required value="">
					</div>
					<div class="col-lg-5 offset-lg-1">
						<label for="defaultRegisterFormFirstName">Введіть прізвище</label>
						<input type="text" name="last_name" value="<?php if (isset($_POST['last_name']))
							echo $_POST['last_name']; ?>" id="defaultRegisterFormFirstName" class="form-control form__registr"
							placeholder="Введіть прізвище " required value="">
					</div>
				</div>
				<!-- password -->
				<div class="form-row ">
					<div class="col-lg-5 ">
						<label for="exampleInputPassword1">Пароль</label>
						<input type="password" name="password" id="newPass" class="form-control form__registr"
							placeholder="Введіть пароль" required value="">
					</div>
					<div class="col-lg-5 offset-lg-1">
						<label for="conformPass">Підтвердження паролю</label>
						<input type="password" name="password_confirm" id="conformPass"
							class="form-control form__registr form__registr__last__child "
							placeholder="Підтвердження паролю" required value="">
					</div>
				</div>

				<!-- Newsletter -->
				<div class="custom-control custom-checkbox newsletter text-center">
					<input type="checkbox" class="custom-control-input" id="defaultRegisterFormNewsletter" required
						value="">
					<label class="custom-control-label p-1" for="defaultRegisterFormNewsletter">
						Я приймаю умови Договору
					</label>
				</div>

				<!-- Sign up button -->

				<button name="submit" class="btn btn-primary my_btn btn_register mt-5 mb-4 d-block m_0_auto"
					type="submit">
					Зареєструватися
				</button>


			</div>


		</div>
	</form>

</div>
<script>
	document.addEventListener("DOMContentLoaded", function () {
		const emailField = document.getElementById("email");
		const storedEmail = localStorage.getItem("user_email");

		if (storedEmail) {
			emailField.value = storedEmail;
			localStorage.removeItem("user_email");
		}
	});
	function validatePassword() {
		if (newPass.value != conformPass.value) {
			conformPass.setCustomValidity('Паролі не співпадають.');
		} else {
			conformPass.setCustomValidity('');
		}
	}
</script>
<?php
include('footer.php')
	?>