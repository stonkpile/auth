<?php
require __DIR__ . '/../config.php';

$payload = array();

if (isset($_SERVER['HTTP_X_ACCESS_TOKEN'])) {
	try {	
		$token = $_SERVER['HTTP_X_ACCESS_TOKEN'];
		$payload = JWT::decode($token, JWT_SECRET, JWT_SIGNING_ALG);
		
		$newToken = $jwt_payload;
		$newToken->iat = time();
		$newToken->exp = time()+JWT_TOKEN_LIFETIME;

		$newToken = JWT::encode($newToken, JWT_SECRET);

		$payload = array(
			'payload' => $payload,
			'token' => $newToken
    );

	} catch(\Exception $error) {
		$response['status'] = false;
		$response['payload'] = ['message' => $error->getMessage()];
		header("Content-Type: application/json");
		echo json_encode($response);
		exit();
  }
} else {
	$response['status'] = false;
	$response['payload'] = ['message' => 'token is required'];
	header("Content-Type: application/json");
	echo json_encode($response);
	exit();
}
