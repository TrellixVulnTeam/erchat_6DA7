<?php
if(isset($_POST['update_details'])) {
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];

	$email_check = mysqli_query($connection, "SELECT * FROM users WHERE email='$email'");
	$row = mysqli_fetch_array($email_check);
	$matched_user = $row['username'];

	if(empty($matched_user) || $matched_user == $userLoggedIn) {
		$message = "<p class='alert alert-primary up-alert'>اطلاعات شما به روز رسانی شد</p>";
		$query = mysqli_query($connection, "UPDATE users SET first_name = '$first_name', last_name = '$last_name', email = '$email' WHERE username = '$userLoggedIn'");
	} else
		$message = "ایمیل قبلا استفاده شده";
}
else
	$message = "";

// Change Password:
if(isset($_POST['update_password'])) {
	$old_password = strip_tags($_POST['old_password']);
	$old_password = mysqli_real_escape_string($connection, $old_password);
	$new_password_1 = strip_tags($_POST['new_password_1']);
	$new_password_2 = strip_tags($_POST['new_password_2']);

	$password_query = mysqli_query($connection, "SELECT password FROM users WHERE username = '$userLoggedIn'");
	$row = mysqli_fetch_array($password_query);
	$db_password = $row['password'];
	

	if(password_verify($old_password, $db_password)) {
		if($new_password_1 == $new_password_2) {
			if(strlen($new_password_1) < 8) {
				$password_message = "<p class='alert alert-primary up-alert'>رمز عبور کوتاه است</p>";
			}	else	{
				$new_password_hash = password_hash($new_password_1, PASSWORD_BCRYPT, array('cost'=>12));
				$password_query = mysqli_query($connection, "UPDATE users SET password = '$new_password_hash' WHERE username = '$userLoggedIn'");
				$password_message = "<p class='alert alert-primary up-alert'>رمز عبور شما تغییر کرد</p>";
			}
		}	else	{
			$password_message = "<p class='alert alert-primary up-alert'>رمز عبور یکسان نیست</p>";
		}
	}	else	{
		$password_message = "<p class='alert alert-primary up-alert'>رمز عبور اشتباه است</p>";
	}
}	else	{
	$password_message = "";
}

// Close Account:
if(isset($_POST['close_account'])) {
	header("Location: close_account.php");
}

?>