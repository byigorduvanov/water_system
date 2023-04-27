<?php
session_start();
$footer_class = 'hide-footer';
$burger_black = 'burger_black';
include('header.php');
require 'vendor/connect.php';
require 'vendor/csrf_token.php';
$errors = array();
if (!empty($_SESSION["id"])) {
	header("Location: room.php");
}

if (!isset($_SESSION['login_attempts'])) {
	$_SESSION['login_attempts'] = 0;
}

if (!isset($_SESSION['last_attempt_time'])) {
	$_SESSION['last_attempt_time'] = time();
}

$cooldown_seconds = 15 * 60;
$cooldown_expired = (time() - $_SESSION['last_attempt_time']) > $cooldown_seconds;

if (isset($_POST["submit"])) {
	if (!verify_csrf_token($_POST['csrf_token'])) {
		$errors['csrf'] = "Invalid CSRF token.";
	} elseif ($_SESSION['login_attempts'] >= 5 && !$cooldown_expired) {
		$remaining_time = $cooldown_seconds - (time() - $_SESSION['last_attempt_time']);
		$remaining_minutes = floor($remaining_time / 60);
		$remaining_seconds = $remaining_time % 60;
		$errors['login_attempts'] = "Занадто багато невдалих спроб входу. Будь ласка, спробуйте ще раз, через $remaining_minutes хвилин та $remaining_seconds секунди.";
	} else {
		if ($cooldown_expired) {
			$_SESSION['login_attempts'] = 0;
			$_SESSION['last_attempt_time'] = time();
		}
		global $conn;
		$email = $_POST["email"];
		$password = md5($_POST["password"]);
		$stmt = $conn->prepare("SELECT * FROM users WHERE  email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		if ($result->num_rows > 0) {
			if ($password == $row['password']) {
				$_SESSION["login"] = true;
				$_SESSION["id"] = $row["id"];
				$_SESSION['login_attempts'] = 0;
				ob_start();
				$redirect_url = "room.php";
			} else {
				$errors['password'] = "Неправильна пошта або пароль";
				$_SESSION['login_attempts']++;
				$_SESSION['last_attempt_time'] = time();
			}
		} else {
			$errors['email'] = "Такого користувача не існує";
			$_SESSION['login_attempts']++;
			$_SESSION['last_attempt_time'] = time();
		}
	}
}

$csrf_token = generate_csrf_token();
?>

<div class="container flex-grow-1">
	<div class="row">
		<div class="col">
			<div class="logo-mobile mt-0">
				<a href="index.php">
					<img src="img/logo.png" alt="main logo" class="">
				</a>
			</div>
			<div class="login-title">
				<p>Вхід у особистий кабінет</p>

			</div>

			<div class="row">
				<div class="col-lg-10 offset-lg-2">
					<form method="post" action="#" class="autorization-form col-xl-5 col-lg-6 col-md-8">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

						<?php
						if (count($errors) > 0) {
							?>
							<div class="alert alert-danger text-center ">
								<?php
								foreach ($errors as $showerror) {
									echo $showerror;
								}
								?>
							</div>
							<?php
						}
						?>

						<div class="form-group ">
							<label for="defaultRegisterFormEmail">
								Логін (Електронна пошта з якої було здійснено реєстрацію)
							</label>
							<input type="email" name="email"
								value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
								id="defaultRegisterFormEmail" class="form-control form__registr" placeholder="E-mail"
								required>
						</div>
						<div class="form-group pass-field ">
							<label for="exampleInputPassword1">Пароль</label>
							<input type="password" name="password" class="form-control form__registr "
								id="exampleInputPassword1" placeholder="Введіть пароль" required>
							<i class="fas fa-eye"></i>
						</div>
						<div class="form-group form-check">
							<input type="checkbox" class="form-check-input" id="exampleCheck1">
							<label class="form-check-label p-1" for="exampleCheck1">Запам’ятати мене</label>
						</div>

						<button type="submit" name="submit"
							class="btn btn-primary my_btn btn_register mt-5">Увійти</button>

						<a href="registr.php" class=" col-lg-4 d-flex  forgot-pasword">Зареєструватися?</a>


					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	const passwordInput = document.querySelector(".pass-field input");
	const eyeIcon = document.querySelector(".pass-field i");
	eyeIcon.addEventListener("click", () => {
		// Toggle the password input type between "password" and "text"
		passwordInput.type = passwordInput.type === "password" ? "text" : "password";

		// Update the eye icon class based on the password input type
		eyeIcon.className = `fas fa-eye${passwordInput.type === "password" ? "" : "-slash"}`;
	});
</script>
<?php
if (isset($redirect_url)) {
	header("Location: $redirect_url");
	ob_end_flush();
}
include('footer.php')
	?>