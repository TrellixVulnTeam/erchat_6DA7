<?php
include "vendors/home_header.php";
include "classes/User.php";
include "classes/Post.php";

$limit = 100;

$posts = new Post($connection, $_REQUEST['userLoggedIn']);
$posts -> loadProfilePosts($_REQUEST, $limit);
