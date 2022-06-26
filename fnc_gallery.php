<?php
	$gallery_photo_orig_folder = "gallery_upload_orig/";
	$gallery_photo_normal_folder = "gallery_upload_normal/";
	$gallery_photo_thumb_folder = "gallery_upload_thumb/";
    
	function count_photos($privacy){
        $photo_count = 0;
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
        $stmt = $conn->prepare("SELECT COUNT(id) FROM vr22_photos WHERE privacy >= ? AND deleted IS NULL");
        echo $conn->error;
        $stmt->bind_param("i", $privacy);
        $stmt->bind_result($count_from_db);
        $stmt->execute();
        if($stmt->fetch()){
            $photo_count = $count_from_db;
        }
        $stmt->close();
		$conn->close();
		return $photo_count;
    }
	
	function read_public_photo_thumbs($privacy, $page, $limit){
        $skip = ($page - 1) * $limit;
        $photo_html = null;
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
        $stmt = $conn->prepare("SELECT vr22_photos.id, vr22_photos.filename, vr22_photos.alttext, vr22_users.firstname, vr22_users.lastname FROM vr22_photos JOIN vr22_users ON vr22_photos.userid = vr22_users.id WHERE vr22_photos.privacy >= ? AND vr22_photos.deleted IS NULL GROUP BY vr22_photos.id ORDER BY vr22_photos.id DESC LIMIT ?, ?");
        echo $conn->error;
        $stmt->bind_param("iii", $privacy, $skip, $limit);
        $stmt->bind_result($id_from_db, $filename_from_db, $alttext_from_db, $firstname_from_db, $lastname_from_db);
        $stmt->execute();
        while($stmt->fetch()){

			
            $photo_html .= '<div class="thumbgallery">' ."\n";
            $photo_html .= '<img src="' .$GLOBALS["gallery_photo_thumb_folder"] .$filename_from_db .'" alt="';
            if(empty($alttext_from_db)){
                $photo_html .= "Üleslaetud foto";
            } else {
                $photo_html .= $alttext_from_db;
            }
            $photo_html .= '" class="thumbs" data-id="' .$id_from_db .'" data-filename="' .$filename_from_db .'">' ."\n";
            $photo_html .= "<p>" .$firstname_from_db ." " .$lastname_from_db ."</p> \n";
            $photo_html .= "</div> \n";
        }
        if(empty($photo_html)){
            $photo_html = "<p>Kahjuks pole ühtegi avalikku fotot üles laetud!</p>";
        }
        $stmt->close();
		$conn->close();
		return $photo_html;
    }
	
	    function show_latest_photo($privacy = 3){
        $photo_html = null;
        $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
        $stmt = $conn->prepare("SELECT filename, alttext FROM vr22_photos WHERE id = (SELECT MAX(id) FROM vr22_photos WHERE privacy = ? AND deleted IS NULL)");
        echo $conn->error;
        $stmt->bind_param("i", $privacy);
        $stmt->bind_result($filename_from_db, $alttext_from_db);
        $stmt->execute();
        if($stmt->fetch()){
            //<img src="kataloog/fail" alt="tekst">
            $photo_html = '<img src="' .$GLOBALS["gallery_photo_normal_folder"] .$filename_from_db .'" alt="';
            if(empty($alttext_from_db)){
                $photo_html .= "Üleslaetud foto";
            } else {
                $photo_html .= $alttext_from_db;
            }
            $photo_html .= '">' ."\n";
        } else {
            $photo_html = "<p>Kahjuks pole ühtegi avalikku fotot üles laetud!</p>";
        }
        $stmt->close();
		$conn->close();
		return $photo_html;
    }