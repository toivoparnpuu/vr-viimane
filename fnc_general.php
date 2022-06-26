<?php
	function read_dir_content($dir){
		$all_files = array_slice(scandir($dir), 2);
		return $all_files;
	}
	
	function check_if_photo($all_files, $photo_dir, $allowed_photo_types){
		$photo_files = [];
		foreach ($all_files as $file){
			$file_info = getimagesize($photo_dir .$file);
			if(isset($file_info["mime"])){
				if(in_array($file_info["mime"], $allowed_photo_types)){
					array_push($photo_files, $file);
				}
			}
		}
		return $photo_files;
	}

	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}