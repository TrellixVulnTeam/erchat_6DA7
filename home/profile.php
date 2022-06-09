<?php include("vendors/home_header.php"); ?>
<?php include "classes/Post.php"; ?>
<?php include("navbar.php"); ?>
<?php
$message_obj = new Message($connection, $userLoggedIn);
$user = new User($connection, $userLoggedIn);

if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $user_details_query = mysqli_query($connection, "SELECT * FROM users WHERE username = '$username'");
    $user_array = mysqli_fetch_array($user_details_query);

    $num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}

if (isset($_POST['remove_friend'])) {
    $user = new User($connection, $userLoggedIn);
    $user->removeFriend($username);
}

if (isset($_POST['add_friend'])) {
    $user = new User($connection, $userLoggedIn);
    $user->sendRequest($username);
}

if (isset($_POST['respond_request'])) {
    header("Location: requests.php");
}

if(isset($_POST['post_message'])) {
    if(isset($_POST['message_body'])) {
      $body = mysqli_real_escape_string($connection, $_POST['message_body']);
      $date = date("Y-m-d H:i:s");
      $message_obj->sendMessage($username, $body, $date);
    }
  
    $link = '#profileTabs a[href="#messages_div"]';
    echo "<script> 
            $(function() {
                $('" . $link ."').tab('show');
            });
          </script>";
  }
?>

<div class="profile-info">
    <?php
        $profile_user_obj = new User($connection, $username);
        $flname = $profile_user_obj->getFirstAndLastName();
    ?>
    
    <div class="user-imagepr">
        <figure class="user-imagepr--figure">
            <img src="<?php echo $user_array['profile_pic']; ?>" alt="image" class="user-imagepr--img">
            <figcaption class="user-imagepr--caption"><?php echo $flname; ?></figcaption>
        </figure>
    </div>

    <form action="<?php echo $username; ?>" method="POST">
        <?php
        $profile_user_obj = new User($connection, $username);
        if ($profile_user_obj->isClosed()) {
            header("Location: user_closed.php");
        }

        $logged_in_user_obj = new User($connection, $userLoggedIn);

        if ($userLoggedIn != $username) {
            if ($logged_in_user_obj->isFriend($username)) {
                echo '<div class="profile-info--btn">
                            <input name="remove_friend" class="profile-btn remove" type="submit" value="حذف دنبال کننده">
                      </div>';
            } else if ($logged_in_user_obj->didReceiveRequest($username)) {
                echo '<div class="profile-info--btn">
                <input name="respond_request" class="profile-btn respond" type="submit" value="تایید درخواست">
            </div>';
            } else if ($logged_in_user_obj->didSendRequest($username)) {
                echo '<div class="profile-info--btn">
                <input name="" class="profile-btn send" type="submit" value="درخواست ارسال شد">
            </div>';
            } else {
                echo '<div class="profile-info--btn">
                <input name="add_friend" class="profile-btn add" type="submit" value="افزودن به دوستان">
            </div>';
            }
        }
        ?>
    </form>
    
    <div class="profile-user--detail">
        <div class="profile-user--posts">
            <p>پست:</p>
            <small><?php echo $user_array['num_posts']; ?></small>
        </div>
        <div class="profile-user--posts">
            <p>لایک:</p>
            <small><?php echo $user_array['num_likes'] ?></small>
        </div>
        <div class="profile-user--posts">
            <p>دنبال کننده:</p>
            <small><?php echo $num_friends; ?></small>
        </div>
    </div>

    <?php
    if ($userLoggedIn != $username) {
        echo '<p class="joint">';
        echo $logged_in_user_obj->getMutualFriends($username);
        echo '    دوست مشترک</p>';
    }
    ?>

</div>
<div class="profile-posts">
    <!-- Tabs -->
    <ul class="nav nav-tabs nav-tabs--style" role="tablist" id="profileTabs">

        <li role="presentation" class="nav-item nav-item--style">
            <a class="nav-link nav-link--style active" href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-bs-toggle="tab">پست ها</a>
        </li>

        <li role="presentation" class="nav-item nav-item--style">
            <a class="nav-link nav-link--style" href="#messages_div" aria-controls="messages_div" role="tab" data-bs-toggle="tab">پیام ها</a>
        </li>
    </ul>
    <!-- End Tabs -->

    <div class="tab-content">

        <!-- Newsfeed Div -->
        <div role="tabpanel" class="tab-pane active" id="newsfeed_div">
            <div class="posts_area profile-post"></div>
            <img id="loading" src="icons/18-autorenew-outline (1).gif" style="width: 4rem;">
        </div>
        <!-- End Newsfeed Div -->

        <!-- Messages Div -->
        <div role="tabpanel" class="tab-pane" id="messages_div">
            <?php
            echo "<h4 class='users-chat'>صفحه چت شما و <a class='chat-to' href='" . $username . "'>" . $profile_user_obj->getUsername() . "</a></h4><hr><br>";
            echo "<div class='messages-load' id='message-scroll'>";
            echo $message_obj->getMessages($username);
            echo "</div>";
            ?>

            <div class="messages-post--profile">
                <form action="" method="POST">
                    <div class='text-input--profile'>
                        <textarea name='message_body' id='message_textarea-profile' placeholder='نوشتن پیام'></textarea>
                        <label for='message_submit'><span class='material-icons-sharp'> send </span></label>
                        <input type='submit' name='post_message' id='message_submit'>
                    </div>
                </form>
            </div>

            <script>
                var div = document.getElementById("message-scroll");
                div.scrollTop = div.scrollHeight;
            </script>
        </div>
        <!-- End Messages Div -->
    </div>
</div>

<script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    var profileUsername = '<?php echo $username; ?>'

    $(document).ready(function() {

        $('#loading').show();

        //Original ajax request for loading first posts 
        $.ajax({
            url: "/erchat/home/ajax_load_profile_posts.php",
            type: "POST",
            data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
            cache: false,

            success: function(data) {
                $('#loading').hide();
                $('.posts_area').html(data);
            }
        });

        $(window).scroll(function() {
            var height = $('.posts_area').height(); //Div containing posts
            var scroll_top = $(this).scrollTop();
            var page = $('.posts_area').find('.nextPage').val();
            var noMorePosts = $('.posts_area').find('.noMorePosts').val();

            if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                $('#loading').show();

                var ajaxReq = $.ajax({
                    url: "/erchat/home/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                    cache: false,

                    success: function(response) {
                        $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
                        $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

                        $('#loading').hide();
                        $('.posts_area').append(response);
                    }
                });

            } //End if 

            return false;

        }); //End (window).scroll(function())


    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<?php include "../vendors/footer.php" ?>