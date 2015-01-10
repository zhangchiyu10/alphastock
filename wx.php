<?php
define("TOKEN", "zhangchiyu10");
define('WEB',"http://182.92.191.23/");
define("WELCOME","欢迎来到Alpha Stock！\nAlpha Stock根据所有玩家的报价每10秒撮合定价一次。\n您的初始资金有1万个Alpha币。\n回复任意键查询账户");
define("TEXT","<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>0</FuncFlag>
</xml>");
$wechatObj = new wechatCallback();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechatCallback
{
    public function valid()
    {
//        if($this->checkSignature()){
            echo $_GET["echostr"];
            exit;
  //      }
    }
    private function checkSignature()
    {
        $tmpArr = array(TOKEN, $_GET["timestamp"], $_GET["nonce"]);
        sort($tmpArr, SORT_STRING);
        if( sha1(implode( $tmpArr )) == $_GET["signature"] ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if(empty($postStr)){                
            echo "empty";
            exit;
        }else{
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $msgType=$postObj->MsgType;
            $href=WEB."?openid=".$fromUsername;
            $con = mysql_connect("localhost", "root", "1234567890") or die("cannot connect to mysql.");
            mysql_select_db("app_alphastock" ,$con) or die ("cannot connect to database.");
            $check=false;
            $bsstr['0']="卖出";$bsstr['1']="买入";
            if($msgType=="text"){//text
                $keyword = trim($postObj->Content);   
                if(strstr($keyword,"最漂亮的女人")){//FUN
                    $contentStr="陈香瑶是世界上最美丽的女人。但深宫锁玉，很少有人见过本尊。";
                }else{
                    $check=true;
                }
            }else if($msgType=="event"){//event                
                    $event=trim($postObj->Event);                    
                    if($event=="subscribe"){         
                        $row=mysql_fetch_array(mysql_query(sprintf("SELECT * FROM client WHERE openid='%s'",$fromUsername)));
                        if(!$row){                        	
                            $contentStr=WELCOME."\n".sprintf("<a href='%s#account'>点击进入账户</a>",$href);
                            mysql_query(sprintf("INSERT INTO client (openid) VALUES ('%s')",$fromUsername));
                        }else{
                            $check=true;
                            $contentStr="欢迎回来！\n";
                        }
                    }
            }else if($msgType=="location"){//location
                $contentStr=sprintf("您在东经%s北纬%s\n%s",$postObj->Location_Y,$postObj->Location_X,$postObj->Label );
            }else if($msgType=="image"){//image
                $contentStr="手动点赞";
            }else{
                exit;                                                
            }
            if($check){
                $result=mysql_query(sprintf("SELECT * FROM entrusts where openid='%s' and hand>dealhand and cancelled=0 ORDER BY entrustid",$fromUsername));
                $orders="";
                $count=0;
                while($row=mysql_fetch_array($result)){
                    $count++;
                    $orders.=sprintf("%s)每手%s%s%s手\n",$count,$row['price'],$bsstr[$row['buy']],(int)$row['hand']-(int)$row['dealhand']);
                }
                $contentStr.=sprintf("==您还有%s笔未成交==\n%s",$count,$orders);                        
                $row=mysql_fetch_array(mysql_query(sprintf("SELECT * FROM client where openid='%s'",$fromUsername)));
                $market = mysql_fetch_array(mysql_query("SELECT * FROM market ORDER BY time desc limit 1"));
                $avecost=(float)$row['avecost'];
                $marketprice=(int)$market['price'];
                $profit=0;
                if($avecost>0){
                   $profit=round(($marketprice/$avecost-1)*100,1);
                }
                $contentStr.=sprintf("=====您的账户=====\n现金:%s\n可卖:%s手\n平均成本价:%s\n<a href='".$href."'>市价【%s】</a>\n浮动盈亏:%s%%\n总资产市值:%s\n",$row['available'],$row['marketposition'],round($avecost,1),$marketprice,$profit,$row['asset']);
                $contentStr.=sprintf("<a href='%s#account'>点击进入账户</a>",$href);
            }
            mysql_close($con);  
            $resultStr = sprintf(TEXT, $fromUsername, $toUsername,time(), "text", $contentStr);
            echo $resultStr;
        }           
    }
}

?>
