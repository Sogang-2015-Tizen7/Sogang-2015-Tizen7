<?php

//ini_set('error_reporting', E_STRICT);
error_reporting(E_ALL);

function sum_sort($a,$b)
{
if ($a->sum==$b->sum) return 0;
return ($a->sum<$b->sum)?-1:1;
}

class DNA{

		public $videoId;
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
		public $sum;
}

class video{
	public $videoId;
	public $imgPath;
	public $videoPath;
}


define("_IP",    "163.239.27.173");
define("_PORT",  "65000");

function videoSearch($filename){

  $servername = "localhost";
  $username = "root";
  $password = "mlint1234";
  $dbname = "MAM_DB";

  $conn = new mysqli($servername, $username, $password, $dbname);
  if($conn->connect_error){
    die("connection failed : ".$conn->connect_error);
  }

  $sock      = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
  socket_connect($sock, _IP, _PORT);
  //echo "CLIENT >> socket connect to "._IP.":"._PORT."\n";
  $date = socket_read($sock, 4096);
  //echo "CLIENT >> this time is $date \n";
  $str = "/var/www/html/ffmpeg/ffmpeg.exe -ss 00:00:01 -i /var/www/html/upfile/".$filename." -frames:v 50 /var/www/html/upfile/snapshots/%d.jpg";
//  $str = "/var/www/html/ffmpeg/ffmpeg.exe -ss 00:00:01 -i /var/www/html/upfile/10sec.mp4 -vsync 0 -vf \"select='not(mod(n,10))'\" /var/www/html/test/%d.jpg";
//  $str = "50 upfile/".$filename." upfile/";
  socket_write($sock,$str,strlen($str));

  $data = socket_read($sock, 4096);
 // echo $data;

  socket_close($sock);
 // echo "CLIENT >> socket closed.\n";


  $imgfilepath = $imgtype="";
  $idx = 1;
  $counter=0;

  for($idx=1;$idx<=10;$idx++)
  {
		$snapname = "/var/www/html/upfile/snapshots/".$idx.".jpg";///var/www/html/ffmpeg/ffmpeg.exe -ss 00:00:01 -i /var/www/html/upfile/10sec.mp4 -vsync 0 -vf \"select='not(mod(n,10))'\" /var/www/html/test/%d.jpg";
		$r11=$r12=$r21=$r22=0;
		$g11=$g12=$g21=$g22=0;
  		$b11=$b12=$b21=$b22=0;
		$height=$width=0;
  		$hw=0;
  		$imgobject="";

		$imgobject = imagecreatefromjpeg($snapname);
		if ($imgobject){
			//echo "??";
			list($width, $height) = getimagesize($snapname);
	
			$vdna[$counter] = new DNA();
		 	$vdna[$counter]->idx=$idx;
			
		  	for($i = 0; $i<$height -1;$i++ ){
    	  		for($j=0; $j<$width-1;$j++){
    	     		if($i <($height/2) && $j <($width/2)){

                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r11 += (int)(($rgb >> 16) & 0xFF);
                        $g11 += (int)(($rgb >> 8) & 0xFF);
                        $b11 += (int)($rgb & 0xFF);

    	      		}
    	      		if($i <($height/2) && $j >=($width/2)){

                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r12 += (int)(($rgb >> 16) & 0xFF);
                        $g12 += (int)(($rgb >> 8) & 0xFF);
                        $b12 += (int)($rgb & 0xFF);

    	      		}	
    	      		if($i>=($height/2) && $j <($width/2)){

                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r21 += (int)(($rgb >> 16) & 0xFF);
                        $g21 += (int)(($rgb >> 8) & 0xFF);
                        $b21 += (int)($rgb & 0xFF);

	          		}
    	      		if($i >=($height/2) && $j >=($width/2)){

                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r22 += (int)(($rgb >> 16) & 0xFF);
                        $g22 += (int)(($rgb >> 8) & 0xFF);
                        $b22 += (int)($rgb & 0xFF);

        	  		} 
				}
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
/*
			$hw = $height*$width /4.0;
			$vdna[$counter]->r1=$r11/$hw; $vdna[$counter]->g1=$g11/$hw; $vdna[$counter]->b1=$b11/$hw;
			$vdna[$counter]->r2=$r12/$hw; $vdna[$counter]->g2=$g12/$hw; $vdna[$counter]->b2=$b12/$hw;
			$vdna[$counter]->r3=$r21/$hw; $vdna[$counter]->g3=$g21/$hw; $vdna[$counter]->b3=$b21/$hw;
			$vdna[$counter]->r4=$r22/$hw; $vdna[$counter]->g4=$g22/$hw; $vdna[$counter]->b4=$b22/$hw;
*/    	}
    	$counter++;
  }

    //2단계, 추출된 10개의 프레임중에서 DB와 비교해서 가장 가까운 프레임을 선정한다.
  foreach ($vdna as $adna)
  {
				$sql = "SELECT Videoid,idx,r1,g1,b1,r2,g2,b2,r3,g3,b3,r4,g4,b4,diff1,diff2,diff3,diff4 FROM VDNA";
				$res = $conn->query($sql);
				$sum_rgb  = 100000;
				//$sum_rgb  = 100000;
				if($res->num_rows >0){
					$sum_rgb  = 100000;
		      		while($row = $res->fetch_assoc()){
            		$sum_tmp = (int)(abs(($row["r1"]) - ($adna->r1)) + abs($row["g1"] - $adna->g1) 
						+ abs($row["b1"] - $adna->b1) + abs($row["r2"] - $adna->r2) 
						+ abs($row["g2"] - $adna->g2) + abs($row["b2"] - $adna->b2)
						+ abs($row["r3"] - $adna->r3) + abs($row["g3"] - $adna->g3) 
						+ abs($row["b3"] - $adna->b3) + abs($row["r4"] - $adna->r4) 
						+ abs($row["g4"] - $adna->g4) + abs($row["b4"] - $adna->b4));
					  if($sum_rgb>$sum_tmp){
						  $sum_rgb = $sum_tmp;
					}
          		}
				}
        $adna->sum = $sum_rgb;
  }

            // 최소값 선택 완료
  usort($vdna,"sum_sort");
  $tdna = $vdna[0];
  //echo $vdna[0]->idx."bb".count($vdna)."<br>";

  $imgfilepath = $imgtype="";
  $idx = 1;
  $counter=1;

    // DNA Phase II: 연속적인 패턴값 추출
  for ($idx1 = $vdna[0]->idx+10; $idx1 <= 5*10; $idx1=$idx1+10 ){
        $snapname = "/var/www/html/upfile/snapshots/".$idx1.".jpg";
		//echo $snapname."qeqweqwe";
        if (file_exists($snapname) != 1) continue; 

		$r11=$r12=$r21=$r22=0;
        $g11=$g12=$g21=$g22=0;
  		$b11=$b12=$b21=$b22=0;
  		$height=$width=0;
  		$hw=0;
  		$imgobject="";
				
		$imgobject = imagecreatefromjpeg($snapname);
        
		if ($imgobject){

            list($width, $height) = getimagesize($snapname);

            $vdna[$counter] = new DNA();
 			$vdna[$counter]->idx=$idx;
            for($i = 0; $i<$height -1;$i++ ){
				for($j=0; $j<$width-1;$j++){
                	if($i <($height/2) && $j <($width/2)){

                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r11 += (int)(($rgb >> 16) & 0xFF);
                        $g11 += (int)(($rgb >> 8) & 0xFF);
                        $b11 += (int)($rgb & 0xFF);

                	}
                	if($i <($height/2) && $j >=($width/2)){

                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r12 += (int)(($rgb >> 16) & 0xFF);
                        $g12 += (int)(($rgb >> 8) & 0xFF);
                        $b12 += (int)($rgb & 0xFF);

                	}
                	if($i>=($height/2) && $j <($width/2)){

                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r21 += (int)(($rgb >> 16) & 0xFF);
                        $g21 += (int)(($rgb >> 8) & 0xFF);
                        $b21 += (int)($rgb & 0xFF);

                	}
      	        	if($i >=($height/2) && $j >=($width/2)){

                        $rgb = imagecolorat($imgobject, $j, $i);
                        $r22 += (int)(($rgb >> 16) & 0xFF);
                        $g22 += (int)(($rgb >> 8) & 0xFF);
                        $b22 += (int)($rgb & 0xFF);

                	}
        		}
			}

	        $hw=$height*$width /4.0;
	        $vdna[$counter]->r1=(int)($r11/$hw); $vdna[$counter]->g1=(int)($g11/$hw); $vdna[$counter]->b1=(int)($b11/$hw);
	        $vdna[$counter]->r2=(int)($r12/$hw); $vdna[$counter]->g2=(int)($g12/$hw); $vdna[$counter]->b2=(int)($b12/$hw);
	        $vdna[$counter]->r3=(int)($r21/$hw); $vdna[$counter]->g3=(int)($g21/$hw); $vdna[$counter]->b3=(int)($b21/$hw);
	        $vdna[$counter]->r4=(int)($r22/$hw); $vdna[$counter]->g4=(int)($g22/$hw); $vdna[$counter]->b4=(int)($b22/$hw);
              // byte레벨로 소수점 이하가 사라짐. 오차 발생 포인트

//		echo $idx1."<br>";
//		for($k=$counter-1;$k>=0&&(($counter-$k)<5);$k--){
//		echo $counter." ".$k."<br>";
            $diff = 0;
//            if ($tdna != null)
//            {
        	//	$diff = 0;
				$diff = abs($vdna[$counter]->r1-$vdna[0]->r1);
                $diff += abs($vdna[$counter]->r2-$vdna[0]->r2);
                $diff += abs($vdna[$counter]->r3-$vdna[0]->r3);
                $diff += abs($vdna[$counter]->r4-$vdna[0]->r4);
                $diff += abs($vdna[$counter]->g1-$vdna[0]->g1);
                $diff += abs($vdna[$counter]->g2-$vdna[0]->g2);
                $diff += abs($vdna[$counter]->g3-$vdna[0]->g3);
                $diff += abs($vdna[$counter]->g4-$vdna[0]->g4);
                $diff += abs($vdna[$counter]->b1-$vdna[0]->b1);
                $diff += abs($vdna[$counter]->b2-$vdna[0]->b2);
                $diff += abs($vdna[$counter]->b3-$vdna[0]->b3);
                $diff += abs($vdna[$counter]->b4-$vdna[0]->b4);
                
				//echo " ddd ".($counter-$k)." ".((int)$diff)."<br>";
                switch((int)$counter){

                        case 1: $vdna[0]->diff1=(int)$diff;
                                break;
                        case 2: $vdna[0]->diff2=(int)$diff;
                                break;
                        case 3: $vdna[0]->diff3=(int)$diff;
                                break;
                        case 4: $vdna[0]->diff4=(int)$diff;
                                break;
/*
                $diff = abs($vdna[$counter]->r1-$vdnaref->r1);
                $diff += abs($vdna[$counter]->r2-$vdnaref->r2);
                $diff += abs($vdna[$counter]->r3-$vdnaref->r3);
                $diff += abs($vdna[$counter]->r4-$vdnaref->r4);
                $diff += abs($vdna[$counter]->g1-$vdnaref->g1);
                $diff += abs($vdna[$counter]->g2-$vdnaref->g2);
                $diff += abs($vdna[$counter]->g3-$vdnaref->g3);
                $diff += abs($vdna[$counter]->g4-$vdnaref->g4);
                $diff += abs($vdna[$counter]->b1-$vdnaref->b1);
                $diff += abs($vdna[$counter]->b2-$vdnaref->b2);
                $diff += abs($vdna[$counter]->b3-$vdnaref->b3);
                $diff += abs($vdna[$counter]->b4-$vdnaref->b4);

				        switch((int)(($idx1-$tdna->idx)/10)){
                        case 1: $tdna->diff1=$diff;
                                break;
                        case 2: $tdna->diff2=$diff;
                                break;
                        case 3: $tdna->diff3=$diff;
                                break;
                        case 4: $tdna->diff4=$diff;
                                break;
           	 			}*/
        	  }
//		}
			  $counter++;
		}
  }

	$sql = "select * from VDNA";
  	$res = $conn->query($sql);
	$num = 0;
	$db = "";
  	if($res->num_rows >0){
		//	echo "fuck";

			while($row = $res->fetch_assoc()){
						$db[$num] = new DNA();
						$db[$num]->videoId = $row["Videoid"];
                        $db[$num]->r1 = $row["r1"];
						$db[$num]->g1 = $row["g1"];
						$db[$num]->b1 = $row["b1"];
						$db[$num]->r2 = $row["r2"];
						$db[$num]->g2 = $row["g2"];
						$db[$num]->b2 = $row["b2"];
						$db[$num]->r3 = $row["r3"];
						$db[$num]->g3 = $row["g3"];
						$db[$num]->b3 = $row["b3"];
						$db[$num]->r4 = $row["r4"];
						$db[$num]->g4 = $row["g4"];
						$db[$num]->b4 = $row["b4"];
						$db[$num]->diff1 = $row["diff1"];
						$db[$num]->diff2 = $row["diff2"];
						$db[$num]->diff3 = $row["diff3"];
						$db[$num]->diff4 = $row["diff4"];
						$num++;
			}
  	}
           

	$num = 0;
	foreach($db as $v2){
			$tmp[$num] = new DNA();
			$sum_rgb = abs($v2->r1-$tdna->r1)
			+abs($v2->g1-$tdna->g1)
			+abs($v2->b1-$tdna->b1)
			+abs($v2->r2-$tdna->r2)
			+abs($v2->g2-$tdna->g2)
			+abs($v2->b2-$tdna->b2)
			+abs($v2->r3-$tdna->r3)
			+abs($v2->g3-$tdna->g3)
			+abs($v2->b3-$tdna->b3)
			+abs($v2->r4-$tdna->r4)
			+abs($v2->g4-$tdna->g4)
			+abs($v2->b4-$tdna->b4)
			+abs($v2->diff1-$tdna->diff1)
			+abs($v2->diff2-$tdna->diff2)
			+abs($v2->diff3-$tdna->diff3)
			+abs($v2->diff4-$tdna->diff4);
		//	echo "fuck";


//			if($sum_rgb <= 10){
				$tmp[$num] = $v2;
				$tmp[$num]->sum = (int)$sum_rgb;
				$num++;
				//echo $tmp[$num-1]->videoId." & ".(int)$sum_rgb." ".($v2->diff4-$tdna->diff4)." ".($tdna->diff1)."<br>";

//				}
	}

            // 최소값 선택 완료
    usort($tmp,"sum_sort");
	$num = 0;
	$Search = "";
	$quantity = 0;

//	$res = $tmp[0];	
	foreach ($tmp as $dd){
		if($dd->sum >= 250)    break;
		//echo $dd->videoId." ".$dd->sum."<br>";
		$sql = "select id,filename720,imgname from dboVideo where id=".$dd->videoId;
        $res = $conn->query($sql);

        $row = $res->fetch_assoc();

		for ($reid = 0;$reid<$num;$reid++){
			if($Search[$reid]->videoId == $row['id'])
				break;
		}
		if($Search[$reid]->videoId == $row['id']){
			$quantity++;
			continue;
		}

		$Search[$num] = new Video();
	  	$Search[$num]->videoId = $row['id'];
	  	$Search[$num]->imgPath = $row['imgname'];
		$Search[$num]->videoPath = $row['filename720'];
		$num++;
//		echo $dd->videoId." ".$dd->sum."<br>";
		$quantity++;
		if($quantity>=10)	break;
	}

	$file = glob("/var/www/html/upfile/".$filename);
	if(is_file($file))
                    unlink($file);
	$files = glob("/var/www/html/upfile/snapshots/*");	
	foreach ($files as $file)
  	{
			if(is_file($file))
                	unlink($file);
  	}
  	$conn->close();
    return $Search;
}

?>
