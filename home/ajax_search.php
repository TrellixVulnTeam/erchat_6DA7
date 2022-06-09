<?php include "vendors/home_header.php"; ?>
<?php include "classes/User.php"; ?>
<?php
$user = new User($connection, $userLoggedIn);
$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

if (strpos($query, '_') !== false)
    $usersReturnedQuery = mysqli_query($connection, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed = 'no' LIMIT 8");
else if (count($names) == 2)
    $usersReturnedQuery = mysqli_query($connection, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed = 'no' LIMIT 8");
else
    $usersReturnedQuery = mysqli_query($connection, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed = 'no' LIMIT 8");


if ($query != "") {
    while ($row = mysqli_fetch_array($usersReturnedQuery)) {
        

        if ($row['username'] != $userLoggedIn)
            $mutual_friends = $user->getMutualFriends($row['username']) . "<p class='mutualText'> دوست مشترک</p>";
        else
            $mutual_friends = "";

        echo "<div class='resultDisplay resultDisplaySearch'>
                <a href='" . $row['username'] . "'>
                    <div class='liveSearchProfilePic'>
                        <img class='searchImg' src='" . $row['profile_pic'] . "'>
                    </div>
                    <div class='tino'>
                        <p class='search-fl'>
                            " . $row['first_name'] . " " . $row['last_name'] . "
                        </p>
                        <div class='rowdis'>
                            <p class='searchU'>@" . $row['username'] . "</p>
                            <p class='mutualSstyle'>" . $mutual_friends . "</p>
                        </div>
                    </div>
                </a>
              </div>";
    }
}
?>