# encoding: utf-8
import MySQLdb,time
db =  MySQLdb.connect("localhost", "root", "1234567890","app_alphastock")
cur = db.cursor()

print "market:"
cur.execute("SELECT time,price,volume,buysum,sellsum FROM market ORDER BY timestamp desc LIMIT 10")
for row in cur.fetchall():
	for i in range(len(row)):
                print str(row[i])+"\t",
        print ""


print "orders:"
cur.execute("SELECT * FROM orders")
for row in cur.fetchall():
	for i in range(len(row)):
                print str(row[i])+"\t",
        print ""


print "client:"
print "available\tfrozen\tmarketposition\tfrozenposition\topenid\tremarks"
cur.execute("SELECT available,frozen,marketposition,frozenposition,openid,remarks FROM client")
for row in cur.fetchall():
	for i in range(len(row)):
        	print str(row[i])+"\t",
	print "" 
