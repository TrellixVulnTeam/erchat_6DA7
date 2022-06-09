<?php
class Notification {
    private $user_obj;
    private $connection;

    public function __construct($connection, $user)
    {
        $this->connection = $connection;
        $this->user_obj = new User($connection, $user);
    }

    public function getUnreadNumber() {
        $userLoggedIn = $this->user_obj->getUsername();
        $query = mysqli_query($this->connection, "SELECT * FROM notifications WHERE viewed = 'no' AND user_to = '$userLoggedIn'");
        return mysqli_num_rows($query);
    }

    public function getNotifications($data, $limit) {
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";

        if($page == 1)
            $start = 0;
        else
            $start = ($page - 1) * $limit;

        
        $set_viewed_query = mysqli_query($this->connection, "UPDATE notifications SET viewed = 'yes' WHERE user_to = '$userLoggedIn'");

        $query = mysqli_query($this->connection, "SELECT * FROM notifications WHERE user_to = '$userLoggedIn' ORDER BY id DESC");

        if(mysqli_num_rows($query) == 0) {
            echo "اعلانی برای شما نیامده";
            return;
        }

        $num_iterations = 0;
        $count = 1;

        while($row = mysqli_fetch_array($query)) {
            
            if($num_iterations++ < $start)
                continue;
            
            if($count > $limit)
                break;
            else
                $count++;

            
            $user_from = $row['user_from'];
            $user_data_query = mysqli_query($this->connection, "SELECT * FROM users WHERE username = '$user_from'");
            $user_data = mysqli_fetch_array($user_data_query);

            // Time Frame:
            $date_time_now = date("Y-m-d H:i:s");
            $start_date = new DateTime($row['datetime']); // Time of Post
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
 
            $opened = $row['opened'];
            $style = (isset($row['opened']) && $row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";

            $return_string .= "<a class='conv-all' href='" . $row['link'] ."'> 
                                    <div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
                                        <div class='notificationsProfilePic'>
                                            <img src = '" . $user_data['profile_pic'] . "'>
                                        </div>
                                        <div class='tino'>
                                            <p class = 'timestamp_smaller' id='grey'>" . $time_message . "</p>
                                            <p class = 'noti_ms'>" . $row['message'] . "</p>
                                        </div>
                                    </div>
                               </a>";
        }

        if($count > $limit)
            $return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
        else
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'>
            <p style='text-align: center;'>اعلان دیگری ندارید</p>";

        return $return_string;
    }

    public function insertNotification($post_id, $user_to, $type) {
        $userLoggedIn = $this->user_obj->getUsername();
        $userLoggedInName = $this->user_obj->getFirstAndLastName();
        $date_time = date("Y-m-d H:i:s");
        
        switch($type) {
            case 'comment':
                $message = $userLoggedInName . " برای شما کامنت گذاشت";
                break;
            case 'like':
                $message = $userLoggedInName . " شما را لایک کرد";
                break;
            case 'profile_post':
                $message = $userLoggedInName . " در پروفایل شما ارسال شده";
                break;
            case 'comment_non_owner':
                $message = $userLoggedInName . " کامنتی درباره پستی که شما نظر داده اید";
                break;
            case 'profile_comment':
                $message = $userLoggedInName . " در روفایل شما کامنت ارسال شده";
                break;
        }
        
        $link = "post.php?id=" . $post_id;
        $insert_query = mysqli_query($this->connection, "INSERT INTO notifications VALUES('', '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
    }
}
?>