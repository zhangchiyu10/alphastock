<?php
    $con = mysqli_connect("localhost", "root", "1234567890") or die("cannot connect to mysql.");
    mysqli_select_db($con,"app_alphastock") or die ("cannot connect to database.");
    $market = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM market WHERE date='".date('Ymd')."'ORDER BY timestamp desc limit 1"));
               
    $openid=$_GET["openid"];            
            
    $client=mysqli_fetch_array(mysqli_query($con,sprintf("SELECT * FROM client where openid='%s'",$openid)));
            
                $result=mysqli_query($con,sprintf("SELECT * FROM entrusts where cancelled=0 and openid='%s' and hand>dealhand ORDER BY entrustid",$openid));
                $orders=array();
                $count=0;
                while($row=mysqli_fetch_array($result)){
                    $count++;
                    $avedealprice=0;
                    if((int)$row['dealhand']>0){
                        $avedealprice=((float)$row['dealsum']/(float)$row['dealhand']);
                    }
                    $orders[]=array('id'=>$row['entrustid'],'buy'=>(boolean)$row['buy'],'price'=>(int)$row['price'],'hand'=>(int)$row['hand'],'dealhand'=>(int)$row['dealhand'],'avedealprice'=>$avedealprice);
                }
            $jsonarray=array(
                'marketprice'=>(int)$market['price'],
                'cash'=>(int)$client['available'],
                'frozen'=>(int)$client['frozen'],
                'position'=>(int)$client['marketposition']+(int)$client['frozenposition'],
                'marketposition'=>(int)$client['marketposition'],
                'avecost'=>round((float)$client['avecost'],1),
                
                'asset'=>(int)$client['asset'],
                'count'=>$count,
                'orders'=>$orders
                );


            if ($_GET['op']==1) {

            
            $result = mysqli_query($con,"SELECT * FROM bids where rank like 'b%'");
            $b=array();
            while($row=mysqli_fetch_array($result)){
                    $b[]=array((int)$row['price'],(int)$row['hand']);
            }
            $result = mysqli_query($con,"SELECT * FROM bids where rank like 's%'");
            $s=array();
            while($row=mysqli_fetch_array($result)){
                    $s[]=array((int)$row['price'],(int)$row['hand']);
            }

                $jsonarray=array(
                'marketprice'=>(int)$market['price'],
                'cash'=>(int)$client['available'],
                'frozen'=>(int)$client['frozen'],
                'position'=>(int)$client['marketposition']+(int)$client['frozenposition'],
                'marketposition'=>(int)$client['marketposition'],
                'avecost'=>round((float)$client['avecost'],1),
                'asset'=>(int)$client['asset'],
                'count'=>$count,
                'orders'=>$orders,

                'time'=>$market['timestamp'],
                's'=>$s,
                'b'=>$b,
                );
           }

        mysqli_close($con);  
        echo json_encode($jsonarray);          
?>