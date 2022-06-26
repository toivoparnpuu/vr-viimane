<?php
	
	function save_news($title, $news, $expire){
		$notice = null;
		//$userid = 1;
		//loon andmebaasiühenduse
		//kasutan globaalseid muutujaid, need, mis on loodud väljaspool funktsiooni: $GLOBALS["server_host"]
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		//määran suhtlemise kooditabeli
		$conn->set_charset("utf8");
		//valmistame ette SQL keeles käsu andmete lisamiseks andmetabelisse
		//   INSERT INTO vr22_news (title, content, expire, userid) VALUES (?,?,?,?)
		$stmt = $conn->prepare("INSERT INTO vr22_news (title, content, expire, userid) VALUES (?,?,?,?)");
		echo $conn->error;
		//s - string, i - integer, d - decimal
		$stmt->bind_param("sssi", $title, $news, $expire, $_SESSION["user_id"]);
		if($stmt->execute()){
			$notice = "Uudis salvestatud!";
		} else {
			$notice = "Uuside salvestamisel tekkis viga: " .$stmt->error;
		}
		//lõpetan käsu
		$stmt->close();
		//sulgen ühenduse
		$conn->close();
		return $notice;
	}

	function update_news($id, $title, $news, $expire){
		$notice = null;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
		//valmistame ette SQL keeles käsu andmete lisamiseks andmetabelisse
		//   INSERT INTO vr22_news (title, content, expire, userid) VALUES (?,?,?,?)
		$stmt = $conn->prepare("UPDATE users SET title=?, content=?, expire=?, userid=? WHERE id=?");
		echo $conn->error;
		//s - string, i - integer, d - decimal
		$stmt->bind_param("sssii", $title, $news, $expire, $_SESSION["user_id"], $id);
		if($stmt->execute()){
			$notice = "Uudis uuendatud!";
		} else {
			$notice = "Uuside uuendamisel tekkis viga: " .$stmt->error;
		}
		//lõpetan käsu
		$stmt->close();
		//sulgen ühenduse
		$conn->close();
		return $notice;
	}
	
	function read_news($amount){
		$news_html = null;
		$today = date("Y-m-d");
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");

		$stmt = $conn->prepare("SELECT title, content, added, firstname, lastname FROM vr22_news JOIN vr22_users on vr22_news.userid = vr22_users.id WHERE deleted IS NULL AND expire >= ? ORDER BY vr22_news.id DESC LIMIT ?");
		echo $conn->error;
		$stmt->bind_param("si", $today, $amount);
		$stmt->bind_result($title_from_db, $content_from_db, $added_from_db, $firstname_from_db, $lastname_from_db);
		$stmt->execute();
		//$stmt->fetch();
		while($stmt->fetch()){
			$news_html .= "<h3>" .$title_from_db ."</h3> \n";
			$news_html .= "<p>Lisas: " .$firstname_from_db ." " .$lastname_from_db .", " .$added_from_db ."</p> \n";
			$news_html .= "<p>" .$content_from_db ."</p> \n";
		}
		$stmt->close();
		$conn->close();
		return $news_html;
	}

	function get_news_data($news_id){
		$news_list = array();
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");

		$stmt = $conn->prepare("SELECT id, title, content, expire FROM vr22_news WHERE id = ?");
				

		echo $conn->error;
		$stmt->bind_param("i", $news_id);
		$stmt->bind_result($id_from_db, $title_from_db, $content_from_db, $expire_from_db);
		$stmt->execute();
		while($stmt->fetch()){
			
			array_push($news_list, $id_from_db, $title_from_db, $content_from_db, $expire_from_db); 
		}
		$stmt->close();
		$conn->close();
		return $news_list;
	}

	function all_news(){
		$news_html = null;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
	

		$stmt = $conn->prepare("SELECT id, title, content, added FROM vr22_news");
		echo $conn->error;
		$stmt->bind_result($id_from_db, $title_from_db, $content_from_db, $added_from_db);
		$stmt->execute();

		while($stmt->fetch()){
			$news_html .= "<h3>" .$title_from_db ."</h3> \n";
			$news_html .= "<p>Lisatud: " .$added_from_db ."</p> \n";
			$news_html .= "<p>" .$content_from_db ."</p> \n";
			$news_html .= "<a href='add_news.php?edit_news=" .$id_from_db ."'>muuda uudist</a> \n";
		}
		$stmt->close();
		$conn->close();
		return $news_html;
	}