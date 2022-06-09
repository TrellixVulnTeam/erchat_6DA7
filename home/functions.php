<?php
function confirmQuery($result)
{
    global $connection;
    if (!$result) {
        die("QUERY FAILED" . mysqli_error($connection));
    }
}

function redirect($location)
{
    header("Location: " . $location);
}

function isLoggedIn()
{
    if (isset($_SESSION['user_role'])) {
        return true;
    }
    return false;
}

function isLoggedUser()
{
    if (isset($_SESSION['username'])) {
        return true;
    }
    return false;
}

function email_exists($email)
{
    global $connection;
    $query = "SELECT email FROM users WHERE email = '$email'";
    $result = mysqli_query($connection, $query);
    confirmQuery($result);
    if (mysqli_num_rows($result)) {
        return true;
    } else {
        return false;
    }
}

function register_user($fname, $lname, $username, $email, $password, $password2)
{
    global $connection;
    $fname = mysqli_real_escape_string($connection, $fname);
    $lname = mysqli_real_escape_string($connection, $lname);
    $username = mysqli_real_escape_string($connection, $username);
    $email = mysqli_real_escape_string($connection, $email);
    $password = mysqli_real_escape_string($connection, $password);
    $password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
    $password2 = mysqli_real_escape_string($connection, $password2);
    $date = date("Y-m-d");
    $rand = rand(1, 2);
    if ($rand == 1)
        $profile_pic = "../home/profile_pic/head_deep_blue.png";
    else if ($rand == 2)
        $profile_pic = "../home/profile_pic/head_emerald.png";
    $query = "INSERT INTO users(first_name, last_name, username, email, password, signup_date, profile_pic, user_closed, friend_array, user_role) ";
    $query .= "VALUES('{$fname}', '{$lname}', '{$username}', '{$email}', '{$password}', '{$date}', '$profile_pic', 'no', ',', 'subscriber') ";
    $register_user_query = mysqli_query($connection, $query);
    confirmQuery($register_user_query);
}

function login_user($username, $password)
{
    global $connection;
    $username = trim($username);
    $password = trim($password);

    $username = mysqli_real_escape_string($connection, $username);
    $password = mysqli_real_escape_string($connection, $password);

    $query = "SELECT * FROM users WHERE username = '{$username}' ";
    $select_user_query = mysqli_query($connection, $query);
    confirmQuery($select_user_query);
    while ($row = mysqli_fetch_array($select_user_query)) {
        $db_user_id = $row['id'];
        $db_user_fname = $row['first_name'];
        $db_user_lname = $row['last_name'];
        $db_username = $row['username'];
        $db_user_email = $row['email'];
        $db_user_password = $row['password'];
        $db_user_role = $row['user_role'];

        if (password_verify($password, $db_user_password)) {
            $_SESSION['id'] = $db_user_id;
            $_SESSION['first_name'] = $db_user_fname;
            $_SESSION['last_name'] = $db_user_lname;
            $_SESSION['username'] = $db_username;
            $_SESSION['email'] = $db_user_email;
            $_SESSION['user_role'] = $db_user_role;
            redirect("/erchat/home");
        } else {
            return false;
        }
    }
    return true;
}

function ifItIsMethod($method = null)
{
    if ($_SERVER['REQUEST_METHOD'] == strtoupper($method)) {
        return true;
    }
    return false;
}

function checkIfIsLoggedInAndRedirect($redirectLocation = null)
{
    if (isLoggedIn()) {
        redirect($redirectLocation);
    }
}


// Change Title
function ch_title($title)
{
    $output = ob_get_contents();
    if (ob_get_length() > 0) {
        ob_end_clean();
    }
    $patterns = array("/<title>(.*?)<\/title>/");
    $replacements = array("<title>$title</title>");
    $output = preg_replace($patterns, $replacements, $output);
    echo $output;
}


// Url
function check_https() {
    return    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || $_SERVER['SERVER_PORT'] == 443;
}

// update details in database function
function update_details($first_name, $last_name, $email)
{
    global $connection;
    $first_name = mysqli_real_escape_string($connection, $first_name);
    $last_name = mysqli_real_escape_string($connection, $last_name);
    $email = mysqli_real_escape_string($connection, $email);
    $query = "UPDATE users SET first_name = '{$first_name}', last_name = '{$last_name}', email = '{$email}' WHERE id = {$_SESSION['id']}";
    $update_query = mysqli_query($connection, $query);
    confirmQuery($update_query);
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['email'] = $email;
}
