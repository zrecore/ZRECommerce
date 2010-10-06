<?php
//ob_start();
$pathinfo = pathinfo($_SERVER['PHP_SELF']); 
$extension = $pathinfo['extension']; 

if($extension == "css"){ 
	header("Accept-Encoding: gzip, deflate");
	header("X-Compression: gzip");
	header("Content-Encoding: gzip");
	header("Content-type: text/css"); 
} 

if($extension == "js"){ 
	header("Accept-Encoding: gzip, deflate");
	header("X-Compression: gzip");
	header("Content-Encoding: gzip");
	header("Content-type: text/javascript"); 
}
 
if($extension == "php"){ 
	header("Accept-Encoding: gzip, deflate");
	header("X-Compression: gzip");
	header("Content-Encoding: gzip");
	header("Content-type: text/html");
	header("Cache-Control: must-revalidate"); 

   
	$offset = 60 * 60 ; 
	$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT"; 
	header($ExpStr); 
}