
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Alpha Stock</title>

        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

        <script type="text/javascript" src="//cdn.jsdelivr.net/jquery/2.1.1/jquery.min.js"></script>
        <script type="text/javascript" src="js/jqt.min.js"></script>
        <script type="text/javascript" src="js/jqtouch-jquery.min.js" ></script>
        <script type="text/javascript" src="js/jqt.actionsheet.min.js" ></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/highstock/2.0.3/highstock.js"></script>  
        <script type="text/javascript" src="js/dark-unica.js"></script>
        <link rel="stylesheet" href="css/jqtouch.min.css" data-jqt-theme>

        <link rel="stylesheet" href="css/account.css" >
        <script type="text/javascript" charset="utf-8">

            var jQT = new $.jQT({
                addGlossToIcon: false,
                statusBar: 'black-translucent',
                preloadImages: []
            }); 
        </script>
         <style type="text/css" media="screen">
            #jqt.fullscreen #home .info {
                display: none;
            }

        </style>
    </head>
    <body>
        <div id="jqt" class="">
            <div id="actionsheet" class="actionsheet">
                <div class="actionchoices">
                    <fieldset>
                        <div id="confirmcancel"></div>
                        <a id="cancelorder" href="#" class="whiteButton caution" onclick="">确认</a>
                    </fieldset>
                    <a href="#" class="redButton dismiss">取消</a>
                </div>
            </div>
            <div id="entrustleft">
                <div class="toolbar">
                    <h1>未成交交易</h1>
                    <a class="back active"  href="#account">返回</a>
                </div>
                <div class="scroll">
                    <ul id="entrusts">
                    </ul>
                </div>
            </div>
            <div id="home" class="current">
                <div class="toolbar">
                    <h1>Alpha Stock</h1>
                    <a class="button cube loading"  href="#account">交易账户</a>
                </div>
                <div class="scroll">
                    <div>
                        <div id="chartToday"  style="width:100%"></div>
                        <div id="chartCandle"  style="width:100%"></div>
                    </div>
                    <input id="today" type="button" value="今日走势" />   
                    <input id="candle" type="button" value="K线图" /> 
                </div>
            </div> 
            <div id="account" class="selectable">
                <div class="toolbar">
                    <h1>您的交易账户</h1>
                    <a class="button cube" href="#home">分时图</a>
                </div>
                <div class="scroll" >
                    <ul class="individual" id="block1">
                    <li class="marketarea">
                        <div class="markettitle"><a id="time"></a></div>
                        <div class="market">
                            <table class="markettable">
          <tr><th></th><th>价格</th><th>手数</th></tr>          
          <tr><td>卖5</td><td><a id="sp5"></a></td><td><a id="sh5"></a></td></tr>
          <tr><td>卖4</td><td><a id="sp4"></a></td><td><a id="sh4"></a></td></tr>
          <tr><td>卖3</td><td><a id="sp3"></a></td><td><a id="sh3"></a></td></tr>
          <tr><td>卖2</td><td><a id="sp2"></a></td><td><a id="sh2"></a></td></tr>
          <tr><td>卖1</td><td><a id="sp1"></a></td><td><a id="sh1"></a></td></tr>          
          <tr><td>最新</td><td><a id="marketprice"></a></td></tr>          
          <tr><td>买1</td><td><a id="bp1"></a></td><td><a id="bh1"></a></td></tr>
          <tr><td>买2</td><td><a id="bp2"></a></td><td><a id="bh2"></a></td></tr>
          <tr><td>买3</td><td><a id="bp3"></a></td><td><a id="bh3"></a></td></tr>
          <tr><td>买4</td><td><a id="bp4"></a></td><td><a id="bh4"></a></td></tr>
          <tr><td>买5</td><td><a id="bp5"></a></td><td><a id="bh5"></a></td></tr>
                            </table >
                        </div>
                        

                    </li>                      
                    <li><input type="number" pattern="[1-9][0-9]*" name="number" placeholder="输入价格" id="price"/></li>
                    <li><input type="number" pattern="[1-9][0-9]*" name="number" placeholder="输入数量" id="hand" /></li>
                    <li><a id="ask" class="btn btn-red action" >买入</a></li>
                    <li><a id="bid" class="btn btn-green action" >卖出</a></li>
                    <li><a class="btn btn-black" href="#entrustleft">撤单<small id="count" class="counter"></small></a></li>                                
                    </ul>

                            <ul id="accountinfo" class="rounded">        
                                <li class="forward"><a href="/rank.php?openid=<?php echo $_GET['openid']?>">查看盈利排名</a></li> 
                                <li><a>现金<small id="cash" class="counter"></small></a></li>
                                <li><a>冻结资金<small id="frozen" class="counter"></small></a></li>
                                <li><a>持仓<small id="position" class="counter"></small></a></li>
                                <li><a>可卖<small id="marketposition" class="counter"></small></a></li>
                                <li><a>平均成本价<small id="avecost" class="counter"></small></a></li>
                                <li><a>浮动盈亏<small id="profit" class="counter"></small></a></li>
                                <li><a>总资产市值<small id="asset" class="counter"></small></a></li>    
                                
                                
                            </ul>

                </div>
               
            </div>
        </div>
    </body>
    <script>
      if (window.navigator.standalone) {
        $("meta[name='apple-mobile-web-app-status-bar-style']").remove();
      }
var loaded=0
var servertime = new Date("<?php echo date('Y-m-d H:i:s'); ?>"),
    clienttime = new Date(),
    diff = servertime - clienttime;
function checkclock()
{
    var c = new Date(),
        s = new Date( c.getTime() + diff);
    if ( s.getSeconds() % 10 == 2) {
        return true;
    }
}

 function getdata(op){
          var openid="<?php echo $_GET['openid']?>";
          
          $.getJSON('/getData.php',{openid:openid,op:op}, function(data) {           
                  $('#cash').html(data.cash);
                  $('#frozen').html(data.frozen);
                  $('#position').html(data.position+"手");
                  $('#marketposition').html(data.marketposition+"手");
                  if(loaded==0){
                    //$('#price').val(data.marketprice);     
                    loaded=1;
                  }
                  $('#marketprice').html(data.marketprice);
                  $('#avecost').html(data.avecost);
                  $('#asset').html(data.asset);
                  $('#count').html(data.count);
                  $("#entrusts").html("<li class='sep'>委托号&emsp;买卖</th><th>价格&emsp;委托数量&emsp;成交数量&emsp;成交均价</li>");
                  $.each(data.orders, function(i,order){
                      var trans="卖出";
                      if(order.buy){trans="买入";}
                      $("#entrusts").append("<li><a class='action' onclick='cancel("+order.id+")'>"+order.id+"&emsp;"+trans+"&emsp;"+order.price+"&emsp;"+order.hand+"&emsp;"+order.dealhand+"&emsp;"+order.avedealprice+"</a></li>");
                  });
  
                  var profit=0;
                  if(data.avecost>0){
                      profit=Math.round((data.marketprice/data.avecost-1)*100,2);
                  }
                  $('#profit').html(profit+"%");
                  
                  if(op==1){
                  
                  $.each(data.b, function(i,b){
                      if(b[0]!=0){
                        $("#bp"+(i+1)).html(b[0]);$("#bh"+(i+1)).html(b[1]);                          
                      }else{
                        $("#bp"+(i+1)).html("-");$("#bh"+(i+1)).html("-");                               
                      }
                  });
                  $.each(data.s, function(i,s){
                      if(s[0]!=0){
                         $("#sp"+(5-i)).html(s[0]);$("#sh"+(5-i)).html(s[1]);                          
                      }else{
                         $("#sp"+(5-i)).html("-");$("#sh"+(5-i)).html("-");
                      }
                  });                   
                  }
              });      
  
      }   
  
  function order(buy,price,hand){
      var openid="<?php echo $_GET['openid']?>";
      $.getJSON('/order.php',{op:1,buy:buy,price:price,hand:hand,openid:openid}, function(data) {
          if(data.hint==0){
              getdata(0);
              alert("委托成功");
          }else if(data.hint==1){
              alert("对不起，您还有超过10笔交易未成交，不能挂单");
          }else if(data.hint==2){
              alert("对不起，您资金不足");
          }else if(data.hint==3){
              alert("对不起，您持仓不足");
          }        
      });  
  }
  function changehint(entrustid){
    $("#confirmcancel").html("确认撤销"+entrustid+"号委托交易？");
    $("#cancelorder").attr("onclick","cancel("+entrustid+")");
  }
  function cancel(entrustid){
      var openid="<?php echo $_GET['openid']?>";      
      if(confirm("确认撤销"+entrustid+"号委托交易？")){
      $.getJSON('/order.php',{op:0,entrustid:entrustid,openid:openid}, function(data) {
              if(data.hint==0){
                  alert("无此委托单");
              }else if(data.hint==1){
                  alert("已经撤销，不可重复撤销");
              }else if(data.hint==2){
                  alert("已经全部成交，不可撤销");
              }else{
                  alert("撤单成功");
              }
              getdata(0);
          }); 
    }
      
  }
  

$(document).ready(function() {


      getdata(1);
    setInterval(function(){
                        if (checkclock()) {
                            getdata(1);                            
                        }                        
                        var c = new Date(),
                            time = new Date( c.getTime() + diff);
                        $('#time').html(time.toLocaleTimeString());
                    }, 1000);
  
      $("#ask").click(function(){
          var price=$('#price').val();
          var hand=$('#hand').val();
          //$("#confirmcancel").html("确认挂单“每手"+price+"买入"+hand+"手”？");
          //$("#cancelorder").attr("onclick","order(1,"+price+","+hand+")");
          if(confirm("确认挂单“每手"+price+"买入"+hand+"手”？"))
          {
            order(1,price,hand);
          }
       });
      $("#bid").click(function(){
          var price=$('#price').val();
          var hand=$('#hand').val();
          //$("#confirmcancel").html("确认挂单“每手"+price+"卖出"+hand+"手”？");
          //$("#cancelorder").attr("onclick","order(0,"+price+","+hand+")");
          if(confirm("确认挂单“每手"+price+"卖出"+hand+"手”？"))
          {
            order(0,price,hand);
          }
          
      });


    $("#chartCandle").hide();

    $("#today").click(function () {
        $('#chartCandle').hide( 1000 );
        $("#chartToday").show( 1000 );
    });

    $("#candle").click(function () {
        $("#chartToday").hide( 1000 );
        $("#chartCandle").show( 1000 );
    });


     var price = [], volume = [];

    $.ajaxSetup({async: false});
    $.getJSON('/getMarket.php',{op:'today'} ,function (points) {
        var current;
        $.each( points, function(index, item) {
            price.push([item[0], item[1]]);
            volume.push([item[0], item[2]]);
            current=item[0];
        })
        for(var t=current;new Date(t).getDate()<servertime.getDate()+1;t+=60000){     
            price.push([t, null]);
            volume.push([t, null]);
        }
        
    })


    Highcharts.setOptions({
        global : {
            useUTC : false
        }
    });

    $('#chartToday').highcharts('StockChart', {
        chart : {
            events : {
                load : function () {
                    var seriesP = this.series[0],
                        seriesV = this.series[1];

                    setInterval(function () {
                        if (checkclock()) {
                            $.getJSON('/getMarket.php',{op:"live"}, function (point) {
                                seriesP.addPoint([point[0], point[1]]);
                                seriesV.addPoint([point[0], point[2]]);

                            })
                        }
                        
                    }, 1000);
                }
            }
        },

        rangeSelector: {
            allButtonsEnabled: false,
            buttons: [{
                count: 1,
                type: 'day',
                text: 'Day'
            }],
            inputEnabled: $('#container').width() > 480,
            selected: 0
        },

        title : {
            text : '今日走势'
        },

        yAxis: [{
            labels: {
                align: 'right',
                x: -3
            },
            height: '60%', 
            lineWidth: 2
        }, {
            labels: {
                align: 'right',
                x: -3
            },
            title: {
                text: '成交量'
            },
            top: '65%',
            height: '35%',
            offset: 0,
            lineWidth: 2
        }],

        series: [{
            type: 'area',
            name: '价格',
            data: price,
            tooltip: {
                    valueDecimals: 2
                },
            fillColor : {
                    linearGradient : {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops : [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
            threshold: null
            
        }, {
            type: 'column', 
            name: '成交量',
            data: volume,
            yAxis: 1
        }]
    });

    $.getJSON('/getMarket.php',{op:"candle"}, function (data) {

        // split the data set into ohlc and volume
        var ohlc = [],
            volume = [],
            dataLength = data.length,
            // set the allowed units for data grouping
            groupingUnits = [[
                'week',                         // unit name
                [1]                             // allowed multiples
            ], [
                'month',
                [1, 2, 3, 4, 6]
            ]],

            i = 0;

        for (i; i < dataLength; i += 1) {
            ohlc.push([
                data[i][0], // the date
                data[i][1], // open
                data[i][2], // high
                data[i][3], // low
                data[i][4] // close
            ]);

            volume.push([
                data[i][0], // the date
                data[i][5] // the volume
            ]);
        }


        // create the chart
    $('#chartCandle').highcharts('StockChart', {

            rangeSelector: {
                inputEnabled: $('#chartCandle').width() > 480,
                selected: 1
            },

            title: {
                text: 'K线图'
            },

            yAxis: [{
                labels: {
                    align: 'right',
                    x: -3
                },
                
                height: '60%',
                lineWidth: 2
            }, {
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: '成交量'
                },
                top: '65%',
                height: '35%',
                offset: 0,
                lineWidth: 2
            }],

            series: [{
                type: 'candlestick',
                data: ohlc,
                dataGrouping: {
                    units: groupingUnits
                }
            }, {
                type: 'column',
                name: '成交量',
                data: volume,
                yAxis: 1,
                dataGrouping: {
                    units: groupingUnits
                }
            }]
        });
    });
});
      
    </script>

</html>
