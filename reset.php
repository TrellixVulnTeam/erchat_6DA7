<?php include "vendors/db.php"; ?>
<?php include "vendors/header.php"; ?>
<?php ch_title("بازیابی"); ?>
<?php
if(!isset($_GET['email'])) {
    redirect('index.php');
}

if($stmt = mysqli_prepare($connection, "SELECT username, email, token FROM users WHERE token = ?")) {
    mysqli_stmt_bind_param($stmt, "s", $_GET['token']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username, $email, $token);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if(isset($_POST['password']) && isset($_POST['confirmPassword'])) {
        if($_POST['password'] === $_POST['confirmPassword']) {
            $password = $_POST['password'];
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, array('cost', 12));

            if($stmt = mysqli_prepare($connection, "UPDATE users SET token = '', password = '{$hashedPassword}' WHERE email = ?")) {
                mysqli_stmt_bind_param($stmt, "s", $_GET['email']);
                mysqli_stmt_execute($stmt);

                if(mysqli_stmt_affected_rows($stmt) >= 1) {
                    redirect('/erchat/index.php');
                }
            }
        }
    }
}
?>

<div class="container">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center">
                            <h3><i class="fa fa-lock fa-4x"></i></h3>
                            <h2 class="text-center">بازیابی رمز عبور</h2>
                            <p>رمز جدید خود را تعیین کنید</p>
                            <div class="panel-body">

                                <form id="register-form" role="form" autocomplete="off" class="form" method="post">

                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-user color-blue"></i></span>
                                            <input id="password" name="password" placeholder="رمز عبور" class="form-control"  type="password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-ok color-blue"></i></span>
                                            <input id="confirmPassword" name="confirmPassword" placeholder="تکرار رمز" class="form-control"  type="password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <input name="resetPassword" class="btn btn-lg btn-primary btn-block" value="انجام شد" type="submit">
                                    </div>

                                    <input type="hidden" class="hide" name="token" id="token" value="">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <?php include "vendors/footer.php"; ?>
</div>
