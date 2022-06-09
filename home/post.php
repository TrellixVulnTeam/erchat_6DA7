<?php include "vendors/home_header.php"; ?>
<?php include "classes/Post.php"; ?>
<?php include "navbar.php"; ?>
<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $id = 0;
}
?>
<main>
    <aside>
        <div class="user-profile">
            <div class="user-profile--img">
                <img src="<?php echo $user['profile_pic']; ?>">
            </div>
            <div class="user-profile--personal">
                <div class="user-profile--det">
                    <h5>نام کاربری:</h5>
                    <p>
                        <?php
                        if (isset($_SESSION['username'])) {
                            echo $_SESSION['username'];
                        }
                        ?>
                    </p>
                </div>
                <div class="user-profile--det">
                    <h5>تعداد پست:</h5>
                    <p><?php echo $user['num_posts'] ?></p>
                </div>
                <div class="user-profile--det">
                    <h5>تعداد لایک:</h5>
                    <p><?php echo $user['num_likes'] ?></p>
                </div>

            </div>
        </div>
    </aside>
    <div class="send-post single-post">
        <div class="posts_area">
            <?php
            $posts = new Post($connection, $userLoggedIn);
            $posts->getSinglePost($id);
            ?>
        </div>
    </div>
</main>