<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	//Auto Loading Going to hard code it a bit sorry younger Sean I promise you were more versitile than I am now.
	echoFiles();

	function echoFiles(){
		$scripts = list_files('./src', 'js');
		$sprites = list_files('./src', 'png');

		$version = rand();
		echo "<script src='lib/client/connect.js?v={$version}'></script>";
		echo "<script src='lib/client/scr_core.js?v={$version}'></script>";

		foreach($scripts as $script){
			echo "<script src='{$script}?v={$version}'></script>";
		}

		foreach($sprites as $sprite){
			$info = pathinfo($sprite)['filename'];
			$info = explode(' ', $info);
			
			$name = explode('/', $info[0]);
			$name = end($name);
			echo "<img data-name='{$name}' data-frames='{$info[1]}' data-width='{$info[2]}' data-height='{$info[3]}' src='{$sprite}?v={$version}'>";
		}
	}

	function list_files($dir, $extension = ''){
		$files = [];
		$readDir = opendir($dir);
		
		while(($file = readdir($readDir)) == true){
			$pathToFile = $dir . '/' . $file;
			
			if( is_system_file($file) ){ 
				continue; 
			}
			
			if( is_dir($pathToFile)){
					$filesInDir = list_files($pathToFile, $extension);
					$files = array_merge($files, $filesInDir);
					continue;
			}
			
			if($extension == '' || pathinfo($file, PATHINFO_EXTENSION) == $extension){
				array_push($files, $pathToFile);
			}
		}
		return $files;
	}

	function is_system_file($fileName){
		return $fileName == ".." || $fileName == ".";
	}
?>