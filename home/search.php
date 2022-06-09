<?php include "vendors/home_header.php"; 
ch_title("نتیجه جستجو")
?>
<?php include "classes/Post.php"; ?>
<?php include "navbar.php"; ?>
<?php
if (isset($_GET['q'])) {
    $query = $_GET['q'];
} else {
    $query = "";
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = "نام ها";
}
?>

<div class="showSearchResult">
<?php
    if ($query == "") {
        echo "<p class='alert alert-danger error-Sr'>در فیلد جستجو چیزی بنویسید</p>";
    } else {

        if ($type == "username")
            $usersReturnedQuery = mysqli_query($connection, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed = 'no' LIMIT 8");
        else {
            $names = explode(" ", $query);

            if (count($names) == 3)
                $usersReturnedQuery = mysqli_query($connection, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed = 'no'");
            else if (count($names) == 2)
                $usersReturnedQuery = mysqli_query($connection, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed = 'no'");
            else
                $usersReturnedQuery = mysqli_query($connection, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed = 'no'");
        }

        if (mysqli_num_rows($usersReturnedQuery) == 0)
            echo "<p class='errorDet'>نتیجه ای برای این جستجو در " . $type . " یافت نشد برای:" . $query . "</p>";
        else
            echo "<div class='textRe'>";
            echo "<p class='textFre'>نتایج پیدا شده: " . mysqli_num_rows($usersReturnedQuery) . "</P><br>";
            echo "<p>تلاش برای جستجو در:</p>";
            echo "<a class='btn btn-warning' href='search.php?q=" . $query . "&type=name'>اسم ها</a> <a class='btn btn-info' href='search.php?q=" . $query . "&type=username'>نام کاربری ها</a><br><br><hr>";
            echo "</div>";

        while ($row = mysqli_fetch_array($usersReturnedQuery)) {
            $user_obj = new User($connection, $user['username']);

            $button = "";
            $mutual_friends = "";

            if ($user['username'] != $row['username']) 
                $mutual_friends = "<p class='mutualText'>" . $user_obj->getMutualFriends($row['username']) . " دوست مشترک</p>";
            

            echo "<div class='search_result'>
                    
                    <div class='result_profile_pic'>
                        <a href='" . $row['username'] . "'>
                            <img class='search-rimg' src='" . $row['profile_pic'] . "'>
                        </a>
                    </div>
                    <a href='" . $row['username'] . "'>
                        <p>" . $row['first_name'] . " " . $row['last_name'] . "</p>
                        <p class='idResultLast'>@" . $row['username'] . "</p>
                    </a>
                    <p class='mutualReSearch'>" . $mutual_friends . "</p>
                  </div>";
        }
    }
    ?>
</div>