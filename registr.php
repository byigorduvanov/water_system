<?php
include('header.php');

require 'vendor/connect.php';
$errors = array();
if(!empty($_SESSION["id"])){
    header("Location: room.php");
}
if(isset($_POST["submit"])){
    global $conn;
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $password = md5($_POST["password"]);
    $password_confirm = md5($_POST["password_confirm"]);
    $duplicate = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if(mysqli_num_rows($duplicate) > 0){
        $errors['email'] = "Пошта вже зайнята";
    }
    else{
        if($password == $password_confirm){
         //   $query = "INSERT INTO users VALUES('','$first_name','$last_name','','','','$email','$password','','')";
            $query = "INSERT INTO `users` (`id`, `first_name`, `last_name`, `middle_name`, `mobile_phone`, `home_phone`, `email`, `password`, `role`, `token`, `address`, `counter`) 
VALUES (NULL, '$first_name', '$last_name', NULL, NULL, NULL, '$email', '$password', NULL, NULL, NULL, NULL);";
            mysqli_query($conn, $query);
//            echo "<script> alert('Registration Successful'); </script>";
            header("Location: autorization.php");
        }
        else{
            $errors['password'] = "Паролі не співпадають";
        }
    }
}

?>
<div class="container">
    <div class="logo-mobile">
        <a href="index.php">
            <img src="img/logo.png" alt="main logo" class="">
        </a>
    </div>
    <form action="registr.php" method="post" oninput="validatePassword()" class="register-form" >
    <div class="row">
        <div class="col">
        <p class="h4 pt-5 pb-4">Реєстрація</p>
            <?php
            if(count($errors) > 0){
                ?>
                <div class="alert alert-danger text-center col-4">
                    <?php
                    foreach($errors as $showerror){
                        echo $showerror;
                    }
                    ?>
                </div>
                <?php
            }
            ?>

        <!-- E-mail -->
        <label for="">E-mail</label>

        <input type="email" name="email" id="email" value="<?php if (isset($_POST['email'])) echo $_POST['email'];?>" class="form-control form__registr mb-4 col-lg-4" placeholder="E-mail" required >

        <div class="form-row mb-4">
            <div class="col-lg-4">
                <!-- First name -->
                <label for="">Введіть ім’я</label>

                <input type="text" name="first_name" value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name'];?>" id="defaultRegisterFormLastName" class="form-control form__registr " placeholder="Введіть ім’я" required value="">

            </div>
            <div class="col-lg-4">
                <!-- Last name -->
                <label for="">Введіть прізвище</label>

                <input type="text" name="last_name" value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name'];?>" id="defaultRegisterFormFirstName" class="form-control form__registr " placeholder="Введіть прізвище " required value="">

            </div>
        </div>
        <!-- Password -->
        <div class="form-row mb-4">
            <div class="col-lg-4 ">
                <label for="exampleInputPassword1">Пароль</label>
                <input type="password" name="password" id="newPass" class="form-control form__registr " id="exampleInputPassword1" placeholder="Введіть пароль" required value="">
            </div>
            <div class="col-lg-4">
                <label for="exampleInputPassword1">Підтвердження паролю</label>

                <input type="password" name="password_confirm" id="conformPass" class="form-control form__registr"  placeholder="Підтвердження паролю" required value="">
            </div>
        </div>

        <!-- Newsletter -->
        <div class="custom-control custom-checkbox newsletter">
            <input type="checkbox" class="custom-control-input" id="defaultRegisterFormNewsletter" required value="">
            <label class="custom-control-label p-1" for="defaultRegisterFormNewsletter">Я приймаю умови Договору</label>
        </div>

        <!-- Sign up button -->

        <button name="submit" class="btn btn-primary my_btn btn_register mt-5" type="submit">Увійти</button>


    </div>


    </div>
    </form>

</div>
<script>
    function validatePassword(){
        if(newPass.value != conformPass.value) {
            conformPass.setCustomValidity('Паролі не співпадають.');
        } else {
            conformPass.setCustomValidity('');
        }
    }
</script>
<?php
include('footer.php')
?>

