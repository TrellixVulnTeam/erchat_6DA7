<?php include "vendors/db.php" ?>
<?php include "vendors/header.php" ?>
<?php

checkIfIsLoggedInAndRedirect('/erchat/home');
if (ifItIsMethod('post')) {
  if (isset($_POST['username'])  && isset($_POST['password'])) {
    login_user($_POST['username'], $_POST['password']);
  } else {
    redirect('/erchat/index.php');
  }
}

?>

<div class="welcome">
  <div class="form-container sign-in-form">
    <div class="form-box sign-in-box">
      <h2>ورود</h2>
      <form method="post" action="index.php">
        <div class="field">
          <span class="material-icons-sharp"> person_search </span>
          <input name="username" type="text" placeholder="نام کاربری" required />
        </div>

        <div class="field">
          <span class="material-icons-sharp"> lock </span>
          <input name="password" class="password-input" type="password" placeholder="رمز عبور" required />
          <div class="eye-btn">
            <span class="material-icons-sharp"> visibility_off </span>
          </div>
        </div>
        <div class="forgot-link">
            <a href="forgot.php?forgot=<?php echo uniqid(true) ?>">فراموشی رمز عبور؟</a>
        </div>
        <input name="login_button" class="submit-btn" type="submit" value="ورود" />
        <div class="link-to-sign-up">
          <a href="register.php">اگر قبلا عضو نشده اید اکانت جدید بسازید</a>
        </div>
      </form>
    </div>
  </div>

  <div class="welcome-text">
    <p class="welcome-text--paragraph">گفتگو و چت آنلاین با ERchat</p>
    <!-- <small class="welcome-text--small">با عضویت در سایت با دوستان خود صحبت کنید</small> -->
  </div>
</div>

<?php include "./vendors/footer.php" ?>