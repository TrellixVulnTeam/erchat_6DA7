<?php ob_start(); ?>
<?php include "classes/User.php"; ?>
<?php include "classes/Message.php"; ?>
<?php include "classes/Notification.php"; ?>

<?php
// if (isset($_POST['post'])) {
//     $post = new Post($connection, $userLoggedIn);
//     $post->submitPost($_POST['post_text'], 'none', $imageName);
// }
?>

<nav>
    <div class="nav">
        <a href="index.php"><img src="../images/logo-fix.png" alt="Logo"></a>
        <div class="all-search">
            <form action="search.php" method="GET" name="search_form">
                <div class="nav-search">
                    <span class="material-icons-sharp button-holder"> search </span>
                    <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" autocomplete="off" placeholder="جستجو" id="nav-search--input">
                </div>
            </form>
            <div class="search_results searchHid hidden"></div>
            <div class="search_results_footer_empty"></div>
        </div>

        <?php
        //Unread Messages:
        $messages = new Message($connection, $userLoggedIn);
        $num_messages = $messages->getUnreadNumber();

        // Unread Notifications:
        $notifications = new Notification($connection, $userLoggedIn);
        $num_notifications = $notifications->getUnreadNumber();

        // Unread Requests:
        $user_obj = new User($connection, $userLoggedIn);
        $num_requests = $user_obj->getNumberFriendRequests();
        ?>

        <div class="nav-taskbar">

            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                <span class="material-icons-sharp nav-taskbar--icon"> notifications </span>
                <?php
                if ($num_notifications > 0)
                    echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
                ?>
            </a>

            <a href="requests.php">
                <span class="material-icons-sharp nav-taskbar--icon"> group </span>
                <?php
                if ($num_requests > 0)
                    echo '<span class="request_badge" id="unread_requests">' . $num_requests . '</span>';
                ?>
            </a>

            <a href="index.php"><span class="material-icons-sharp nav-taskbar--icon"> home </span></a>

            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
                <span class="material-icons-sharp nav-taskbar--icon"> email </span>
                <?php
                if ($num_messages > 0)
                    echo '<span class="message_badge" id="unread_message">' . $num_messages . '</span>';
                ?>
            </a>

            <a href="settings.php"><span class="material-icons-sharp nav-taskbar--icon"> settings </span></a>
        </div>

        <div class="nav-user">
            <img src="<?php echo $user['profile_pic']; ?>" alt="user image" class="nav-user--img">
            <a href="<?php echo $userLoggedIn; ?>"><small class="nav-user--name">
                    <?php
                    if (isset($_SESSION['username'])) {
                        echo $_SESSION['username'];
                    }
                    ?>
                </small></a>
        </div>

        <div class="nav-logout">
            <a href="../vendors/logout.php"><span class="material-icons-sharp"> logout </span></a>
        </div>
    </div>
</nav>

<!-- Dropdown Message -->
<div class="dropdown_data_window" style="height: 0px; border: none;"></div>
<input type="hidden" id="dropdown_data_type" value="">

<script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';

    $(document).ready(function() {

        $('.dropdown_data_window').scroll(function() {
            var inner_height = $('.dropdown_data_window').innerHeight();
            var scroll_top = $('.dropdown_data_window').scrollTop();
            var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
            var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

            if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {

                var pageName;
                var type = $('#dropdown_data_type').val();

                if (type == 'notification')
                    pageName = "ajax_load_notifications.php";
                else if (type == 'message')
                    pageName = "ajax_load_messages.php";


                var ajaxReq = $.ajax({
                    url: "/erchat/home/" + pageName,
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                    cache: false,

                    success: function(response) {
                        $('.dropdown_data_window').find('.nextPageDropdownData').remove();
                        $('.dropdown_data_window').find('.noMoreDropdownData').remove();

                        $('.dropdown_data_window').append(response);
                    }
                });

            } //End if 

            return false;

        });
    });
</script>

<script>
var inputSearch = document.getElementById("nav-search--input");
var searchingResult = document.querySelector(".search_results");
var searchHid = document.querySelector(".searchHid");
var body = document.createElement("body");
var main = document.createElement("main");
var resultDis = document.querySelector(".resultDisplay");

inputSearch.addEventListener('click', function(){
    searchingResult.classList.remove('hidden');
    searchingResult.style.background = "#f8f9fa";
    searchingResult.style.height = "28rem";
    // searchingResult.style.shadow = "none";
});

body.addEventListener('click', function(){
    searchingResult.classList.add('hidden');
    searchingResult.style.height = "0";
    searchingResult.style.background = "none";
    searchHid.classList.add('hidden');
    resultDis.classList.add('hidden');
    searchingResult.style.display = "none";
});

main.addEventListener('click', function(){
    searchingResult.classList.add('hidden');
    searchingResult.style.display = "none";
})
</script>