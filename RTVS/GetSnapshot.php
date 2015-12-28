<?php

define("_IP",    "163.239.27.173");
define("_PORT",  "65000");

$sSock      = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($sSock, _IP, _PORT);
socket_listen($sSock);
while($cSock = socket_accept($sSock))
{
    socket_getpeername($cSock, $addr, $port);
    echo "SERVER >> client connected $addr:$port \n";
    $date = date("Y/m/d H:i:s");
    socket_write($cSock, $date);
    $data = socket_read($cSock, 1024);
    
	$token = explode(' ',$data);

	echo $data;
	shell_exec("wine ".$data);

//	shell_exec("wine /var/www/html/ffmpeg/ffmpeg.exe -ss 00:00:01 -i ".$token[1]." -y -vframes ".$token[0]." -an /var/www/html/".$token[2]."snapshots/%d.jpg");

//	shell_exec("wine /var/www/html/ffmpeg/ffmpeg.exe -ss 00:00:01 -i /var/www/html/".$token[1]." -y -vframes ".$token[0]." -an /var/www/html/".$token[2]."snapshots/%d.jpg");

    socket_write($cSock, "OK\n");	

    socket_close($cSock);
    echo "SERVER >> client Close.\n";
}

