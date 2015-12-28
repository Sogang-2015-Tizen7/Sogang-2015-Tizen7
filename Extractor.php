<!DOCTYPE HTML>
<html>
<head>
</head>
<body>

<?php

error_reporting(E_ALL);
$title = $overview = $imgname = $userfile="";

if($_SERVER["REQUEST_METHOD"] == "POST"){
        $title = test_input($_POST["title"]);
        $overview =  test_input($_POST["overview"]);
}

function test_input($data){

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
}
define("_IP",    "163.239.27.173");
define("_PORT",  "65000");
?>

<form name="form" method="POST" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  enctype="multipart/form-data"/>
<input type="hidden" name="MAX_FILE_SIZE" value="1024000000"/>
title <input type="text" name="title"/><br>
overview <input type="text" name="overview"/> <br>
image <input type="file" name="imgfile"/><br>
Video <input type="file" name="videofile"/><br>
<br>
<input type="submit" value="upload"/>
<input type="reset" value="reset"/>
</form>



<?php
$servername = "localhost";
$username = "root";
$password = "mlint1234";
$dbname = "MAM_DB";

class DNA{

        public $idx;
        public $r1;
        public $r2;
        public $r3;
        public $r4;
        public $g1;
        public $g2;
        public $g3;
        public $g4;
        public $b1;
        public $b2;
        public $b3;
        public $b4;
        public $diff1;
        public $diff2;
        public $diff3;
        public $diff4;
}




$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error){
        die("connection failed : ".$conn->connect_error);
}

if($title != ""){

 // uploads디렉토리에 파일을 업로드합니다.
 $imgfilepath = $imgtype="";
 $idx = 1;
 $counter=0;

 $uploaddir = getcwd().'/Storage/';
 if (is_dir($uploaddir) && is_writable($uploaddir)) {


	  $unique = mktime();
      $imgfilepath = $uploaddir."Image/vid_".$unique.".jpg";//.$_FILES['imgfile']['name'];
      $imgtype = $_FILES['imgfile']['type'];
      if(move_uploaded_file($_FILES['imgfile']['tmp_name'], $imgfilepath)){
                print_r($_FILES);
                echo "\n success!<br>";
        }
        else{
                echo "\nfile upload failed\n<br>";
        }

      $videofilepath = $uploaddir."Video/vid_".$unique.".mp4";//.$_FILES['videofile']['name'];
      $videotype = $_FILES['videofile']['type'];
      if(move_uploaded_file($_FILES['videofile']['tmp_name'], $videofilepath)){
                print_r($_FILES);
                echo "\n success!<br>";
				
	    $conn = new mysqli($servername, $username, $password, $dbname);
	    if($conn->connect_error){
        die("connection failed : ".$conn->connect_error);
                }

$sock      = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($sock, _IP, _PORT);
echo "CLIENT >> socket connect to "._IP.":"._PORT."\n";
$date = socket_read($sock, 4096);
echo "CLIENT >> this time is $date \n";
$str = "/var/www/html/ffmpeg/ffmpeg.exe -ss 00:00:01 -i ".$videofilepath." -vsync 0 -vf \"select='between(mod(n,10),9,9)'\" /var/www/html/Storage/snapshots/%d.jpg";

//$str = "50 Storage/Video/vid_".$unique.$_FILES['videofile']['name']." Storage/";
socket_write($sock,$str,strlen($str));

$data = socket_read($sock, 4096);
echo $data;

socket_close($sock);
echo "CLIENT >> socket closed.<br>";

        }
        else{
                echo "\nfile upload failed\n<br>";
        }

 } else {

    echo "Upload directory is not writable, or does not exist.\n<br>";
 }

$filepath = explode("html",$videofilepath);
$videofilepath = "http://mlcherry.sogang.ac.kr".$filepath[1];
$filepath = explode("html",$imgfilepath);
$imgfilepath = "http://mlcherry.sogang.ac.kr".$filepath[1];

$sql = "INSERT INTO dboVideo (title,overview,filename720,filename360,imgname)
VALUES('$title','$overview','$videofilepath','$videofilepath','$imgfilepath')";

//echo $title." ".$overview." ".$_FILES['videofile']['name']." ".$videofilepath." ".$videofilepath." ".$imgfilepath."<br>";
 if($conn->query($sql) === TRUE){

  echo "DB inserted!\n<br>";
   }
    $sql = "select id,filename720 from dboVideo order by id desc limit 5";

	 $res = $conn->query($sql);
	  while($row = $res->fetch_assoc()){
	  // if($row["filename720"] == $videofilepath)
		      $videoId = $row['id'];
//			  echo ($videoId);
			  break;
				   //}
	  }

  /* snashots*/
  $filename = "/var/www/html/Storage/snapshots/1.jpg";

  while(file_exists($filename) == 1){
	if($counter > 80)	break;
  //while($imgfilepath != NULL){

/*$sql = "INSERT INTO dboVideo (title,overview,filename720,filename360,imgname)
VALUES('$title','$overview','$videofilepath','$videofilepath','$imgfilepath')";

 if($conn->query($sql) === TRUE){

 echo "DB inserted!\n";
 }
 $sql = "select id,filename720 from dboVideo order by id desc limit 5";

 $res = $conn->query($sql);
 while($row = $res->fetch_assoc()){
// if($row["filename720"] == $videofilepath)
 	echo $row['id']."\n";
 	$videoId = $row['id'];
	break;
 //}
}
*/

  $r11=$r12=$r21=$r22=0;
  $g11=$g12=$g21=$g22=0;
  $b11=$b12=$b21=$b22=0;
  $height=$width=0;
  $hw=0;
  $imgobject="";

  $imgobject = @imagecreatefromjpeg($filename);
//  echo "jpge imgae converted to bmp<br>";

  list($width, $height) = getimagesize($filename);
// $width = $size[0];
// $height = $size[1];

 $vdna[$counter] = new DNA();
 $vdna[$counter]->idx=$idx;
 // echo "himdlguna".$counter."<br>";
// echo $width." ".$height."<br>";
//echo "&&&&".$vdna[$counter]->idx;
 for($i = 0; $i<$height -1;$i++ ){
        for($j=0; $j<$width-1;$j++){
                if($i <($height/2) && $j <($width/2)){
                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r11 += (int)(($rgb >> 16) & 0xFF);
                        $g11 += (int)(($rgb >> 8) & 0xFF);
                        $b11 += (int)($rgb & 0xFF);
//					echo "please where1?<br>";
                }
                if($i <($height/2) && $j >=($width/2)){

                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r12 += (int)(($rgb >> 16) & 0xFF);
                        $g12 += (int)(($rgb >> 8) & 0xFF);
                        $b12 += (int)($rgb & 0xFF);
//					echo "please where2?<br>";
                }
                if($i>=($height/2) && $j <($width/2)){

                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r21 += (int)(($rgb >> 16) & 0xFF);
                        $g21 += (int)(($rgb >> 8) & 0xFF);
                        $b21 += (int)($rgb & 0xFF);
//					echo "please where3?<br>";
                }
                if($i >=($height/2) && $j >=($width/2)){
                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r22 += (int)(($rgb >> 16) & 0xFF);
                        $g22 += (int)(($rgb >> 8) & 0xFF);
                        $b22 += (int)($rgb & 0xFF);
//					echo "please where4?<br>";
                }
        }
		//echo "please where?<br>";
 }
$hw=$height*$width /4.0;
            $vdna[$counter]->r1=(int)($r11/$hw); $vdna[$counter]->g1=(int)($g11/$hw); $vdna[$counter]->b1=(int)($b11/$hw);
            $vdna[$counter]->r2=(int)($r12/$hw); $vdna[$counter]->g2=(int)($g12/$hw); $vdna[$counter]->b2=(int)($b12/$hw);
            $vdna[$counter]->r3=(int)($r21/$hw); $vdna[$counter]->g3=(int)($g21/$hw); $vdna[$counter]->b3=(int)($b21/$hw);
            $vdna[$counter]->r4=(int)($r22/$hw); $vdna[$counter]->g4=(int)($g22/$hw); $vdna[$counter]->b4=(int)($b22/$hw);
/*echo $vdna[$counter]->r1." ".$vdna[$counter]->g1." ".$vdna[$counter]->b1." <br>";
            echo $vdna[$counter]->r2." ".$vdna[$counter]->g2." ".$vdna[$counter]->b2." <br>";
            echo $vdna[$counter]->r3." ".$vdna[$counter]->g3." ".$vdna[$counter]->b3." <br>";
            echo $vdna[$counter]->r4." ".$vdna[$counter]->g4." ".$vdna[$counter]->b4." end.<br>";
*/
//	echo "why please where?<br>";
/* $hw=$height*$width /4.0;
 $vdna[$counter]->r1=$r11/$hw; $vdna[$counter]->g1=$g11/$hw; $vdna[$counter]->b1=$b11/$hw;
 $vdna[$counter]->r2=$r12/$hw; $vdna[$counter]->g2=$g12/$hw; $vdna[$counter]->b2=$b12/$hw;
 $vdna[$counter]->r3=$r21/$hw; $vdna[$counter]->g3=$g21/$hw; $vdna[$counter]->b3=$b21/$hw;
 $vdna[$counter]->r4=$r22/$hw; $vdna[$counter]->g4=$g22/$hw; $vdna[$counter]->b4=$b22/$hw;
*/
//echo "RGB extracted<br>";

 $vdnaref="";//0;


// $idx = $vdna[$counter]->idx;
 for($k=$counter-1;$k>=0&&(($counter-$k)<5);$k--){
//		$vdnaref="";
/*
        for($z=0; $z<count($vdna); $z++){
				//echo "????????????????????2";
                if($vnda[$z]->idx==$k){
				echo "????????????????????55";
                        $vdnaref=$z;
                        break;
                }
        }
		echo "!!!!!!!!!!!!!!!!!!!!!!!";*/
//        if($vdnaref!= ""){
//				echo "????????????????????ialsfdaslfksa";
                $diff = 0;
/*
                $diff = abs($vdna[$counter]->r1-$vdna[$vdnaref]->r1);
                $diff += abs($vdna[$counter]->r2-$vdna[$vdnaref]->r2);
                $diff += abs($vdna[$counter]->r3-$vdna[$vdnaref]->r3);
                $diff += abs($vdna[$counter]->r4-$vdna[$vdnaref]->r4);
                $diff += abs($vdna[$counter]->g1-$vdna[$vdnaref]->g1);
                $diff += abs($vdna[$counter]->g2-$vdna[$vdnaref]->g2);
                $diff += abs($vdna[$counter]->g3-$vdna[$vdnaref]->g3);
                $diff += abs($vdna[$counter]->g4-$vdna[$vdnaref]->g4);
                $diff += abs($vdna[$counter]->b1-$vdna[$vdnaref]->b1);
                $diff += abs($vdna[$counter]->b2-$vdna[$vdnaref]->b2);
                $diff += abs($vdna[$counter]->b3-$vdna[$vdnaref]->b3);
                $diff += abs($vdna[$counter]->b4-$vdna[$vdnaref]->b4);
                switch((int)$idx-$k){

                        case 1: $vdna[$vdnaref]->diff1=$diff;
                                break;
                        case 2: $vdna[$vdnaref]->diff2=$diff;
                                break;
                        case 3: $vdna[$vdnaref]->diff3=$diff;
                                break;
                        case 4: $vdna[$vdnaref]->diff4=$diff;
                                break;
*/
				$diff = abs($vdna[$counter]->r1-$vdna[$k]->r1);
                $diff += abs($vdna[$counter]->r2-$vdna[$k]->r2);
                $diff += abs($vdna[$counter]->r3-$vdna[$k]->r3);
                $diff += abs($vdna[$counter]->r4-$vdna[$k]->r4);
                $diff += abs($vdna[$counter]->g1-$vdna[$k]->g1);
                $diff += abs($vdna[$counter]->g2-$vdna[$k]->g2);
                $diff += abs($vdna[$counter]->g3-$vdna[$k]->g3);
                $diff += abs($vdna[$counter]->g4-$vdna[$k]->g4);
                $diff += abs($vdna[$counter]->b1-$vdna[$k]->b1);
                $diff += abs($vdna[$counter]->b2-$vdna[$k]->b2);
                $diff += abs($vdna[$counter]->b3-$vdna[$k]->b3);
                $diff += abs($vdna[$counter]->b4-$vdna[$k]->b4);
   //             echo $counter-$k." ".$diff."<br>";
				switch((int)$counter-$k){

                        case 1: $vdna[$k]->diff1=(int)$diff;
                                break;
                        case 2: $vdna[$k]->diff2=(int)$diff;
                                break;
                        case 3: $vdna[$k]->diff3=(int)$diff;
                                break;
                        case 4: $vdna[$k]->diff4=(int)$diff;
                                break;

                }
        //}
 }

// echo "diffrence extracted<br>";
 $counter++;
 $idx++;
 $filename = "/var/www/html/Storage/snapshots/".$idx.".jpg";
}
$insertid = $counter;
$counter = 0;
$idx = 1;
//echo $counter;
while($insertid>$counter){

/*echo $vdna[$counter]->r1." ".$vdna[$counter]->g1." ".$vdna[$counter]->b1." <br>";
            echo $vdna[$counter]->r2." ".$vdna[$counter]->g2." ".$vdna[$counter]->b2." <br>";
            echo $vdna[$counter]->r3." ".$vdna[$counter]->g3." ".$vdna[$counter]->b3." <br>";
            echo $vdna[$counter]->r4." ".$vdna[$counter]->g4." ".$vdna[$counter]->b4." end.<br>";
			echo $vdna[$counter]->diff1." ".$vdna[$counter]->diff2." ".$vdna[$counter]->diff3." ".$vdna[$counter]->diff4." end<br>";
*/

 $r11=(int)$vdna[$counter]->r1; $r12=$vdna[$counter]->r2; $r21=$vdna[$counter]->r3; $r22=$vdna[$counter]->r4;
 $g11=$vdna[$counter]->g1; $g12=$vdna[$counter]->g2; $g21=$vdna[$counter]->g3; $g22=$vdna[$counter]->g4;
 $b11=$vdna[$counter]->b1; $b12=$vdna[$counter]->b2; $b21=$vdna[$counter]->b3; $b22=$vdna[$counter]->b4;
 $diff1=$vdna[$counter]->diff1;
 $diff2=$vdna[$counter]->diff2;
 $diff3=$vdna[$counter]->diff3;
 $diff4=$vdna[$counter]->diff4;

//  echo $r11." ".$g11." ".$b11." ".$r12." ".$g12." ".$b12." ".$r21." ".$g21." ".$b21." ".$r22." ".$g22." ".$b22." end!<br>";
// $sql = "select id from dboVideo where id = ".$videoId;
// echo "insert please<br>";
 $sql = "INSERT INTO VDNA (Videoid,idx,r1,g1,b1,r2,g2,b2,r3,g3,b3,r4,g4,b4,diff1,diff2,diff3,diff4) values ((select id from dboVideo where id = ".$videoId." ),'$idx', '$r11', '$g11','$b11', '$r12', '$g12','$b12',  '$r21', '$g21','$b21', '$r22', '$g22','$b22','$diff1', '$diff2', '$diff3', '$diff4')";

 if($conn->query($sql) === TRUE){
	//echo $idx."<br>";
 }

 $idx++;
 $counter++;

 }
}
	$files = glob("/var/www/html/Storage/snapshots/*");
    foreach ($files as $file)
    {
            if(is_file($file))
                    unlink($file);
    }
$conn->close();

?>

</body>
</html>




