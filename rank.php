<div id="rank">
	<div class="toolbar">
		<h1>排名</h1>
		<a class="back active"  href="#account">返回</a>
	</div>
	<div>
<?php

define('WEB',"
<h1>您总共获利%s<br>全球排名第%s</h1>");
$con = mysqli_connect("localhost", "root", "1234567890") or die("cannot connect to mysql.");
mysqli_select_db($con,"app_alphastock") or die ("cannot connect to database.");
$row=mysqli_fetch_array(mysqli_query($con,sprintf("SELECT * FROM client where openid='%s'",$_GET["openid"])));    
$profitratio=round((float)$row['gain']/(float)$row['initial']*100,2);
mysqli_close($con);  
echo sprintf(WEB,$row['gain'],$row['rank']);

?>
	</div>
</div>