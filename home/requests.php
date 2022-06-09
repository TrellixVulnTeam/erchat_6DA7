<?php include "vendors/home_header.php"; ?>
<?php include "classes/Post.php"; ?>
<?php include "navbar.php"; ?>
<?php
if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $user_details_query = mysqli_query($connection, "SELECT * FROM users WHERE username = '$username'");
    $user_array = mysqli_fetch_array($user_details_query);
}
?>

<div class="requests">
    <h4>درخواست ها</h4>
    <?php
    $query = mysqli_query($connection, "SELECT * FROM friend_requests WHERE user_to = '$userLoggedIn'");
    if (mysqli_num_rows($query) == 0) {
        echo "<p class='alert alert-info info-style' role='alert'>فعلا درخواستی برای شما ارسال نشده</p>";
    } else {
        while ($row = mysqli_fetch_array($query)) {
            $user_from = $row['user_from'];
            $user_from_obj = new User($connection, $user_from);

            echo "<div class='requests_card'>";
            echo "<img src=" . $user_from_obj->getProfilePic() . ">";
            echo "<p>شما یک درخواست از   <a href='$user_from'>" . $user_from_obj->getUsername() . "</a> دارید</p>";
            echo "</div>";

            $user_from_friend_array = $user_from_obj->getFriendArray();

            if (isset($_POST['accept_request' . $user_from])) {
                $accept = $_POST['accept_request'];

                $add_friend_query = mysqli_query($connection, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username = '$userLoggedIn'");
                $add_friend_query = mysqli_query($connection, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username = '$user_from'");

                $delete_query = mysqli_query($connection, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");

                echo "<p>این کاربر به دوستان شما اضافه شد</p>";
                header("Location: requests.php");
            }

            if (isset($_POST['ignore_request' . $user_from])) {
                $delete_query = mysqli_query($connection, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
                echo "<p>درخواست کاربر توسط شما رد شد</p>";
                header("Location: requests.php");
            }
    ?>
            <form class="acig-btn" action="requests.php" method="POST">
                <input type="submit" name="accept_request<?php echo $user_from; ?>" class="accept_btn" value="پذیرفتن">
                <input type="submit" name="ignore_request<?php echo $user_from; ?>" class="ignore_btn" value="رد کردن">
            </form>
    <?php
        }
    }
    ?>
</div>