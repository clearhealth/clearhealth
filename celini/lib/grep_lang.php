<?php

	$start_file = 'en.php';
	$file_list=list_directory(dirname(__FILE__) . '/../../local/templates',"*.html");

	$print_file_counts=true;//this can help you find new templates that have not been translated!

	$hash_list = array();//The md5 sums mapped to translations
	$comment_hash_list = array();// for maintaining english in the comments
	$file_word_count = array();// for recording what files have been translated and how may items where in each file

	if(file_exists($start_file)){
		include($start_file);
		$hash_list = array_merge($GLOBALS['__LANG'],$hash_list);
	//	echo "reading from $start_file \n";	
	}

	foreach($file_list as $file){
		$fp = fopen($file, 'r');
		if(filesize($file)==0){
			//do nothing with this file!
		}else{

		$content = fread($fp, filesize($file));

		$regex = '!{l}(.*?){/l}!s';
		preg_match_all($regex, $content, $matches);
	//	echo "matching $file \n";
	
		foreach($matches[1] as $m) {
			 
			if(fnmatch("*<*",$m)){
				echo "you have an < in file $file \n";
				echo "This means that you did not properly close a {l} tag.\n";
				echo "Here is the resulting message...\n";
				echo "$m \n";
				exit();
			}
			$mymd5 = md5($m);
			if(!array_key_exists($mymd5,$hash_list)){ //Then this was not in the original file
			 	$hash_list[$mymd5]= ''; 
			}		
			$comment_hash_list[$mymd5] = $m; //so I always have an english copy
		}

		$file_word_count[$file]=count($matches[1]);// how many words where in this file
		}
	}
	
	echo "<?php \n";
	foreach($hash_list as $hash => $word){
		$comment = $comment_hash_list[$hash];
		echo '$GLOBALS[\'__LANG\'][\'' . $hash . '\'] = \'' . $word . '\'; ' . "//$comment\n";
	}
	if($print_file_counts){
		echo "/*\n";
	
		foreach($file_word_count as $filename => $count){
			echo "$filename translation count = $count\n";
		}
		echo "*/\n";
		echo "?> \n";
	}

   function list_directory($dir,$match) {
       $file_list = '';
       $stack[] = $dir;
       while ($stack) {
           $current_dir = array_pop($stack);
           if ($dh = opendir($current_dir)) {
               while (($file = readdir($dh)) !== false) {
                   if ($file !== '.' AND $file !== '..') {
                       $current_file = "{$current_dir}/{$file}";
                       if (is_file($current_file)) {
				if (fnmatch("*.html",$current_file))
                           		$file_list[] = "{$current_dir}/{$file}";
                       } elseif (is_dir($current_file)) {
                           $stack[] = $current_file;
                       }
                   }
               }
           }
       }
       return $file_list;
   }
?>
