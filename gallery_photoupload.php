<?php
    require_once "use_session.php";	
	require_once "../../cnf.php";
    require_once "fnc_general.php";
	require_once "fnc_photoupload.php";
	require_once "classes/Photoupload.class.php";
	
    $photo_error = null;
    $photo_upload_notice = null;
	$alt_text = null;
	$privacy = 1;
    $file_name = null;
	$file_type = null;
	//muutujad, mis võiks olla conf failis
	$photo_upload_size_limit = 1024 * 1024 * 1.5;
	$gallery_photo_orig_folder = "gallery_upload_orig/";
	$gallery_photo_normal_folder = "gallery_upload_normal/";
	$gallery_photo_thumb_folder = "gallery_upload_thumb/";
	$photo_name_prefix = "vr_";
	$normal_photo_max_width = 600;
	$normal_photo_max_height = 400;
	$thumbnail_width = $thumbnail_height = 100;
	$watermark = "vr_watermark.png";
	
	if($_SERVER["REQUEST_METHOD"] === "POST"){
		if(isset($_POST["photo_submit"])){
			//var_dump($_POST);
			//var_dump($_FILES);
			//kas on olemas pilt
			if(isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])){
				//kui pilt valitud
				//kas on foto
				$image_check = getimagesize($_FILES["photo_input"]["tmp_name"]);
				if($image_check !== false){
					if($image_check["mime"] == "image/jpeg"){
						$file_type = "jpg";
					}
					if($image_check["mime"] == "image/png"){
						$file_type = "png";
					}
					if($image_check["mime"] == "image/gif"){
						$file_type = "gif";
					}
					//tuleks kontrollida, kas on lubatud formaat (jpg, png, gif või osa neist)
				} else {
					$photo_error = "Valitud fail pole foto!";
				}
				
				//kas lubatud suurusega
				if($photo_error == null and $_FILES["photo_input"]["size"] > $photo_upload_size_limit){
					$photo_error = "Valitud fail on liiga suur!";
				}
				
				//võiks "puhastada" alt teksti
				//võiks kontrollida privacy
				
				//kui kõik korras, siis laeme üles
				if($photo_error == null){
					
					//võtame kasutusele klassi
					$upload = new Photoupload($_FILES["photo_input"], $file_type);
					
					//loon uue failinime
					$file_name = create_filename($photo_name_prefix, $file_type);
					
					//suuruse muutmine
					//$my_temp_image = create_image($_FILES["photo_input"]["tmp_name"], $file_type);
					//$my_normal_image = resize_photo($my_temp_image, $normal_photo_max_width, $normal_photo_max_height);
					
					//suuruse muutmine klassiga
					$upload->resize_photo($normal_photo_max_width, $normal_photo_max_height);
					
					//lisan vesimärgi
					$upload->add_watermark($watermark);
					
					//salvestame vähendatud pildifaili
					//$photo_upload_notice = "Normaalsuuruses " .save_image($my_normal_image, $gallery_photo_normal_folder .$file_name, $file_type);
					$photo_upload_notice = "Normaalsuuruses " .$upload->save_image($gallery_photo_normal_folder .$file_name);
					
					//thumbnail ka
					//$my_thumb_image = resize_photo($my_temp_image, $thumbnail_width, $thumbnail_height);
					//$photo_upload_notice .= " Pisipildi " .save_image($my_thumb_image, $gallery_photo_thumb_folder .$file_name, $file_type);
					$upload->resize_photo($thumbnail_width, $thumbnail_height);
					$photo_upload_notice .= " Pisipildi " .$upload->save_image($gallery_photo_thumb_folder .$file_name);
					
					//kopeerime originaali soovitud kohta
					//move_uploaded_file($_FILES["photo_input"]["tmp_name"], $gallery_photo_orig_folder .$file_name);
					$photo_upload_notice .= $upload->move_orig_photo($gallery_photo_orig_folder .$file_name);
					
					//talletame andmebaasi
					$photo_upload_notice .= " " .store_photo_data($file_name, $_POST["alt_input"], $_POST["privacy_input"]);
					
					//tühjendame mälu
					//imagedestroy($my_temp_image);
					//imagedestroy($my_normal_image);
					//imagedestroy($my_thumb_image);
					//laseme klassi minna...
					unset($upload);
				}
				
				
			} else {
				$photo_error = "Pildifaili pole valitud!";
			}//kas pilt valitud
			
			if($photo_upload_notice == null){
				$photo_upload_notice = $photo_error;
			}
		}
	}
    
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $_SESSION["firstname"] ." " .$_SESSION["lastname"]; ?> teeb veebi</title>
	<link rel="stylesheet" type="text/css" href="styles/general.css">
	<script src="javascript/fileCheck.js" defer></script>
</head>
<body>
	<header>
		<img id="banner" src="../media/pic/rif21_banner.png" alt="RIF21 bänner">
		<h1><?php echo $_SESSION["firstname"] ." " .$_SESSION["lastname"]; ?> arendab veebi</h1>
		<details>
			<summary>Selle lehe mõte</summary>
			<p>See leht on loodud õppetöö raames ja ei sisalda tõsiseltvõetavat materjali!</p>
		</details>
		
		<hr>
	</header>
	
	<nav>
		<h2>Olulised lingid</h2>
		<ul>
			<li><a href="home.php">Avaleht</a></li>
			<li><a href="?logout=1">Logi välja!</a></li>
			<li><a href="https://www.tlu.ee/haapsalu">Tallinna Ülikooli Haapsalu kolledž</a></li>
			
		</ul>
	</nav>
	<main>
	<section>
			

		<h2>Foto üleslaadimine</h2>
		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
			<label for="photo_input"> Vali pildifail! </label>
			<input type="file" name="photo_input" id="photo_input">
			<br>
			<label for="alt_input">Alternatiivtekst (alt): </label>
			<input type="text" name="alt_input" id="alt_input" placeholder="alternatiivtekst" value="<?php echo $alt_text; ?>">
			<br>
			<input type="radio" name="privacy_input" id="privacy_input_1" value="1" <?php if($privacy == 1){echo " checked";} ?>>
			<label for="privacy_input_1">Privaatne (ainult mina näen)</label>
			<br>
			<input type="radio" name="privacy_input" id="privacy_input_2" value="2" <?php if($privacy == 2){echo " checked";} ?>>
			<label for="privacy_input_2">Sisseloginud kasutajatele</label>
			<br>
			<input type="radio" name="privacy_input" id="privacy_input_3" value="3" <?php if($privacy == 3){echo " checked";} ?>>
			<label for="privacy_input_3">Avalik (kõik näevad)</label>
			<br>
			<input type="submit" name="photo_submit" id="photo_submit" value="Lae pilt üles">
		</form>
		<span id="notice"><?php echo $photo_upload_notice; ?></span>
	</section>
	<?php
		require_once "pagefooter.php";
	?>