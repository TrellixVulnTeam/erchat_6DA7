<?php include "vendors/db.php" ?>
<?php include "vendors/header.php" ?>
<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $fname = trim($_POST['reg_fname']);
    $lname = trim($_POST['reg_lname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['reg_email']);
    $password = trim($_POST['reg_password']);
    $password2 = trim($_POST['reg_password2']);

    // $error = [
    //     'fname' => '',
    //     'lname' => '',
    //     'email' => '',
    //     'password' => '',
    //     'password2' => ''
    // ];

    if ($fname == '') {
        header("Location: register.php?error=قسمت نام را پر کنید");
        exit();
    }

    if ($lname == '') {
        header("Location: register.php?error=نام خانوادگی را پر کنید");
        exit();
    }

    if (email_exists($email)) {
        header("Location: register.php?error=ایمیل قبلا ثبت شده است");
        exit();
    }

    if ($password !== $password2) {
        header("Location: register.php?error=رمز عبور با تکرار آن یکسان نیست");
        exit();
    }

    if (strlen($password) < 8) {
        header("Location: register.php?error=رمز عبور باید حداقل 8 کاراکتر باشد");
        exit();
    }

    // foreach ($error as $key => $value) {
    //     if (empty($value)) {
    //         unset($error[$key]);
    //     }
    // }

    if (empty($error)) {
        register_user($fname, $lname, $username, $email, $password, $password2);
        login_user($username, $password);
    }
}

?>


<div class="welcome">
    <div class="form-container sign-in-form">
        <div class="form-box sign-in-box">
            <h2>عضویت</h2>
            <form method="post" action="register.php">

                <!-- <p class="register-error">ایمیل از قبل وجود دارد</p> -->
                <?php if (isset($_GET['error'])) { ?>
                    <p class="register-error"><?php echo $_GET['error']; ?></p>
                <?php } ?>

                <div class="field">
                    <span class="material-icons-sharp"> person </span>
                    <input name="reg_fname" type="text" placeholder="نام" required />
                </div>

                <div class="field">
                    <span class="material-icons-sharp"> person </span>
                    <input name="reg_lname" type="text" placeholder="نام خانوادگی" required />
                </div>

                <div class="field">
                    <span class="material-icons-sharp"> person_search </span>
                    <input name="username" type="text" placeholder="نام کاربری" required />
                </div>

                <div class="field">
                    <span class="material-icons-sharp"> alternate_email </span>
                    <input name="reg_email" type="email" placeholder="ایمیل" required />
                </div>

                <div class="field">
                    <span class="material-icons-sharp"> lock </span>
                    <input name="reg_password" class="password-input" type="password" placeholder="رمز عبور" required />
                </div>

                <div class="field">
                    <span class="material-icons-sharp"> password </span>
                    <input name="reg_password2" class="password-input" type="password" placeholder="تکرار رمز عبور" required />
                </div>

                <input name="register_button" class="submit-btn" type="submit" value="عضویت" />
                <div class="link-to-sign-in">
                    <a href="index.php">اگر از قبل حساب ایجاد کردید وارد شوید</a>
                </div>
            </form>
        </div>
    </div>

    <div class="welcome-text">
        <p class="welcome-text--paragraph">گفتگو و چت آنلاین با ERchat</p>
        <small class="welcome-text--small">با عضویت در سایت با دوستان خود صحبت کنید</small>
    </div>
</div>

<?php include "./vendors/footer.php" ?>
