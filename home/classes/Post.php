<?php

class Post
{
    private $user_obj;
    private $connection;

    public function __construct($connection, $user)
    {
        $this->connection = $connection;
        $this->user_obj = new User($connection, $user);
    }

    public function submitPost($body, $user_to, $imageName)
    {
        $body = strip_tags($body); // Removes HTML tags
        $body = mysqli_real_escape_string($this->connection, $body);

        $body = str_replace('\r\n', '\n', $body);
        $body = nl2br($body);

        $check_empty = preg_replace('/\s+/', '', $body); // Delete All Space

        if ($check_empty != "") {
            
            // Show YOUTUBE Link:
            $body_array = preg_split("/\s+/", $body);
            foreach($body_array as $key => $value) {
                if(strpos($value, "www.youtube.com/watch?v=") !== false) {
                    $link = preg_split("!&!", $value);
                    $value = preg_replace("!watch\?v=!", "embed/", $link[0]);
                    $value = "<br><iframe width=\'420\' height=\'315\' src=\'" . $value ."\'></iframe><br>";
                    $body_array[$key] = $value;
                }
            }
            $body = implode(" ", $body_array);

            // Current Date and Time:
            $date_added = date("Y-m-d H:i:s");
            // Get Username:
            $added_by = $this->user_obj->getUsername();

            if ($user_to == $added_by) {
                $user_to = "none";
            }

            // Insert Post:
            $query = mysqli_query($this->connection, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$imageName')");
            $returned_id = mysqli_insert_id($this->connection);

            // Insert Notification:
            if ($user_to != 'none') {
                $notification = new Notification($this->connection, $added_by);
                $notification->insertNotification($returned_id, $user_to, "like");
            }

            // Update post count:
            $num_posts = $this->user_obj->getNumPosts();
            $num_posts++;
            $update_query = mysqli_query($this->connection, "UPDATE users SET num_posts = '$num_posts' WHERE username = '$added_by'");
        }
    }

    public function loadPostsFriends($data, $limit)
    {
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();

        if ($page == 1)
            $start = 0;
        else
            $start = ($page - 1) * $limit;

        $str = "";
        $data_query = mysqli_query($this->connection, "SELECT * FROM posts WHERE deleted = 'no' ORDER BY id DESC");

        if (mysqli_num_rows($data_query) > 0) {
            $num_iterations = 0;
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];
                $image_path = $row['image'];

                if ($row['user_to'] == "none") {
                    $user_to = "";
                } else {
                    $user_to_obj = new User($this->connection, $row['user_to']);
                    $user_to_name = $user_to_obj->getUsername();
                    $user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
                }

                $added_by_obj = new User($this->connection, $added_by);
                if ($added_by_obj->isClosed()) {
                    continue;
                }

                $user_logged_obj = new User($this->connection, $userLoggedIn);
                if ($user_logged_obj->isFriend($added_by)) {

                    if ($num_iterations++ < $start)
                        continue;


                    if ($count > $limit) {
                        break;
                    } else {
                        $count++;
                    }

                    if ($userLoggedIn == $added_by)
                        $delete_button = "<button class='delete' id='post$id'>Delete</button>";
                    else
                        $delete_button = "";


                    $user_details_query = mysqli_query($this->connection, "SELECT username, profile_pic FROM users WHERE username = '$added_by'");
                    $user_row = mysqli_fetch_array($user_details_query);
                    $username = $user_row['username'];
                    $profile_pic = $user_row['profile_pic'];
?>
                    <script>
                        function toggle<?php echo $id ?>() {
                            var target = $(event.target);
                            if (!target.is("a") && !target.is("button")) {
                                var element = document.getElementById("toggleComment<?php echo $id ?>");

                                if (element.style.display == "block")
                                    element.style.display = "none";
                                else
                                    element.style.display = "block";
                            }
                        }
                    </script>
                <?php
                    $comments_check = mysqli_query($this->connection, "SELECT * FROM comments WHERE post_id='$id'");
                    $comments_check_num = mysqli_num_rows($comments_check);

                    // Time Frame:
                    $date_time_now = date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_time); // Time of Post
                    $end_date = new DateTime($date_time_now); // Current Time
                    $interval = $start_date->diff($end_date);

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

                    if($image_path != "") {
                        $image_div = "<div class='postImage'>
                                        <img src='$image_path'>
                                      </div>";
                    }   else {
                        $image_div = "";
                    }

                    $str .= "<div class='posts' onClick='javascript:toggle$id()'>
                                <div class='posts-container'>
                                    <div class='posts-container--user'>
                                        <img src='$profile_pic' alt='user'>
                                        <a href='$added_by'>
                                            $username
                                        </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;
                                        <small>$time_message</small>
                                        $delete_button
                                    </div>
                                    <div class='posts-container--status'>
                                        <p>$body</p>
                                        $image_div
                                    </div>
                                    <div class='posts-lico'>
                                        <div class='posts-lico--like'>
                                            <iframe class='iframe_like' src='like.php?post_id=$id' frameborder='0'></iframe>
                                        </div>
                                        <div class='posts-lico--comment'>
                                            <span class='material-icons-sharp'>
                                                question_answer
                                            </span>
                                            <p class='newsFeedPostOptions'>$comments_check_num Comment</p>
                                        </div>
                                    </div>
                                    <div class='post_comment' id='toggleComment$id'>
                                        <iframe class='iframe-style' src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                                    </div>
                                </div>

                            </div>";
                }
                ?>
                <script>
                    $(document).ready(function() {
                        $('#post<?php echo $id; ?>').on('click', function() {
                            // bootbox.confirm("آیا می خواهید این پست حذف شود؟", function(result) {
                            //     $.post("/erchat/home/delete_post.php?post_id=<?php echo $id; ?>", {
                            //         result: result
                            //     });

                            //     // New:
                            //     buttons:{
                            //         Ok:{
                            //             label: "بله"
                            //         }

                            //         Cancel: {
                            //             label: "خیر"
                            //         }
                            //     }
                            //     // End New

                            //     if (result)
                            //         location.reload();
                            // });

                            bootbox.confirm({
                                // size: "small",
                                message: "آیا می خواهید این پست حذف شود؟",
                                buttons: {
                                    confirm: {
                                        label: 'بله',
                                        className: 'btn-primary'
                                    },
                                    cancel: {
                                        label: 'خیر',
                                        className: 'btn-secondary'
                                    }
                                },

                                callback: function(result) {
                                    $.post("/erchat/home/delete_post.php?post_id=<?php echo $id; ?>", {
                                        result: result
                                    });
                                }
                            })
                        });
                    });
                </script>
            <?php
            }
        }
        echo $str;
    }

    public function loadProfilePosts($data, $limit)
    {
        $page = $data['page'];
        $profileUser = $data['profileUsername'];
        $userLoggedIn = $this->user_obj->getUsername();

        if ($page == 1)
            $start = 0;
        else
            $start = ($page - 1) * $limit;

        $str = "";
        $data_query = mysqli_query($this->connection, "SELECT * FROM posts WHERE deleted = 'no' AND ((added_by = '$profileUser' AND user_to = 'none') OR user_to = '$profileUser') ORDER BY id DESC");

        if (mysqli_num_rows($data_query) > 0) {
            $num_iterations = 0;
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];

                if ($num_iterations++ < $start)
                    continue;


                if ($count > $limit) {
                    break;
                } else {
                    $count++;
                }

                if ($userLoggedIn == $added_by)
                    $delete_button = "<button class='delete' id='post$id'>Delete</button>";
                else
                    $delete_button = "";


                $user_details_query = mysqli_query($this->connection, "SELECT username, profile_pic FROM users WHERE username = '$added_by'");
                $user_row = mysqli_fetch_array($user_details_query);
                $username = $user_row['username'];
                $profile_pic = $user_row['profile_pic'];
            ?>
                <script>
                    function toggle<?php echo $id ?>() {
                        var target = $(event.target);
                        if (!target.is("a") && !target.is("button")) {
                            var element = document.getElementById("toggleComment<?php echo $id ?>");

                            if (element.style.display == "block")
                                element.style.display = "none";
                            else
                                element.style.display = "block";
                        }
                    }
                </script>
                <?php
                $comments_check = mysqli_query($this->connection, "SELECT * FROM comments WHERE post_id='$id'");
                $comments_check_num = mysqli_num_rows($comments_check);

                // Time Frame:
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); // Time of Post
                $end_date = new DateTime($date_time_now); // Current Time
                $interval = $start_date->diff($end_date);

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

                $str .= "<div class='posts' onClick='javascript:toggle$id()'>
                                <div class='posts-container'>
                                    <div class='posts-container--user'>
                                        <img src='$profile_pic' alt='user'>
                                        <a href='$added_by'>
                                            $username
                                        </a> &nbsp;&nbsp;&nbsp;&nbsp;
                                        <small>$time_message</small>
                                        $delete_button
                                    </div>
                                    <div class='posts-container--status'>
                                        <p>$body</p>
                                    </div>
                                    <div class='posts-lico'>
                                        <div class='posts-lico--like'>
                                            <iframe class='iframe_like' src='like.php?post_id=$id' frameborder='0'></iframe>
                                        </div>
                                        <div class='posts-lico--comment'>
                                            <span class='material-icons-sharp'>
                                                question_answer
                                            </span>
                                            <p class='newsFeedPostOptions'>$comments_check_num Comment</p>
                                        </div>
                                    </div>
                                    <div class='post_comment' id='toggleComment$id'>
                                        <iframe class='iframe-style' src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                                    </div>
                                </div>

                            </div>";
                ?>
                <script>
                    $(document).ready(function() {
                        $('#post<?php echo $id; ?>').on('click', function() {
                            bootbox.confirm("آیا می خواهید این پست حذف شود؟", function(result) {
                                $.post("/erchat/home/delete_post.php?post_id=<?php echo $id; ?>", {
                                    result: result
                                });

                                if (result)
                                    location.reload();
                            });
                        });
                    });
                </script>
            <?php
            }
        }
        echo $str;
    }

    public function getSinglePost($post_id)
    {
        $userLoggedIn = $this->user_obj->getUsername();
        $opened_query = mysqli_query($this->connection, "UPDATE notifications SET opened = 'yes' WHERE user_to = '$userLoggedIn' AND link LIKE '%=$post_id'");
        $str = "";
        $data_query = mysqli_query($this->connection, "SELECT * FROM posts WHERE deleted = 'no' AND id = '$post_id'");

        if (mysqli_num_rows($data_query) > 0) {

            $row = mysqli_fetch_array($data_query);
            $id = $row['id'];
            $body = $row['body'];
            $added_by = $row['added_by'];
            $date_time = $row['date_added'];

            if ($row['user_to'] == "none") {
                $user_to = "";
            } else {
                $user_to_obj = new User($this->connection, $row['user_to']);
                $user_to_name = $user_to_obj->getUsername();
                $user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
            }

            $added_by_obj = new User($this->connection, $added_by);
            if ($added_by_obj->isClosed()) {
                return;
            }

            $user_logged_obj = new User($this->connection, $userLoggedIn);
            if ($user_logged_obj->isFriend($added_by)) {

                if ($userLoggedIn == $added_by)
                    $delete_button = "<button class='delete' id='post$id'>Delete</button>";
                else
                    $delete_button = "";


                $user_details_query = mysqli_query($this->connection, "SELECT username, profile_pic FROM users WHERE username = '$added_by'");
                $user_row = mysqli_fetch_array($user_details_query);
                $username = $user_row['username'];
                $profile_pic = $user_row['profile_pic'];
            ?>
                <script>
                    function toggle<?php echo $id ?>() {
                        var target = $(event.target);
                        if (!target.is("a") && !target.is("button")) {
                            var element = document.getElementById("toggleComment<?php echo $id ?>");

                            if (element.style.display == "block")
                                element.style.display = "none";
                            else
                                element.style.display = "block";
                        }
                    }
                </script>
                <?php
                $comments_check = mysqli_query($this->connection, "SELECT * FROM comments WHERE post_id='$id'");
                $comments_check_num = mysqli_num_rows($comments_check);

                // Time Frame:
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); // Time of Post
                $end_date = new DateTime($date_time_now); // Current Time
                $interval = $start_date->diff($end_date);

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

                $str .= "<div class='posts' onClick='javascript:toggle$id()'>
                                <div class='posts-container'>
                                    <div class='posts-container--user'>
                                        <img src='$profile_pic' alt='user'>
                                        <a href='$added_by'>
                                            $username
                                        </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;
                                        <small>$time_message</small>
                                        $delete_button
                                    </div>
                                    <div class='posts-container--status'>
                                        <p>$body</p>
                                    </div>
                                    <div class='posts-lico'>
                                        <div class='posts-lico--like'>
                                            <iframe class='iframe_like' src='like.php?post_id=$id' frameborder='0'></iframe>
                                        </div>
                                        <div class='posts-lico--comment'>
                                            <span class='material-icons-sharp'>
                                                question_answer
                                            </span>
                                            <p class='newsFeedPostOptions'>$comments_check_num Comment</p>
                                        </div>
                                    </div>
                                    <div class='post_comment' id='toggleComment$id'>
                                        <iframe class='iframe-style' src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                                    </div>
                                </div>

                            </div>";
                ?>
                <script>
                    $(document).ready(function() {
                        $('#post<?php echo $id; ?>').on('click', function() {
                            bootbox.confirm("آیا می خواهید این پست حذف شود؟", function(result) {
                                $.post("/erchat/home/delete_post.php?post_id=<?php echo $id; ?>", {
                                    result: result
                                });

                                if (result)
                                    location.reload();
                            });
                        });
                    });
                </script>
<?php
            } else {
                echo "<p class='alert alert-warning' style='text-align: center; margin-top: 2rem;'>شما نمیتوانید این پست را ببینید</p>";
                return;
            }
        } else {
            echo "<p class='alert alert-danger' style='text-align: center; margin-top: 2rem;'>این پست پیدا نشد</p>";
            return;
        }
        echo $str;
    }
}
?>