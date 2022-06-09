<?php include "vendors/home_header.php"; ?>
<?php include "classes/User.php"; ?>
<?php
$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

if(strpos($query, "_") !== false) {
    $usersReturned = mysqli_query($connection, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed = 'no' LIMIT 8");

}   else if(count($names) == 2)   {
        $usersReturned = mysqli_query($connection, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND user_closed = 'no' LIMIT 8");

}   else    {
    $usersReturned = mysqli_query($connection, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed = 'no' LIMIT 8");
}

if($query != "") {
    while($row = mysqli_fetch_array($usersReturned)) {
        $user = new User($connection, $userLoggedIn);
        if($row['username'] != $userLoggedIn) {
            $mutual_friends = $user->getMutualFriends($row['username']) . "<p class='mufr-text'> دوست مشترک</p>";
        
        }   else    {
            $mutual_friends = "";
        }

        if($user->isFriend($row['username'])) {
            echo "<div class='result-display'>
                    <a class='all-result' href='messages.php?u=" . $row['username'] . "'>
                        <div class='liveSearchProfilePic'>
                            <img class='result-img' src='". $row['profile_pic'] . "'>
                        </div>

                        <div class='liveSearchText'>
                            <p class='liveSearchText-flname'>".$row['first_name'] . " " . $row['last_name']. "</p>
                            <div class='liveSearchText-df'>
                                <p class='liveSearchText-df--uname'> @". $row['username'] . "</p>
                                <p class='liveSearchText-df--mufr' id='grey'>".$mutual_friends . "</p>
                            </div>
                        </div>
                    </a>
                  </div>";
        }
    }
}
?>