<?php include "vendors/home_header.php"; ?>
<?php include "classes/Post.php"; ?>
<?php include "navbar.php" ?>
<?php
// Send Post:
if (isset($_POST['post'])) {

    // Add Image To Post:
    $uploadOk = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $errorMessage = "";

    if ($imageName != "") {
        $targetDir = 'post_pic';
        $imageName = $targetDir . uniqid() . basename($imageName);
        $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

        if ($_FILES['fileToUpload']['size'] > 9999999999) {
            $errorMessage = "<p>متاسفانه حجم فایل بالاست</p>";
            $uploadOk = 0;
        }

        if (strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
            $errorMessage = "<p>فرمت فایل باید jpg, png یا jpeg باشد</p>";
            $uploadOk = 0;
        }

        if ($uploadOk) {
            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
            } else {
                $uploadOk = 0;
            }
        }
    }

    // Add Post:
    if ($uploadOk) {
        $post = new Post($connection, $userLoggedIn);
        $post->submitPost($_POST['writing'], 'none', $imageName);
        header("Location: index.php");
    } else {
        echo "<div class='alert alert-danger err-large'>$errorMessage</div>";
    }
}
?>
<a id="backToTopBtn"></a>
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

    <section class="send-post">
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <div class="send-field">
                <img src="<?php echo $user['profile_pic']; ?>" alt="user">
                <textarea name="writing" placeholder="متنی بنویسید..." class="post-write" maxlength="188"></textarea>
            </div>
            <input name="post" type="submit" class="send-btn" value="بفرست">
            <label for="fileToUpload">
                <span class="material-icons-sharp add-icon--label"> add_a_photo </span>
            </label>
            <input type="file" name="fileToUpload" id="fileToUpload">
        </form>

        <div class="posts_area"></div>
        <img id="loading" src="icons/18-autorenew-outline (1).gif" style="width: 4rem;">

    </section>
    <script>
        var userLoggedIn = '<?php echo $userLoggedIn; ?>';

        $(document).ready(function() {

            $('#loading').show();

            $.ajax({
                url: "/erchat/home/ajax_load_posts.php",
                type: "POST",
                data: "page=1&userLoggedIn=" + userLoggedIn,
                cache: false,

                success: function(data) {
                    $('#loading').hide();
                    $('.posts_area').html(data);
                }
            });

            $(window).scroll(function() {
                var height = $('.posts_area').height();
                var scroll_top = $(this).scrollTop();
                var page = $('.posts_area').find('.nextPage').val();
                var noMorePosts = $('.posts_area').find('.noMorePosts').val();

                if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                    $('#loading').show();

                    var ajaxReq = $.ajax({
                        url: "/erchat/home/ajax_load_posts.php",
                        type: "POST",
                        data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                        cache: false,

                        success: function(response) {
                            $('.posts_area').find('.nextPage').remove();
                            $('.posts_area').find('.noMorePosts').remove();

                            $('#loading').hide();
                            $('.posts_area').append(response);
                        }
                    });

                } //End if 

                return false;

            });
        });
    </script>
</main>
<?php include "../vendors/footer.php" ?>