SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

USE app_alphastock;



DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `openid` varchar(30) NOT NULL,
  `opentime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `available` int(11) NOT NULL DEFAULT '10000',
  `frozen` int(11) NOT NULL DEFAULT '0',
  `marketposition` int(11) NOT NULL DEFAULT '0',
  `frozenposition` int(11) NOT NULL DEFAULT '0',
  `cost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0',
  `avecost` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0',
  `asset` int(11) NOT NULL DEFAULT '0',
  `initial` int(11) NOT NULL DEFAULT '10000',
  `gain` int(11) NOT NULL DEFAULT '0',
  `rank` int(11) NOT NULL DEFAULT '1',
  `remarks` text NOT NULL DEFAULT '',
  PRIMARY KEY (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `client` (`openid`, `opentime`, `remarks`) VALUES
('oNJ_Ptzd5xf0ZNk7s3Htnd7vNtzc', '2014-07-30 18:56:32', 'me'),
('oNJ_Pt2yWalMSCAu9t0eTdxhnvhU', '2014-07-30 19:36:43', 'chen xiangyao'),
('oNJ_Pt97LLnLoa1THhd6tZrcjJSQ', '2014-07-30 21:33:26', 'pang haitian'),
('oNJ_Pt3PXVN49Iqyt19zR87YZ2Rg', '2014-08-05 19:03:38', 'wan sheng'),
('oNJ_Pt5EzV1RGdkHisZR437PaOsQ', '2014-08-05 20:01:00', 'wan sheng\'s girl'),
('oNJ_Pt5VbFi3TtAqMBD6X2rCjbEk', '2014-08-14 15:01:00', 'mom'),
('oNJ_Pt0h81yyysx95-euUb4f5ikM', '2014-08-16 14:00:00', 'dong chenghao');
-- me
UPDATE client SET marketposition=100,cost=10000,avecost=100,initial=20000 where openid='oNJ_Ptzd5xf0ZNk7s3Htnd7vNtzc';
-- wan sheng
UPDATE client SET available=0,marketposition=100,cost=10000,avecost=100 where openid='oNJ_Pt3PXVN49Iqyt19zR87YZ2Rg';

DROP TABLE IF EXISTS `entrusts`;
CREATE TABLE IF NOT EXISTS `entrusts` (
  `entrustid` int(11) NOT NULL AUTO_INCREMENT,
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `openid` varchar(30) NOT NULL,
  `buy` tinyint(1) NOT NULL,
  `price` int(11) NOT NULL,
  `hand` int(11) NOT NULL,
  `dealhand` int(11) NOT NULL DEFAULT '0',
  `dealsum` int(11) NOT NULL DEFAULT '0',
  `cancelled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`entrustid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `entrustid` int(11) NOT NULL AUTO_INCREMENT,
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `openid` varchar(30) NOT NULL,
  `price` int(11) NOT NULL,
  `hand` int(11) NOT NULL,
  `buy` tinyint(1) NOT NULL,
  PRIMARY KEY (`entrustid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `deals`;
CREATE TABLE IF NOT EXISTS `deals` (
  `dealid` int(11) NOT NULL AUTO_INCREMENT,
  `buyentrustid` int(11) NOT NULL,
  `sellentrustid` int(11) NOT NULL,
  `buyopenid` varchar(30) NOT NULL,
  `sellopenid` varchar(30) NOT NULL,
  `dealprice` int(11) NOT NULL,
  `dealhand` int(11) NOT NULL,
  `inside` int(11) NOT NULL,
  `dealtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dealid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `market`;
CREATE TABLE IF NOT EXISTS `market` (
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `price` int(11) NOT NULL DEFAULT '100',
  `volume` int(11) NOT NULL DEFAULT '0',
  `buysum` int(11) NOT NULL DEFAULT '0',
  `sellsum` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `market`  VALUES ();

DROP TABLE IF EXISTS `bids`;
CREATE TABLE IF NOT EXISTS `bids` (
  `rank` varchar(2),
  `price` int(11) ,
  `hand` int(11) ,
  PRIMARY KEY (`rank`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `bids`  VALUES 
('s5',0,0),
('s4',0,0),
('s3',0,0),
('s2',0,0),
('s1',0,0),
('b1',0,0),
('b2',0,0),
('b3',0,0),
('b4',0,0),
('b5',0,0);
