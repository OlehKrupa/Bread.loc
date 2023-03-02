<?php 
require_once "config.php";

$fields = ["user","password"];

if (!empty($_POST)){
	$error = [];

	if($_POST['user']!==DB_USER){
		$error["user"]="Неправильне ім'я користувача";
	}

	if($_POST['password']!==DB_USER_PASS){
		$error["password"]="Неправильний пароль!";
	}

	foreach ($_POST as $k => $v) {
		if (in_array($k, $fields) && empty($v)){
			$error[$k]="Поле має бути заповнене!";
		}
	}

	if (empty($error)){
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "http://test.loc/Api.loc/");
		curl_setopt($ch, CURLOPT_POST, 1);
		$data = array(
			'user' => $_POST['user'],
			'password' => $_POST['password']
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($response, true);

		if ($result['success']) {
			echo "Response message: " . $result['message'];
		} else {
			echo "Error message: " . $result['message'];
		}

		/*
		$_SESSION['user']=htmlspecialchars($_POST['user']);
		$_SESSION['logined']=1;
		
		header("location: /index.php");
		die();
		*/
	}
}

require_once TEMPLATES_PATH."login.html";
?>