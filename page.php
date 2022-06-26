<?php
	session_start();
	
	require_once "../../cnf.php";
    require_once "fnc_general.php";
    require_once "fnc_user.php";
	require_once "fnc_gallery.php";
	$notice = null;
	$email = null;
	$email_error = null;
	$password_error = null;
	
	
	//piltide kataloog
	$photo_dir = "pildid/";
	$all_files = read_dir_content($photo_dir);
	//var_dump($all_files);
	$allowed_photo_types = ["image/jpeg", "image/png"];
	$photo_files = check_if_photo($all_files, $photo_dir, $allowed_photo_types);
	//näitame kolme juhuslikku fotot!
	$photo_count = count($photo_files);
	$photo_count_limit = 3;
	if($photo_count < 3){
		$photo_count_limit = $photo_count;
	}
	$random_photo_files = array_rand($photo_files, $photo_count_limit);
	//echo $photo_count;
	//$random_num = mt_rand(0, $photo_count - 1);
	//$photo_html = "\n" .'<img src="' .$photo_dir .$photo_files[$random_num] .'" alt="Haapsalu kolledž"  class="photoframe">' ."\n";
	$photo_html = "";
	foreach ($random_photo_files as $file){
	$photo_html .= "\n" .'<img src="' .$photo_dir .$photo_files[$file] .'" alt="Haapsalu kolledž"  class="photoframe">' ."\n";
	}
	
	
	$full_time_now = date("d.m.Y H:i:s");
	$weekday_now = date("N");
	//echo $weekday_now;
	$weekday_names_et = ["esmaspäev", "teisipäev", "kolmapäev", "neljapäev", "reede", "laupäev", "pühapäev"];
	//echo $weekday_names_et[$weekday_now - 1];
	$day_category = "lihtsalt päev";
	if($weekday_now <= 5){
		$day_category = "kooli- või tööpäev";
	} else {
		$day_category = "normaalsete inimeste puhkepäev";
	}
	
	$part_of_day = "hägune aeg";
	$hour_now = date("H");
	if($hour_now < 6 or $hour_now >= 23){
			$part_of_day = "uneaeg";
		}
		if($hour_now >= 6 and $hour_now < 8){
			$part_of_day = "hommik";
		}
		if($hour_now >= 8 and $hour_now < 18){
			$part_of_day = "aktiivne aeg";
		}
		if($hour_now >= 18 and $hour_now < 23){
			$part_of_day = "õhtu";
		}
	
	$semester_begin = new DateTime("2022-1-31");
	$semester_end = new DateTime("2022-6-30");
	$semester_duration = $semester_begin->diff($semester_end);
	//echo $semester_duration;
	$semester_duration_days = $semester_duration->format("%r%a");
	//echo $semester_duration_days;
	$from_semester_begin = $semester_begin->diff(new DateTime("now"));
	$from_semester_begin_days = $from_semester_begin->format("%r%a");
	
	if($from_semester_begin_days > 0){
		if($from_semester_begin_days <= $semester_duration_days){
			$semester_meter = "\n" .'<p>Semester edeneb: <meter min="0" max="' .$semester_duration_days .'" value="' .$from_semester_begin_days .'"></meter>.</p>' ."\n";
		} else {
			$semester_meter = "\n <p>Semester on lõppenud!</p> \n";
		}
	} elseif($from_semester_begin_days === 0) {
		$semester_meter = "\n <p>Semester algab täna!</p> \n";
	} else {
		$semester_meter = "\n <p>Semestri alguseni on jäänud " . (abs($from_semester_begin_days) + 1) ." päeva!</p> \n";
	}
	
	
	//kontrollime sisestust sisselogimiseks
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if(isset($_POST["login_submit"])){
			//email
            if(isset($_POST["email_input"]) and !empty($_POST["email_input"])){
                $email = test_input(filter_var($_POST["email_input"], FILTER_VALIDATE_EMAIL));
                if(empty($email)){
                    $email_error = "Palun sisesta oma e-posti aadress!";
                }
            } else {
                $email_error = "Palun sisesta oma e-posti aadress!";
            }
			
			//parool
            if(isset($_POST["password_input"]) and !empty($_POST["password_input"])){
                if(strlen($_POST["password_input"]) < 8){
                    $password_error = "Sisestatud salasõna on liiga lühike!";
                }
            } else {
                $password_error = "Palun sisesta salasõna!";
            }
			
			if(empty($email_error) and empty($password_error)){
				$notice = sign_in($email, $_POST["password_input"]);
			} else {
				$notice = $email_error ." " .$password_error;
			}
			
		}
	}
	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Uudiste ja piltide portaal</title>
    <link rel="stylesheet" type="text/css" href="styles/general.css">
	<script src="javascript/nav.js" defer></script>
</head>
<body>
	<header>
		<img id="banner" src="../media/pic/rif21_banner.png" alt="RIF21 bänner">
		<h1>Toivo Pärnpuu arendab veebi</h1>
		<details>
			<summary>Selle lehe mõte</summary>
			<p>See leht on loodud õppetöö raames ja ei sisalda tõsiseltvõetavat materjali!</p>
		</details>
		
        <hr>
	</header>
    <?php
	require_once "nav-pub.php";
	?>

	
	<hr>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="email" name="email_input" placeholder="email ehk kasutajatunnus" value="<?php echo $email; ?>">
        <input type="password" name="password_input" placeholder="salasõna">
        <input type="submit" name="login_submit" value="Logi sisse">
		<span><?php echo $notice; ?></span>
    </form>
    <p>Loo omale <a href="add_user.php">kasutajakonto</a></p>
    <hr>
        
	<main>
		<section>
			<h2>Uusim avalik foto</h2>
			<?php echo show_latest_photo(3); ?>
		</section>
<?php
	require_once "pagefooter.php";
?>
