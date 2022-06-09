<?php include "vendors/home_header.php"; ?>
<?php include "classes/User.php"; ?>
<?php include "classes/Post.php"; ?>
<?php include "classes/Notification.php"; ?>
<?php
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
}

$get_likes = mysqli_query($connection, "SELECT likes, added_by FROM posts WHERE id = '$post_id'");
$row = mysqli_fetch_array($get_likes);
$total_likes = $row['likes'];
$user_liked = $row['added_by'];

$user_details_query = mysqli_query($connection, "SELECT * FROM users WHERE username = '$user_liked'");
$row = mysqli_fetch_array($user_details_query);
$total_user_likes = $row['num_likes'];

// like button
if (isset($_POST['like_button'])) {
    $total_likes++;
    $query = mysqli_query($connection, "UPDATE posts SET likes='$total_likes' WHERE id = '$post_id'");
    $total_user_likes++;
    $user_likes = mysqli_query($connection, "UPDATE users SET num_likes = '$total_user_likes' WHERE username = '$user_liked'");
    $insert_user = mysqli_query($connection, "INSERT INTO likes VALUES('', '$userLoggedIn', '$post_id')");

    // Insert Notification
    if($user_liked != $userLoggedIn) {
        $notification = new Notification($connection, $userLoggedIn);
        $notification->insertNotification($post_id, $user_liked, "like");
    }
}

// unlike button
if (isset($_POST['unlike_button'])) {
    $total_likes--;
    $query = mysqli_query($connection, "UPDATE posts SET likes='$total_likes' WHERE id = '$post_id'");
    $total_user_likes--;
    $user_likes = mysqli_query($connection, "UPDATE users SET num_likes = '$total_user_likes' WHERE username = '$user_liked'");
    $insert_user = mysqli_query($connection, "DELETE FROM likes WHERE username = '$userLoggedIn' AND post_id = '$post_id'");
}
// Check for previous likes
$check_query = mysqli_query($connection, "SELECT * FROM likes WHERE username = '$userLoggedIn' AND post_id = '$post_id'");
$num_rows = mysqli_num_rows($check_query);

if ($num_rows > 0) {
    echo '<form class="like_form--style" action="like.php?post_id=' . $post_id . '" method="POST">
        <button class="material-icons-sharp like_button" type="submit" name="unlike_button">
            thumb_down
        </button>
        <div class="like_value">
            ' . $total_likes . '
        </div>
    </form>';
} else {
    echo '<form class="like_form--style" action="like.php?post_id=' . $post_id . '" method="POST">
    <button class="material-icons-sharp like_button" type="submit" name="like_button">
        thumb_up
</button >
    <div class="like_value" >
        ' . $total_likes . '
    </div >
</form > ';
}
?>