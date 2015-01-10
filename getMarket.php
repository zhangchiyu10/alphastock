<?php

$con = mysqli_connect("localhost", "root", "1234567890") or die("cannot connect to mysql.");
mysqli_select_db($con, "app_alphastock") or die ("cannot connect to database.");
$jsonArray = array();
if($_GET['op']=='live'){
	$market = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM market_min WHERE datetime like '".date('Ymd')."%' ORDER BY datetime DESC limit 1"));

	$jsonArray = array(strtotime($market['datetime'])*1000, (int)$market['price'], (int)$market['volume']);
}else if($_GET['op']=='today'){
	$market = mysqli_query($con, "SELECT * FROM market_min  WHERE datetime like '".date('Ymd')."%' ORDER BY datetime ASC");
	while($row = mysqli_fetch_array($market)) {
		array_push($jsonArray, array(strtotime($row['datetime'])*1000, (int)$row['price'], (int)$row['volume']));
	}
}else if($_GET['op']=='candle'){
	$candlestick = mysqli_query($con, "SELECT * FROM candlestick ORDER BY date");
	while($row = mysqli_fetch_array($candlestick)) {
		array_push($jsonArray, array(strtotime($row['date'])*1000, (int)$row['open'], (int)$row['high'], (int)$row['low'], (int)$row['close'], (int)$row['volume']));
	}

}
echo json_encode($jsonArray);
mysqli_close($con);



?>