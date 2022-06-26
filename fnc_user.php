<?php

function sign_up($first_name, $surname, $gender, $birth_date, $email, $password){
	$notice = 0;
	$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
	$conn->set_charset("utf8");
	$stmt = $conn->prepare("INSERT INTO vr22_users (firstname, lastname, birthdate, gender, email, password) VALUES (?,?,?,?,?,?)");
	echo $conn->error;
	//krüpteerime salasõna
	//$options = ["cost"=>12, "salt"=>...];
	$options = ["cost"=>12];
	$pwd_hash = password_hash($password, PASSWORD_BCRYPT, $options);
	$stmt->bind_param("sssiss", $first_name, $surname, $birth_date, $gender, $email, $pwd_hash);
	if($stmt->execute()){
		$notice = 1;
	}
	$stmt->close();
	$conn->close();
	return $notice;
}

function sign_in($email, $password){
	$notice = 0;
	$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
	$conn->set_charset("utf8");
	$stmt = $conn->prepare("SELECT id, firstname, lastname, password FROM vr22_users WHERE email = ?");
	echo $conn->error;
	$stmt->bind_param("s", $email);
	$stmt->bind_result($id_from_db, $firstname_from_db, $lastname_from_db, $password_from_db);
	$stmt->execute();
	if($stmt->fetch()){
		//kas parool õige
		if(password_verify($password, $password_from_db)){
			//olemegi sees
			$notice = 1;
			$_SESSION["user_id"] = $id_from_db;
			$_SESSION["firstname"] = $firstname_from_db;
			$_SESSION["lastname"] = $lastname_from_db;
			header("Location: home.php");
			$stmt->close();
			$conn->close();
			exit();
		}
	}
	
	$stmt->close();
	$conn->close();
	return $notice;
}