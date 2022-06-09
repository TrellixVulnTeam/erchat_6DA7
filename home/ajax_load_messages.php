<?php include "vendors/home_header.php"; ?>
<?php include "classes/User.php"; ?>
<?php include "classes/Message.php"; ?>
<?php
$limit = 7;

$message = new Message($connection, $_REQUEST['userLoggedIn']);
echo $message->getConvosDropdown($_REQUEST, $limit);
?>