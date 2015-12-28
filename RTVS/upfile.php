<?php include 'VideoEngine.php';

// echo "upfile.php";

 $searchResults = null;

 if($_SERVER["REQUEST_METHOD"] == "POST"){
 $imgfilepath = $imgtype="";
 $uploaddir = getcwd().'/upfile/';

 if (is_dir($uploaddir) && is_writable($uploaddir)) {
      $videofilepath = $uploaddir.$_FILES['videofile']['name'];
      $videotype = $_FILES['videofile']['type'];
      if(move_uploaded_file($_FILES['videofile']['tmp_name'], $videofilepath)){
        //        print_r($_FILES);
//        echo "\n success!<br>";

        $searchResults = videoSearch($_FILES['videofile']['name']);
		foreach($searchResults as $v2){
			echo $v2->videoId." ".$v2->imgPath." ".$v2->videoPath."\r\n";
        }
		}
        else{
                echo "\nfile upload failed<br>";
        }
 } else {

    echo "Upload directory is not writable, or does not exist.<br>";
 }

}


?>
