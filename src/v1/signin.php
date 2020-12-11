<?php 
require __DIR__ . '/../config.php';
require __DIR__ . '/../jwt/jwt.php';

if (isset($_POST['email']) && isset($_POST['pass'])) {
  $response = array();
  $connect = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

  $email = $connect->real_escape_string($_POST['email']);
  $pass = $connect->real_escape_string($_POST['pass']);
  
  $check_query = "SELECT * FROM `users` WHERE email='$email'";
  $check_result = $connect->query($check_query) or die($connect->error);
  $check_row = $check_result->fetch_assoc();

  $confirm = $check_row['confirm'];
  $active = $check_row['active'];

  if ($active == 'true') {
    $safepass = safepass($pass, $confirm);
    $query = "SELECT * FROM `users` WHERE email='$email' AND password='$safepass'";
    $result = $connect->query($query) or die($connect->error);
    $row = $result->fetch_assoc();

    if ($email == $row['email'] && $safepass == $row['pass']) {
      $result = $connect->query($update) or die($connect->error);

      $payload = array(
        'email' => $email,
        'iat' => time(),
        'exp' => time() + JWT_TOKEN_LIFETIME
      );

      $jwt = JWT::encode($payload, JWT_SECRET);

      $response['status'] = true;
      $response['jwt'] = array(
        'payload' => $payload,
        'token' => $jwt
      );
    } else {
      $response['status'] = false;
      $response['payload'] = array('message' => 'invalid username and password');
    }

    $connect->close();
  } else {
    $response['status'] = false;
    $response['payload'] = array('message' => 'you must confirm');
  }
} else {
  $response['status'] = false;
  $response['payload'] = array('message' => 'username & password is required');
}

header("Content-Type: application/json");
echo json_encode($response);
