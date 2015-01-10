<?php

$con = mysqli_connect("localhost", "root", "1234567890") or die("cannot connect to mysql.");
mysqli_select_db($con,"app_alphastock") or die ("cannot connect to database.");
$jsonarray="";
if ($_GET['op']=='1' && isset($_GET['openid']) && preg_match("/^[1-9][0-9]*$/",$_GET['price']) && preg_match("/^[1-9][0-9]*$/",$_GET['hand']) && preg_match("/^[0|1]$/",$_GET['buy']) ) {
    $openid = $_GET['openid'];
    $cost=(int)$_GET['price'];
    $hand=(int)$_GET['hand'];
    $buy=$_GET['buy'];
    $freeze=$hand*$cost;
    $hint=-1;
    $row=mysqli_fetch_array(mysqli_query($con,sprintf("SELECT * FROM client where openid='%s'",$openid)));
    if($row){
        $total=mysqli_fetch_array(mysqli_query($con,sprintf("SELECT COUNT(*) FROM orders where openid='%s'",$openid)));
                        if($total[0]>=10){
                            $hint=1;
                        }else if($buy && (int)$row['available']<$freeze){
                           $hint=2;                           
                        }else if(!$buy && (int)$row['marketposition']<$hand){
                            $hint=3;
                        }else{
                            if($buy){                                      
                                mysqli_query($con,sprintf("UPDATE client SET frozen=frozen+%s,available=available-%s WHERE openid='%s'",$freeze,$freeze,$openid));
                            }else{
                                mysqli_query($con,sprintf("UPDATE client SET frozenposition=frozenposition+%s,marketposition=marketposition-%s WHERE openid='%s'",$hand,$hand,$openid));
                            }
                            mysqli_query($con,sprintf("INSERT INTO entrusts (openid,buy,price,hand) VALUES ('%s','%s','%s','%s')",$openid,$buy,$cost,$hand));
                            mysqli_query($con,sprintf("INSERT INTO orders (openid,buy,price,hand) VALUES ('%s','%s','%s','%s')",$openid,$buy,$cost,$hand));
                            $hint=0;
                        } 
    }
    $jsonarray=array('hint'=>$hint);    
}else if($_GET['op']=='0' && isset($_GET['openid']) && preg_match("/^[1-9][0-9]*$/",$_GET['entrustid']) ) {
    $openid=$_GET["openid"]; 
    $entrustid=$_GET["entrustid"]; 
    $row=mysqli_fetch_array(mysqli_query($con,sprintf("SELECT * FROM entrusts where openid='%s' and entrustid='%s'",$openid,$entrustid)));
    $hint=-1;//null
    if($row){
        if($row['cancelled']==1){
            $hint=1;//already cancelled
        }else if($row['cancelled']==0 && $row['hand']==$row['dealhand']){
            $hint=2;//all deal
        }else{
            $hint=3;
            mysqli_query($con,sprintf("UPDATE entrusts SET cancelled=1 where entrustid='%s'",$entrustid));
            mysqli_query($con,sprintf("DELETE FROM orders where entrustid='%s'",$entrustid));
            $handleft=(int)$row['hand']-(int)$row['dealhand'];
            if($row['buy']){
                $unfrozen=(int)$row['price']*$handleft;
                mysqli_query($con,sprintf("UPDATE client SET frozen=frozen-%s,available=available+%s WHERE openid='%s'",$unfrozen,$unfrozen,$openid));
            }else{
                mysqli_query($con,sprintf("UPDATE client SET frozenposition=frozenposition-%s,marketposition=marketposition+%s WHERE openid='%s'",$handleft,$handleft,$openid));
            }
            
                        
        }
    }
    
    $jsonarray=array("hint"=>$hint);
}
mysqli_close($con);  
echo json_encode($jsonarray);



                
?>