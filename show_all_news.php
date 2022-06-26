<?php
	require_once "use_session.php";	
	require_once "../../cnf.php";
	require_once "fnc_news.php";

?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $_SESSION["firstname"] ." " .$_SESSION["lastname"] ?> teeb veebi</title>
    <link rel="stylesheet" type="text/css" href="styles/general.css">
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
    
	<?php
		require_once "nav-user.php";
	?>
        
	<main>
		<section>
			<h2>Kõik uudised</h2>
			<?php echo all_news(); ?>
		</section>
<?php
	require_once "pagefooter.php";
?>
