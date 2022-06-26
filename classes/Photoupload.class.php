<?php
	class Photoupload {
		private $photo_to_upload;
		private $file_type;//alguses saadame parameetrina, hiljem teeb klass selle ise kindlaks
		private $temp_image;
		private $new_temp_image;
		
		function __construct($photo_to_upload, $file_type){
			$this->photo_to_upload = $photo_to_upload;
			$this->file_type = $file_type;
			//echo "Fotoga kõik kombes!";
			//var_dump($this->photo_to_upload);
			$this->temp_image = $this->create_image_from_file($this->photo_to_upload["tmp_name"], $this->file_type);
		}
		
		function __destruct(){
			if(isset($this->temp_image)){
				imagedestroy($this->temp_image);
			}
		}
		
		private function create_image_from_file($file, $file_type){
			$temp_image = null;
			if($file_type == "jpg"){
				$temp_image = imagecreatefromjpeg($file);
				echo "korras!";
			}
			if($file_type == "png"){
				$temp_image = imagecreatefrompng($file);
			}
			if($file_type == "gif"){
				$temp_image = imagecreatefromgif($file);
			}
			return $temp_image;
		}
		
		function resize_photo($w, $h, $keep_orig_proportion = true){
			$image_w = imagesx($this->temp_image);
			$image_h = imagesy($this->temp_image);
			$new_w = $w;
			$new_h = $h;
			$cut_x = 0;
			$cut_y = 0;
			$cut_size_w = $image_w;
			$cut_size_h = $image_h;
			
			if($w == $h){
				if($image_w > $image_h){
					$cut_size_w = $image_h;
					$cut_x = round(($image_w - $cut_size_w) / 2);
				} else {
					$cut_size_h = $image_w;
					$cut_y = round(($image_h - $cut_size_h) / 2);
				}	
			} elseif($keep_orig_proportion){//kui tuleb originaaproportsioone säilitada
				if($image_w / $w > $image_h / $h){
					$new_h = round($image_h / ($image_w / $w));
				} else {
					$new_w = round($image_w / ($image_h / $h));
				}
			} else { //kui on vaja kindlasti etteantud suurust, ehk pisut ka kärpida
				if($image_w / $w < $image_h / $h){
					$cut_size_h = round($image_w / $w * $h);
					$cut_y = round(($image_h - $cut_size_h) / 2);
				} else {
					$cut_size_w = round($image_h / $h * $w);
					$cut_x = round(($image_w - $cut_size_w) / 2);
				}
			}
				
			//loome uue ajutise pildiobjekti
			$this->new_temp_image = imagecreatetruecolor($new_w, $new_h);
			//säilitame vajadusel läbipaistvuse (png ja gif piltide jaoks
			imagesavealpha($this->new_temp_image, true);
			$trans_color = imagecolorallocatealpha($this->new_temp_image, 0, 0, 0, 127);
			imagefill($this->new_temp_image, 0, 0, $trans_color);
			
			imagecopyresampled($this->new_temp_image, $this->temp_image, 0, 0, $cut_x, $cut_y, $new_w, $new_h, $cut_size_w, $cut_size_h);
		}
		
		public function add_watermark($watermark){
			//praegu selline usalduslik, eeldame, et on pilt
			$watermark_file_type = strtolower(pathinfo($watermark, PATHINFO_EXTENSION));
			$watermark_image = $this->create_image_from_file($watermark, $watermark_file_type);
			$watermark_w = imagesx($watermark_image);
			$watermark_h = imagesy($watermark_image);
			$watermark_x = imagesx($this->new_temp_image) - $watermark_w - 10;
			$watermark_y = imagesy($this->new_temp_image) - $watermark_h - 10;
			imagecopy($this->new_temp_image, $watermark_image, $watermark_x, $watermark_y, 0, 0, $watermark_w, $watermark_h);
			imagedestroy($watermark_image);
		}
		
		function save_image($target){
			$notice = null;
			if($this->file_type == "jpg"){
				if(imagejpeg($this->new_temp_image, $target, 95)){
					$notice = "salvestamine õnnestus!";
				} else {
					$notice = "salvestamisel tekkis tõrge!";
				}
			}
			if($this->file_type == "png"){
				if(imagepng($this->new_temp_image, $target, 6)){
					$notice = "salvestamine õnnestus!";
				} else {
					$notice = "salvestamisel tekkis tõrge!";
				}
			}
			if($this->file_type == "gif"){
				if(imagegif($this->new_temp_image, $target)){
					$notice = "salvestamine õnnestus!";
				} else {
					$notice = "salvestamisel tekkis tõrge!";
				}
			}
			imagedestroy($this->new_temp_image);
			return $notice;
		}
		
		public function move_orig_photo($target){
			$notice = null;
			if(move_uploaded_file($this->photo_to_upload["tmp_name"], $target)){
				$notice .= " Originaalfoto laeti üles!";
			} else {
				$notice .= " Originaalfoto üleslaadimine ei õnnestunud!";
			}
			return $notice;
		}
		
		
	}//class lõppeb