<?php
$footer_class = 'hide-footer';
$burger_black = 'burger_black';
include('header.php');

require 'vendor/connect.php';
require 'vendor/csrf_token.php';
$errors = array();
if (!empty($_SESSION["id"])) {
	header("Location: room.php");
}

if (!isset($_SESSION["reg_attempts"])) {
	$_SESSION["reg_attempts"] = 0;
}

if (isset($_POST["submit"])) {
	// Check if the registration attempts limit is exceeded
	if ($_SESSION["reg_attempts"] >= 5) {
		if (!isset($_SESSION["block_time"]) || time() - $_SESSION["block_time"] > 900) { // 900 seconds (15 minutes) cooldown
			// Reset the attempts counter and block time
			$_SESSION["reg_attempts"] = 0;
			unset($_SESSION["block_time"]);
		} else {
			$errors['reg_attempts'] = "Ви перевищили максимальну кількість спроб реєстрації. Будь ласка, спробуйте знову через " . (900 - (time() - $_SESSION["block_time"])) . " секунд.";
		}
	} else {
		// Check CSRF token
		if (isset($_POST["csrf_token"]) && verify_csrf_token($_POST["csrf_token"])) {
			global $conn;
			$first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
			$last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
			$email = mysqli_real_escape_string($conn, $_POST["email"]);
			$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
			$password_confirm = $_POST["password_confirm"];
			$duplicate = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
			if (mysqli_num_rows($duplicate) > 0) {
				$errors['email'] = "Email is already taken";
			} else {
				if (password_verify($password_confirm, $password)) {
					$query = "INSERT INTO `users` (`id`, `first_name`, `last_name`, `middle_name`, `mobile_phone`, `home_phone`, `email`, `password`, `role`, `token`, `address`, `counter`) 
					VALUES (NULL, '$first_name', '$last_name', NULL, NULL, NULL, '$email', '$password', NULL, NULL, NULL, NULL);";
					mysqli_query($conn, $query);
					header("Location: autorization.php");
				} else {
					$errors['password'] = "Паролі не збігаються sql";
				}
			}
		} else {
			$errors['csrf'] = "Недійсний токен CSRF.";
		}
		$_SESSION["reg_attempts"]++;
	}
}
?>
<div class="container flex-grow-1">
	<div class="logo-mobile mt-0">
		<a href="index.php">
			<img src="img/logo.png" alt="main logo" class="">
		</a>
	</div>
	<form action="registr.php" method="post" onsubmit="return validateForm()" class="register-form">
		<input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
		<div class="row">
			<div class="col-lg-11 offset-lg-1">
				<p class="h4 pt-5">Реєстрація</p>
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
							echo $_POST['email']; ?>" class="form-control form__registr" placeholder="Введіть email " required>
					</div>
				</div>
				<!-- name -->
				<div class="form-row ">
					<div class="col-lg-5">
						<label for="defaultRegisterFormLastName"> Ваше прізвище</label>
						<input type="text" name="first_name" value="<?php if (isset($_POST['first_name']))
							echo $_POST['first_name']; ?>" id="defaultRegisterFormLastName" class="form-control form__registr"
							placeholder="Введіть прізвище ">
					</div>
					<div class="col-lg-5 offset-lg-1">
						<label for="defaultRegisterFormFirstName">Ваше ім’я</label>
						<input type="text" name="last_name" value="<?php if (isset($_POST['last_name']))
							echo $_POST['last_name']; ?>" id="defaultRegisterFormFirstName" class="form-control form__registr"
							placeholder="Введіть ім’я">
					</div>
				</div>
				<!-- password -->
				<div class="form-row ">
					<div class="col-lg-5">
						<label for="newPass">Пароль</label>
						<input type="password" name="password" id="newPass" class="form-control form__registr"
							placeholder="Введіть пароль" required value="">
						<div class="input-group-append">
							<span class="input-group-text" id="togglePassword" style="cursor: pointer;">👁️</span>
						</div>
						<div class="password-strength mt-2">
							<ul id="password-requirements" class="list-unstyled">
								<li id="length" class="text-danger">Мінімум 8 символів</li>
								<li id="lowercase" class="text-danger">Хоча б одна маленька літера</li>
								<li id="uppercase" class="text-danger">Хоча б одна велика літера</li>
								<li id="number" class="text-danger">Хоча б одна цифра</li>
							</ul>
						</div>
					</div>
					<div class="col-lg-5 offset-lg-1">
						<label for="conformPass">Підтвердження паролю</label>
						<input type="password" name="password_confirm" id="conformPass"
							class="form-control form__registr form__registr__last__child "
							placeholder="Підтвердіть пароль" required value="">
						<div class="input-group-append">
							<span class="input-group-text" id="toggleConfirmPassword"
								style="cursor: pointer;">👁️</span>
						</div>
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
				<div class="g-recaptcha" data-sitekey="6Ldbgq8lAAAAAH3aHy3rxI9ckqNBx02PJX0GaIsN"></div>

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

		const password = document.getElementById("newPass");
		const passwordConfirm = document.getElementById("conformPass");
		const togglePassword = document.getElementById("togglePassword");
		const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

		password.addEventListener("input", updatePasswordStrength);
		togglePassword.addEventListener("click", togglePasswordVisibility);
		toggleConfirmPassword.addEventListener("click", toggleConfirmPasswordVisibility);
	});

	function updatePasswordStrength() {
		const password = document.getElementById("newPass");
		const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/;
		const length = document.getElementById("length");
		const lowercase = document.getElementById("lowercase");
		const uppercase = document.getElementById("uppercase");
		const number = document.getElementById("number");

		length.classList.toggle("text-danger", password.value.length < 8);
		lowercase.classList.toggle("text-danger", !/[a-z]/.test(password.value));
		uppercase.classList.toggle("text-danger", !/[A-Z]/.test(password.value));
		number.classList.toggle("text-danger", !/\d/.test(password.value));
	}

	function togglePasswordVisibility() {
		const password = document.getElementById("newPass");
		password.type = password.type === "password" ? "text" : "password";
	}

	function toggleConfirmPasswordVisibility() {
		const passwordConfirm = document.getElementById("conformPass");
		passwordConfirm.type = passwordConfirm.type === "password" ? "text" : "password";
	}

	function validateForm() {
		// Check password requirements
		const password = document.getElementById("newPass");
		const passwordConfirm = document.getElementById("conformPass");
		const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/;
		let errorMessages = [];

		if (!passwordRegex.test(password.value)) {
			errorMessages.push("Пароль повинен містити щонайменше 8 символів, одну велику літеру, одну малу літеру та одну цифру.");
		}

		// Check reCAPTCHA
		if (grecaptcha.getResponse() === "") {
			errorMessages.push("Будь ласка, заповніть reCAPTCHA.");
		}

		// Check password confirmation
		if (password.value !== passwordConfirm.value) {
			errorMessages.push("Паролі не збігаються.");
		}

		if (errorMessages.length > 0) {
			alert(errorMessages.join("\n"));
			return false;
		}

		return true;
	}


</script>
<?php
include('footer.php')
	?>