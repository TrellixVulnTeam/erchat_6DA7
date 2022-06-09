<?php include "vendors/home_header.php"; ?>
<?php include "classes/User.php"; ?>
<?php include "classes/Post.php"; ?>
<?php 
$limit = 500;

$posts = new Post($connection, $_REQUEST['userLoggedIn']);
$posts->loadPostsFriends($_REQUEST, $limit);
?>