<?php include "vendors/home_header.php"; ?>
<?php include "classes/Post.php"; ?>
<?php include "navbar.php"; ?>
<?php
$message_obj = new Message($connection, $userLoggedIn);

if (isset($_GET['u']))
    $user_to = $_GET['u'];
else {
    $user_to = $message_obj->getMostRecentUser();
    if ($user_to == false)
        $user_to = 'new';
}

if ($user_to != "new")
    $user_to_obj = new User($connection, $user_to);

if (isset($_POST['post_message'])) {
    if (isset($_POST['message_body'])) {
        $body = mysqli_real_escape_string($connection, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);
    }
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

    <div class="section-flex">
        <section class="messages">
            <div class="messages-users">
                <?php
                if ($user_to != "new") {
                    echo "<h4 class='users-chat'>صفحه چت شما و <a class='chat-to' href='$user_to'>" . $user_to_obj->getUsername() . "</a></h4><hr><br>";
                    echo "<div class='messages-load' id='message-scroll'>";
                    echo $message_obj->getMessages($user_to);
                    echo "</div>";
                } else {
                    echo "<h4>پیام جدید</h4>";
                }
                ?>
            </div>
            <div class="messages-post">
                <form action="" method="POST">
                    <?php
                    if ($user_to == "new") {
                        echo "<p class='select-p'><span class='material-icons-sharp'>
                        navigate_before
                        </span> یک کاربر برای چت کردن انتخاب کنید </p><br>";
                    ?>

                        <div class="message-searching">
                            <p class="mes-search--p">گفتگو با: </p> <input class='form-control to-input search-user--m' id='search_text_input' type='text' onkeyup='getUser(this.value, "<?php echo $userLoggedIn; ?>")' name='q' autocomplete='off'> <br>
                        </div>

                    <?php
                        echo "<div class='results'></div>";
                    } else {
                        echo "<div class='text-input'>";
                        echo "<textarea name='message_body' id='message_textarea' placeholder='نوشتن پیام'></textarea>";
                        echo "<label for='message_submit'><span class='material-icons-sharp'> send </span></label>";
                        echo "<input type='submit' name='post_message' id='message_submit'>";
                        echo "</div>";
                    }
                    ?>
                </form>
            </div>

            <script>
                var div = document.getElementById("message-scroll");
                div.scrollTop = div.scrollHeight;
            </script>
        </section>

        <div class="conversations">
            <h4>گفتگوها</h4>

            <div class="loaded-conversations">
                <?php echo $message_obj->getConversations(); ?>
            </div>
            <br>
        </div>
        <a class="goto-newm" href="messages.php?u=new">گفتگوی جدید</a>
    </div>
</main>