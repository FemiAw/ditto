<?php

require ('./resources/db/db_connect.php');
require ('./resources/db/db_query.php');

session_start();
$error='';

if ( isset($_POST['submit']) ) {
  if (empty($_POST['email']) || empty($_POST['password'])) {
    echo 'username or password is invalid';
  } else {

    $email = cleanInput($_POST['email']);
    $password = cleanInput($_POST['password']);
    $connection = db_connect();
    $query = db_query("SELECT * FROM `users` WHERE email = '$email' AND hashedPassword = '$password' ");

    if (mysqli_num_rows(db_query("SELECT * FROM `users` WHERE email = '$email' AND hashedPassword = '$password'")) === 1) {
      $_SESSION['login_user'] = $email; // Initializing Session
      header("location: ./public/php/home/home.php");
    } else {
      $error = "Username or Password is invalid";
    }
    mysqli_close($connection); // Closing Connection
  }
}

/*
A collection of functions to clean the input data.
*/
function cleanInput($input) {
  $input = stripslashes($input);
  // will add more
  return $input;
}
?>
