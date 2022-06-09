<?php 
include "vendors/home_header.php";
ch_title("بستن حساب")
?>
<?php include "classes/Post.php"; ?>
<?php include "navbar.php"; ?>
<?php
if(isset($_POST['cancel'])) {
    header("Location: settings.php");
}

if(isset($_POST['close_account'])) {
    $close_query = mysqli_query($connection, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
    session_destroy();
    header("Location: ../register.php");
}
?>

<div class="close_cont">
    <h4>بستن حساب</h4>
    <p>آیا از بستن حساب خود اطمینان دارید؟<br> با بستن حساب تمام فعالیت شما پاک میشود اما امکان ورود مجدد به حساب خود را دارید.</p>

    <form action="close_account.php" method="POST">
        <input class="btn btn-danger" type="submit" name="close_account" value="بستن حساب">
        <input class="btn btn-secondary" type="submit" name="cancel" value="خیر">
    </form>
</div>