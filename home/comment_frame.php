<?php
// require '../vendors/db.php';
include("vendors/home_header.php");
include("classes/User.php");
include("classes/Post.php");
include "classes/Notification.php";

// if (isset($_SESSION['username'])) {
//     $userLoggedIn = $_SESSION['username'];
//     $user_details_query = mysqli_query($connection, "SELECT * FROM users WHERE username='$userLoggedIn'");
//     $user = mysqli_fetch_array($user_details_query);
// }
?>

<script>
    function toggle() {
        var element = document.getElementById("comment_section");

        if (element.style.display == "block")
            element.style.display = "none";
        else
            element.style.display = "block";
    }
</script>

<?php
// Get id of post:
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
}

$user_query = mysqli_query($connection, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
$row = mysqli_fetch_array($user_query);

$posted_to = $row['added_by'];
$user_to = $row['user_to'];

if (isset($_POST['postComment' . $post_id])) {
    $post_body      = $_POST['post_body'];
    $post_body      = mysqli_escape_string($connection, $post_body);
    $date_time_now  = date("Y-m-d H:i:s");
    if (!empty($post_body)) {
        $insert_post    = mysqli_query($connection, "INSERT INTO comments VALUES('', '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");
        echo "<p class='comment_alert comment_alert--accept'>کامنت ارسال شد</p>";
    } else {
        echo "<p class='comment_alert comment_alert--reject'>فیلد نباید خالی باشد</p>";
    }

    if($posted_to != $userLoggedIn) {
        $notification = new Notification($connection, $userLoggedIn);
        $notification->insertNotification($post_id, $posted_to, "comment");
    }

    if($user_to != 'none' && $user_to != $userLoggedIn) {
        $notification = new Notification($connection, $userLoggedIn);
        $notification->insertNotification($post_id, $user_to, "profile_comment");
    }

    $get_commenters = mysqli_query($connection, "SELECT * FROM comments WHERE post_id = '$post_id'");
    $notified_users = array();
    while($row = mysqli_fetch_array($get_commenters)) {
        if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)) {
            $notification = new Notification($connection, $userLoggedIn);
            $notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

            array_push($notified_users, $row['posted_by']);
        }
    }
}
?>
<form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id ?>" method="POST">
    <textarea class="form-control text_com" name="post_body" id="floatingTextarea"></textarea>
    <input class="btn btn-primary btn_send_com" type="submit" name="postComment<?php echo $post_id; ?>" value="ارسال">
</form>

<!-- Load Comments -->
<?php
$get_comments = mysqli_query($connection, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC");
$count = mysqli_num_rows($get_comments);

if ($count != 0) {
    while ($comment = mysqli_fetch_array($get_comments)) {
        $comment_body = $comment['post_body'];
        $posted_to    = $comment['posted_to'];
        $posted_by    = $comment['posted_by'];
        $date_added   = $comment['date_added'];
        $removed      = $comment['removed'];

        $date_time_now  = date("Y-m-d H:i:s");
        $start_date     = new DateTime($date_added); // Time of Post
        $end_date       = new DateTime($date_time_now); // Current Time
        $interval       = $start_date->diff($end_date);

        if ($interval->y >= 1) {
            if ($interval == 1)
                $time_message = $interval->y . " year ago"; // 1 year ago
            else
                $time_message = $interval->y . " year ago"; // 1+ year ago
        } else if ($interval->m >= 1) {
            if ($interval->d == 0) {
                $days = " ago";
            } else if ($interval->d == 1) {
                $days = $interval->d . " day ago";
            } else {
                $days = $interval->d . " days ago";
            }

            if ($interval->m == 1) {
                $time_message = $interval->m . " month" . $days;
            } else {
                $time_message = $interval->m . " months" . $days;
            }
        } else if ($interval->d >= 1) {
            if ($interval->d == 1) {
                $time_message = "Yesterday";
            } else {
                $time_message = $interval->d . " days ago";
            }
        } else if ($interval->h >= 1) {
            if ($interval->h == 1) {
                $time_message = $interval->h . " hour ago";
            } else {
                $time_message = $interval->h . " hours ago";
            }
        } else if ($interval->i >= 1) {
            if ($interval->i == 1) {
                $time_message = $interval->i . " minute ago";
            } else {
                $time_message = $interval->i . " minutes ago";
            }
        } else {
            if ($interval->s < 30) {
                $time_message = "Just now";
            } else {
                $time_message = $interval->s . " seconds ago";
            }
        }
        $user_obj = new User($connection, $posted_by);
?>
        <div class="comment_section">
            <div class="reply_user--detail">
                <a href="<?php echo $posted_by ?>" target="_parent">
                    <img src="<?php echo $user_obj->getProfilePic() ?>" title="<?php echo $posted_by; ?>">
                </a>
                <div class="reply_usertime">
                    <a href="<?php echo $posted_by ?>" target="_parent">
                        <b class="reply_user"><?php echo $user_obj->getUsername(); ?></b>
                    </a>
                    <?php echo "<p class='reply_time'>$time_message</p>"; ?>
                </div>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp; <?php echo "<p class='reply_body'>$comment_body</p>"; ?>
            <hr>
        </div>
<?php
    }
} else {
    echo "<p class='nocomment'>کامنتی ارسال نشده</p>";
}
?>