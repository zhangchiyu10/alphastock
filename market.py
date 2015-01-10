# encoding: utf-8
import MySQLdb,time
datetime=time.strftime('%Y%m%d%H%M%S',time.localtime(time.time()))
db =  MySQLdb.connect("localhost", "root", "1234567890","app_alphastock")
cur = db.cursor()
cur.execute("SELECT price FROM market ORDER BY timestamp desc LIMIT 1")
preMarketPrice= cur.fetchone()
preMarketPrice=int(preMarketPrice[0])
marketPrice=preMarketPrice
volume=0
buy=db.cursor()
sell=db.cursor()
buy.execute("SELECT * FROM orders WHERE buy=1 ORDER BY price desc, entrustid")
sell.execute("SELECT * FROM orders WHERE buy=0 ORDER BY price, entrustid")
sellrow=sell.fetchall()
num_sell=len(sellrow)
sellorderno=0
sellhand=0
dealend=False    
hasbuy=False
for buyrow in buy.fetchall():
    hasbuy=True
    buyorderid=buyrow[0]
    buytime=buyrow[1]
    buyclient=buyrow[2]
    buyprice=int(buyrow[3])
    buyhand=int(buyrow[4])
    while buyhand>0:        
        nosell=True
        if sellhand>0:
            nosell=False            
        elif sellorderno<num_sell:
            sellorderid=sellrow[sellorderno][0]
            selltime=sellrow[sellorderno][1]
            sellclient=sellrow[sellorderno][2]
            sellprice=int(sellrow[sellorderno][3])
            sellhand=int(sellrow[sellorderno][4])
            sellorderno+=1
            nosell=False            
        if not nosell:           
            if buyprice>=sellprice:
                marketPrice=preMarketPrice
                inside=2
                if buyprice<preMarketPrice:
                    marketPrice=buyprice
                    inside=1
                if sellprice>preMarketPrice:
                    marketPrice=sellprice
                    inside=0
                dealhand=min(sellhand,buyhand)
                sellhand-=dealhand
                buyhand-=dealhand
                volume+=dealhand
                if sellhand==0:
                   	cur.execute("DELETE FROM orders WHERE entrustid='%s'" % sellorderid) 
                if buyhand==0:
                    cur.execute("DELETE FROM orders WHERE entrustid='%s'" % buyorderid) 
                cur.execute("UPDATE client SET frozen=frozen-%s,available=available+%s,cost=cost+%s,marketposition=marketposition+%s WHERE openid='%s'" % (buyprice*dealhand,(buyprice-marketPrice)*dealhand,marketPrice*dealhand,dealhand,buyclient))
                cur.execute("UPDATE client SET frozenposition=frozenposition-%s,available=available+%s,cost=cost-%s*avecost WHERE openid='%s'" % (dealhand,marketPrice*dealhand,dealhand,sellclient))   
                cur.execute("UPDATE client SET avecost=cost/(frozenposition+marketposition) WHERE openid='%s' or openid='%s'" % (buyclient,sellclient))
                
                
                cur.execute("INSERT INTO deals (buyentrustid,sellentrustid,buyopenid,sellopenid,dealprice,dealhand,inside) VALUES('%s','%s','%s','%s','%s','%s','%s')" % (buyorderid,sellorderid,buyclient,sellclient,marketPrice,dealhand,inside))

                cur.execute("UPDATE entrusts SET dealhand=dealhand+%s,dealsum=dealsum+%s WHERE entrustid=%s or entrustid=%s" % (dealhand,marketPrice*dealhand,buyorderid,sellorderid))
                
            else:#not deal 
            	cur.execute("UPDATE orders SET hand=%s WHERE entrustid='%s'" % (buyhand,buyorderid))
                cur.execute("UPDATE orders SET hand=%s WHERE entrustid='%s'" % (sellhand,sellorderid))
                dealend=True
             	break 
        else:#buy only
            cur.execute("UPDATE orders SET hand=%s WHERE entrustid='%s'" % (buyhand,buyorderid))
            dealend=True
            break
    if dealend:
        break
if not dealend:#sell only 1
    if sellhand>0:
        cur.execute("UPDATE orders SET hand=%s WHERE entrustid='%s'" % (sellhand,sellorderid))
        #marketPrice=sellprice
    #elif sellorderno<num_sell:#sell only 2
    	#marketPrice=sellrow[sellorderno][3]
    #else:
        #if hasbuy:
            #marketPrice=max(sellprice,preMarketPrice)        			
            #marketPrice=min(buyprice,marketPrice)


cur.execute("SELECT sum(hand) FROM orders WHERE buy=0")
sellsum=cur.fetchone()
sh=sellsum[0]
if not sh:
    sh=0
cur.execute("SELECT sum(hand) FROM orders WHERE buy=1")
buysum=cur.fetchone()
bh=buysum[0]
if not bh:
    bh=0
cur.execute("INSERT INTO market (date,time,price,volume,buysum,sellsum) VALUES (%s,%s,%d,%d,%d,%d)" % (datetime[0:8],datetime[8:14],marketPrice,volume,bh,sh))
db.commit()

cur.execute("SELECT datetime from market_min where datetime='%s'" % (datetime[0:12]))
minute=cur.fetchone()
if minute:
    cur.execute("UPDATE market_min SET price=%d,volume=volume+%d WHERE datetime='%s'" % (marketPrice,volume,datetime[0:12]))    
else:
    cur.execute("INSERT INTO market_min (datetime,price,volume) VALUES (%s,%d,%d)" % (datetime[0:12],marketPrice,volume))
db.commit()

cur.execute("SELECT high,low from candlestick where date='%s'" % (datetime[0:8]))
candle=cur.fetchone()
if candle:
    cur.execute("UPDATE candlestick SET close=%d,volume=volume+%d where date='%s'" % (marketPrice,volume,datetime[0:8]))
    if int(candle[0])<marketPrice:
        cur.execute("UPDATE candlestick SET high=%d where date='%s'" % (marketPrice,datetime[0:8]))
    if int(candle[1])>marketPrice:
        cur.execute("UPDATE candlestick SET low=%d where date='%s'" % (marketPrice,datetime[0:8]))
else:
    cur.execute("INSERT INTO candlestick VALUES(%s,%d,%d,%d,%d,%d)" % (datetime[0:8],marketPrice,marketPrice,marketPrice,marketPrice,volume))
db.commit()


cur.execute("SELECT price,sum(hand)FROM orders WHERE buy=1 GROUP BY price ORDER BY price DESC LIMIT 5")
f=0
for buydata in cur.fetchall():
    f=f+1
    cur.execute("UPDATE bids set price=%s,hand=%s WHERE rank='%s'" % (buydata[0],buydata[1],"b"+str(f)))
while f<5:
    f=f+1
    cur.execute("UPDATE bids set price=%s,hand=%s WHERE rank='%s'" % (0,0,"b"+str(f)))    
    

cur.execute("SELECT price,sum(hand) FROM orders WHERE buy=0 GROUP BY price ORDER BY price LIMIT 5")
f=0
for selldata in cur.fetchall():
    f=f+1
    cur.execute("UPDATE bids set price=%s,hand=%s WHERE rank='%s'" % (selldata[0],selldata[1],"s"+str(f)))
while f<5:
    f=f+1
    cur.execute("UPDATE bids set price=%s,hand=%s WHERE rank='%s'" % (0,0,"s"+str(f)))    
    


cur.execute("UPDATE client SET \
asset=%d*(marketposition+frozenposition)+(available+frozen),\
gain=asset-initial " % (marketPrice))


cur.execute("SET @rank=0")
cur.execute("UPDATE client,(SELECT @rank:=@rank+1 AS Rank,openid FROM client ORDER BY gain/initial DESC,opentime) as s SET client.rank=s.rank where client.openid=s.openid ")

db.commit()
db.close()
print "%s %d %d %d %d" % (datetime,marketPrice,volume,bh,sh)