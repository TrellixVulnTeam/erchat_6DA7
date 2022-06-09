<?php
include "vendors/home_header.php";
ch_title("تنطیمات");
?>
<?php include "classes/Post.php"; ?>
<?php include "navbar.php"; ?>
<?php include "form_handlers/settings_handler.php"; ?>

<!-- Messages -->
<?php echo $message; ?>
<?php echo $password_message; ?>

<div class="setting_con">
    <h4 class="card-title">صفحه تنطیمات</h4>
    <div class="setting_con--first">
        <div class="setting_con--chimg">
            <?php
            echo '<img src="' . $user['profile_pic'] . '">';
            ?>
            <a class="btn btn-secondary" href="upload.php">تغییر عکس پروفایل</a>
        </div>
        <div class="setting_con--personDet">
            <p>اصلاعات جدید را وارد کرده و دکمه <strong>ثبت</strong> را فشار دهید</p>

            <?php
            $user_data_query = mysqli_query($connection, "SELECT first_name, last_name, email FROM users WHERE username = '$userLoggedIn'");
            $row = mysqli_fetch_array($user_data_query);

            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $email = $row['email'];
            ?>

            <form action="settings.php" method="POST">
                <div class="person_det">
                    <label for="sName">نام</label>
                    <input class="form-control form-control-sm" id="sName" type="text" name="first_name" value="<?php echo $first_name; ?>">
                </div>

                <div class="person_det">
                    <label for="sLastName" class="lname-label">نام خانوادگی</label>
                    <input class="form-control form-control-sm" id="sLastName" type="text" name="last_name" value="<?php echo $last_name; ?>">
                </div>

                <div class="person_det">
                    <label for="sEmail">ایمیل</label>
                    <input class="form-control form-control-sm" id="sEmail" type="email" name="email" value="<?php echo $email; ?>">
                </div>



                <input class="btn btn-light save-btn--ch" type="submit" name="update_details" value="ثبت">
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="changePass-con">
        <h4>تغییر رمز عبور</h4>
        <form class="changePass-con--passForm" action="settings.php" method="POST">
            <input class="form-control" type="password" name="old_password" placeholder="رمز عبور قدیم">
            <input class="form-control" type="password" name="new_password_1" placeholder="رمز عبور جدید">
            <input class="form-control" type="password" name="new_password_2" placeholder="تکرار رمز عبور جدید">

            <input class="btn btn-light" type="submit" name="update_password" value="ذخیره رمز عبور">
        </form>
    </div>

    <!-- Close Account -->
    <div class="closeAc-con">
        <h4>بستن حساب کاربری</h4>
        <form action="settings.php" method="POST">
            <input class="btn btn-danger" type="submit" name="close_account" value="بستن حساب">
        </form>
    </div>
</div>