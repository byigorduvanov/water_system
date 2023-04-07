<?php
include('header.php');
require 'vendor/connect.php';
$errors = array();
if(!empty($_SESSION["id"])){
    header("Location: room.php");
}
if(isset($_POST["submit"])){
    global $conn;
    $email = $_POST["email"];
    $password = md5($_POST["password"]);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE  email = '$email'");
    $row = mysqli_fetch_assoc($result);
    if(mysqli_num_rows($result) > 0){
        if($password == $row['password']){
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["id"];
            header("Location: room.php");
        }
        else{

            $errors['password'] = "Неправильна пошта або пароль";
        }
    }
    else{
        $errors['email'] = "Такого користувача не існує";
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col">
            <div class="logo-mobile">
                <a href="index.php">
                    <img src="img/logo.png" alt="main logo" class="">
                </a>
            </div>
            <div class="login-title">
                <p>Вхід у особистий кабінет</p>

            </div>

            <form  method="post" action="#" class="autorization-form col-xl-4 col-lg-6 col-md-8">

                <?php
                if(count($errors) > 0){
                    ?>
                    <div class="alert alert-danger text-center ">
                        <?php
                        foreach($errors as $showerror){
                            echo $showerror;
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>

                <div class="form-group ">
                    <label for="exampleInputEmail1">Логін (Електронна пошта з якої було
                        здійснено реєстрацію)</label>
                    <input type="email" name="email" value="" id="defaultRegisterFormEmail" class="form-control form__registr" placeholder="E-mail" >
                </div>
                <div class="form-group ">
                    <label for="exampleInputPassword1">Пароль</label>
                    <input type="password" name="password" class="form-control form__registr " id="exampleInputPassword1" placeholder="Введіть пароль">
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label p-1" for="exampleCheck1">Запам’ятати мене</label>
                </div>

                <button type="submit" name="submit"  class="btn btn-primary my_btn btn_register mt-5">Увійти</button>

                <a href="registr.php" class=" col-lg-4 d-flex  forgot-pasword">Зареєструватися?</a>


            </form>
        </div>
    </div>
</div>
<?php
include ('footer.php')
?>