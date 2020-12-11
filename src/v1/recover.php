<?php
require __DIR__ . '/../config.php';

if (isset($_POST['email'])) {
  $connect = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
  $email = $connect->real_escape_string($_POST['email']);

  $check_query = "SELECT * FROM `".PREFIX."users` WHERE email='$email'";
  $check_result = $connect->query($check_query) or die($connect->error);
  $check_row = $check_result->fetch_assoc();

  $confirm = $check_row['confirm'];
  $pass = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)) )), 1, 10);
  $safepass = safepass($pass, $confirm);

  $update = "UPDATE `users` SET password='$safepass' WHERE email='$email'";

  if ($connect->query($update)) {
    $response['status'] = true;
    $response['payload'] = array('message' => 'password updated');

    $subject = 'Recover your account';
    $message = 'Hi, your new password is: ' . $pass;
    $headers = 'From: webmaster@example.com' . "\r\n" .
               'Reply-To: webmaster@example.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    mail($email, $subject, $message, $headers);
  } else {
    $response['status'] = false;
    $response['payload'] = array('message' => $connect->error);
  }

  $connect->close();
} else {
  $response['status'] = false;
  $response['payload'] = array('message' => 'Confirm key not matching any id');
}

header("Content-Type: application/json");
echo json_encode($response);
