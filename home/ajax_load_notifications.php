<?php include "vendors/home_header.php"; ?>
<?php include "classes/User.php"; ?>
<?php include "classes/Notification.php"; ?>
<?php
$limit = 7;

$notifications = new Notification($connection, $_REQUEST['userLoggedIn']);
echo $notifications->getNotifications($_REQUEST, $limit);
?>