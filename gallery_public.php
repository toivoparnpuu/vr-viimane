<?php
    require_once "use_session.php";	
	require_once "../../cnf.php";
    //require_once "fnc_general.php";
	require_once "fnc_gallery.php";
	
	$privacy = 2;
    
    $page = 1;
    $limit = 10;
    $photo_count = count_photos($privacy);
    //kontrollime, mis lehel oleme ja kas selline leht on võimalik
    if(!isset($_GET["page"]) or $_GET["page"] < 1){
        $page = 1;
    } elseif(round($_GET["page"] - 1) * $limit >= $photo_count){
        $page = ceil($photo_count / $limit);
    } else {
        $page = $_GET["page"];
    }
	
	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $_SESSION["firstname"] ." " .$_SESSION["lastname"]; ?> teeb veebi</title>
	<link rel="stylesheet" type="text/css" href="styles/general.css">
	<link rel="stylesheet" type="text/css" href="styles/gallery.css">
	<link rel="stylesheet" type="text/css" href="styles/modal.css">
	<script src="javascript/modal.js" defer></script>
</head>
<body>
	<header>
		<img id="banner" src="../media/pic/rif21_banner.png" alt="RIF21 bänner">
		<h1>Tere <?php echo $_SESSION["firstname"]; ?>!</h1>
		<details>
			<summary>Selle lehe mõte</summary>
			<p>See leht on loodud õppetöö raames ja ei sisalda tõsiseltvõetavat materjali!</p>
		</details>
		
		<hr>
	</header>
	
	<?php
		require_once "nav-user.php";
	?>
	<main>
	<section>
		<!--modaalaken fotode näitamiseks-->
		<dialog id="modal" class="modalarea">
			<span id="modalclose" class="modalclose">&times;</span>
			<div class="modalhorizontal">
				<div class="modalvertical">
					<p id="modalcaption"></p>
					<img id="modalimage" src="empty.png" alt="Galeriipilt">
					
					<br>
					<input id="rate1" name="rating" type="radio" value="1"><label for="rate1">1</label>
					<input id="rate2" name="rating" type="radio" value="2"><label for="rate2">2</label>
					<input id="rate3" name="rating" type="radio" value="3"><label for="rate3">3</label>
					<input id="rate4" name="rating" type="radio" value="4"><label for="rate4">4</label>
					<input id="rate5" name="rating" type="radio" value="5"><label for="rate5">5</label>
					<button id="storeRating" type="button">Salvesta hinne</button>
					<br>
					<p id="avgrating"></p>
					
				</div>
			</div>
		</dialog>

		<h2>Avalike fotode galerii</h2>
		<p>
			<?php
				//Eelmine leht | Järgmine leht
				//<span>Eelmine leht</span> | <span><a href="?page=2">Järgmine leht</a></span>
				if($page > 1){
					echo '<span><a href="?page=' .($page - 1) .'">Eelmine leht</a></span>';
				} else {
					echo "<span>Eelmine leht</span>";
				}
				echo " | ";
				if($page * $limit < $photo_count){
					echo '<span><a href="?page=' .($page + 1) .'">Järgmine leht</a></span>';
				} else {
					echo "<span>Järgmine leht</span>";
				}
			?>
		</p>
		<div class="gallery">
			
			<?php echo read_public_photo_thumbs($privacy, $page, $limit); ?>
		</div>
	</section>
	<?php
		require_once "pagefooter.php";
	?>