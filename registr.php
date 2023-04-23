<?php
$footer_class = 'hide-footer';
$burger_black = 'burger_black';
include('header.php');

require 'vendor/connect.php';
require 'vendor/csrf_token.php'; // Include the CSRF token functions
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
		$errors['reg_attempts'] = "You have exceeded the maximum number of registration attempts.";
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
					$errors['password'] = "Passwords do not match";
				}
			}
		} else {
			$errors['csrf'] = "Invalid CSRF token.";
		}
		$_SESSION["reg_attempts"]++;
	}
}
?>
<!-- Include the reCAPTCHA script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

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
				<p class="h4 pt-5">Registration</p>
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
						<label for="defaultRegisterFormLastName">Enter your first name</label>
						<input type="text" name="first_name" value="<?php if (isset($_POST['first_name']))
							echo $_POST['first_name']; ?>" id="defaultRegisterFormLastName" class="form-control form__registr"
							placeholder="Enter your first name">
					</div>
					<div class="col-lg-5 offset-lg-1">
						<label for="defaultRegisterFormFirstName">Enter your last name</label>
						<input type="text" name="last_name" value="<?php if (isset($_POST['last_name']))
							echo $_POST['last_name']; ?>" id="defaultRegisterFormFirstName" class="form-control form__registr"
							placeholder="Enter your last name">
					</div>
				</div>
				<!-- password -->
				<div class="form-row ">
					<div class="col-lg-5 ">
						<label for="newPass">Password</label>
						<input type="password" name="password" id="newPass" class="form-control form__registr"
							placeholder="Enter your password" required value="">
					</div>
					<div class="col-lg-5 offset-lg-1">
						<label for="conformPass">Password confirmation</label>
						<input type="password" name="password_confirm" id="conformPass"
							class="form-control form__registr form__registr__last__child "
							placeholder="Password confirmation" required value="">
					</div>
				</div>

				<!-- Newsletter -->
				<div class="custom-control custom-checkbox newsletter text-center">
					<input type="checkbox" class="custom-control-input" id="defaultRegisterFormNewsletter" required
						value="">
					<label class="custom-control-label p-1" for="defaultRegisterFormNewsletter">
						I accept the terms of the Agreement
					</label>
				</div>

				<!-- Sign up button -->

				<button name="submit" class="btn btn-primary my_btn btn_register mt-5 mb-4 d-block m_0_auto"
					type="submit">
					Register
				</button>
			</div>
		</div>
		<div class="g-recaptcha" data-sitekey="6Ldbgq8lAAAAAH3aHy3rxI9ckqNBx02PJX0GaIsN"></div>


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
		const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

		if (newPass.value != conformPass.value) {
			conformPass.setCustomValidity('Passwords do not match.');
		} else if (!passwordRegex.test(newPass.value)) {
			newPass.setCustomValidity('Password must contain at least 8 characters, including uppercase and lowercase letters, numbers, and special characters.');
		} else {
			conformPass.setCustomValidity('');
			newPass.setCustomValidity('');
		}
	}

	function validateForm() {
		// Check password requirements
		const password = document.getElementById("newPass");
		const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/;
		if (!passwordRegex.test(password.value)) {
			alert("Password must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.");
			return false;
		}

		// Check reCAPTCHA
		if (grecaptcha.getResponse() === "") {
			alert("Please complete the reCAPTCHA.");
			return false;
		}

		// Check password confirmation
		if (newPass.value != conformPass.value) {
			conformPass.setCustomValidity('Passwords do not match.');
			return false;
		} else {
			conformPass.setCustomValidity('');
		}
		return true;
	}
</script>
<?php
include('footer.php')
	?>