<?php ob_start(); ?>
<?php require '../vendors/db.php' ?>
<?php include "functions.php" ?>
<?php session_start(); ?>
<?php

if (!isset($_SESSION['user_role'])) {
    redirect("../index.php");
}

if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $query = "SELECT * FROM users WHERE username = '$userLoggedIn'";
    $user_details_query = mysqli_query($connection, $query);
    $user = mysqli_fetch_array($user_details_query);
} else {
    header("Location: ../register.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet" />
    <link rel="stylesheet" href="../css/jquery.mobile-1.4.5.min.css">
    <link rel="stylesheet" type="text/css" href="your_website_domain/css_root/flaticon.css" />
    <link rel="stylesheet" type="text/css" href="../css/fontawesome-free-6.0.0-web/css/fontawesome.css">
    <link rel="stylesheet" type="text/css" href="../css/fontawesome-free-6.0.0-web/css/fontawesome.min.css">

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src="js/bootbox.min.js"></script>
    <script src="js/first.js"></script>
    <script src="js/jquery.Jcrop.js"></script>
    <script src="js/jcrop_bits.js"></script>
    
    <!-- <script src="../../js/bootstrap.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> -->

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> -->

    <!-- <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js" integrity="sha256-eTyxS0rkjpLEo16uXTS0uVCS4815lc40K2iVpWDvdSY=" crossorigin="anonymous"></script> -->

    <!-- <script src="js/jquery-3.6.0.min.js"></script> -->
    <!-- <script src="jquery-3.5.1.min.js"></script> -->

    <!-- Style -->
    <!-- <link rel="stylesheet" type="text/css" href="../../css/bootstrap.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" type="text/css" href="../css/jquery.Jcrop.css">

    <title>ERchat | Social media</title>

    <script src="https://kit.fontawesome.com/6df5ce0ad9.js" crossorigin="anonymous"></script>

    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> -->
    <!-- <script src="../js/bootbox.min.js"></script> -->

</head>

<body>