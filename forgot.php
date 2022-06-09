<?php use PHPMailer\PHPMailer\PHPMailer; ?>
<?php include "vendors/db.php" ?>
<?php include "vendors/header.php" ?>
<?php ch_title("بازیابی کلمه عبور") ?>
<?php
require "vendor/autoload.php";
require "vendor/phpmailer/phpmailer/src/PHPMailer.php";
require "vendor/phpmailer/phpmailer/src/SMTP.php";
require "vendor/phpmailer/phpmailer/src/Exception.php";
?>
<?php
if (!isset($_GET['forgot'])) {
    redirect('index.php');
}

if (ifItIsMethod('post')) {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $length = 50;
        $token = bin2hex(openssl_random_pseudo_bytes($length));

        if (email_exists($email)) {
            if ($stmt = mysqli_prepare($connection, "UPDATE users SET token = '{$token}' WHERE email = ?")) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                $mail = new PHPMailer();

                $mail->isSMTP();
                $mail->Host = Config::SMTP_HOST;
                $mail->Username = Config::SMTP_USER;
                $mail->Password = Config::SMTP_PASSWORD;
                $mail->Port = Config::SMTP_PORT;

                $mail->SMTPSecure = 'tls';
                $mail->SMTPAuth = true;
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->setFrom('pclearn4@gmail.com', 'erfan');
                $mail->addAddress($email);

                $mail->Subject = 'بازیابی رمزعبور';
                $mail->Body = '<p>برای بازیابی رمز عبور روی لینک کلیک  کنید <a href="http://localhost/erchat/reset.php?email=' . $email . '&token=' . $token . '">
                                    http://localhost:8080/cms/reset.php?email=' . $email . '&token=' . $token . '
                               </a></p>';

                if ($mail->send()) {
                    $emailSend = true;
                } else {
                    echo "ایمیل ارسال نشد";
                }
            }
        }
    }
}
?>


<div class="container cont-style">
    <div class="form-gap"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center text-c--style">
                            <?php if (!isset($emailSend)): ?>
                                <h3><i class="fa fa-lock fa-4x"></i></h3>
                                <h2 class="text-center">رمزعبور را فراموش کرده اید؟</h2>
                                <p>برای بازیابی ایمیل خود را در این قسمت وارد کنید</p>

                                <div class="panel-body">

                                    <form id="register-form" role="form" autocomplete="off" class="form" method="post">
                                        <div class="form-group">
                                            <div class="input-group ig-style">
                                            <span class="input-group-addon"><i
                                                        class="glyphicon glyphicon-envelope color-blue"></i></span>
                                                <input id="email" name="email" placeholder="آدرس ایمیل"
                                                       class="form-control" type="email">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <input name="recover-submit" class="btn btn-lg btn-primary btn-block reset-pass--btn"
                                                   value="بازیابی رمز عبور" type="submit">
                                        </div>

                                        <input type="hidden" class="hide" name="token" id="token" value="">
                                    </form>

                                </div><!-- Body-->

                            <?php else: ?>
                                <h4>ایمیل خود را چک کنید</h4>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "vendors/footer.php"; ?>
</div>
