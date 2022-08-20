###
### Table structure for table 'clan'
###


CREATE TABLE clan (
  num smallint unsigned NOT NULL auto_increment,
  founder mediumint unsigned NOT NULL default 0,
  asst mediumint unsigned NOT NULL default 0,
  fa1 mediumint unsigned NOT NULL default 0,
  fa2 mediumint unsigned NOT NULL default 0,
  ally1 smallint unsigned NOT NULL default 0,
  ally2 smallint unsigned NOT NULL default 0,
  ally3 smallint unsigned NOT NULL default 0,
  war1 smallint unsigned NOT NULL default 0,
  war2 smallint unsigned NOT NULL default 0,
  war3 smallint unsigned NOT NULL default 0,
  pic tinytext NOT NULL,
  url tinytext NOT NULL,
  motd text NOT NULL,
  members smallint NOT NULL default 1,
  name tinytext NOT NULL,
  tag tinytext NOT NULL,
  password tinytext NOT NULL,
  PRIMARY KEY (num)
) TYPE=MyISAM;

###
### Table structure for table 'eras'
###

CREATE TABLE eras (
  id tinyint unsigned NOT NULL auto_increment,
  name tinytext NOT NULL,
  peasants tinytext NOT NULL,
  food tinytext NOT NULL,
  runes tinytext NOT NULL,
  armtrp tinytext NOT NULL,
  lndtrp tinytext NOT NULL,
  flytrp tinytext NOT NULL,
  seatrp tinytext NOT NULL,
  wizards tinytext NOT NULL,
  homes tinytext NOT NULL,
  shops tinytext NOT NULL,
  industry tinytext NOT NULL,
  barracks tinytext NOT NULL,
  labs tinytext NOT NULL,
  farms tinytext NOT NULL,
  towers tinytext NOT NULL,
  o_armtrp tinyint unsigned NOT NULL default 0,
  d_armtrp tinyint unsigned NOT NULL default 0,
  o_lndtrp tinyint unsigned NOT NULL default 0,
  d_lndtrp tinyint unsigned NOT NULL default 0,
  o_flytrp tinyint unsigned NOT NULL default 0,
  d_flytrp tinyint unsigned NOT NULL default 0,
  o_seatrp tinyint unsigned NOT NULL default 0,
  d_seatrp tinyint unsigned NOT NULL default 0,
  PRIMARY KEY (id)
) TYPE=MyISAM;

###
### Table structure for table 'lottery'
###

CREATE TABLE lottery (
  num int unsigned NOT NULL default 0,
  ticket int unsigned NOT NULL default 0,
  cash bigint unsigned NOT NULL default 0,
  KEY num (num),
  KEY ticket (ticket)
) TYPE=MyISAM;

###
### Table structure for table 'market'
###

CREATE TABLE market (
  id int unsigned NOT NULL auto_increment,
  type tinytext NOT NULL,
  seller mediumint unsigned NOT NULL default 0,
  amount bigint unsigned NOT NULL default 0,
  price int unsigned NOT NULL default 0,
  time int NOT NULL default 0,
  PRIMARY KEY (id),
  KEY price (price),
  KEY time (time)
) TYPE=MyISAM;

###
### Table structure for table 'messages'
###

CREATE TABLE messages (
  id int unsigned NOT NULL auto_increment,
  time int NOT NULL default 0,
  src mediumint unsigned NOT NULL default 0,
  dest mediumint unsigned NOT NULL default 0,
  msg text NOT NULL,
  replied tinyint unsigned NOT NULL default 0,
  deleted tinyint unsigned NOT NULL default 0,
  PRIMARY KEY (id),
  KEY dest (dest),
  KEY time (time),
  KEY deleted (deleted)
) TYPE=MyISAM;

###
### Table structure for table 'news'
###

CREATE TABLE news (
  time int NOT NULL default 0,
  num_s mediumint unsigned NOT NULL default 0,
  clan_s smallint unsigned NOT NULL default 0,
  num_d mediumint unsigned NOT NULL default 0,
  clan_d smallint unsigned NOT NULL default 0,
  event smallint unsigned NOT NULL default 0,
  data0 bigint NOT NULL default 0,
  data1 bigint NOT NULL default 0,
  data2 bigint NOT NULL default 0,
  data3 bigint NOT NULL default 0,
  data4 bigint NOT NULL default 0,
  data5 bigint NOT NULL default 0,
  data6 bigint NOT NULL default 0,
  data7 bigint NOT NULL default 0,
  data8 bigint NOT NULL default 0,
  KEY time (time),
  KEY num_s (num_s),
  KEY clan_s (clan_s),
  KEY num_d (num_d),
  KEY clan_d (clan_d),
  KEY event (event)
) TYPE=MyISAM;

###
### Table structure for table 'players'
###

CREATE TABLE players (
#Account Identifiers
  username tinytext NOT NULL,
  password tinytext NOT NULL,
  name tinytext NOT NULL,
  email tinytext NOT NULL,
#Account Stats
  IP tinytext NOT NULL,
  signedup int NOT NULL default 0,
  ismulti tinyint unsigned NOT NULL default 0,
  disabled tinyint unsigned NOT NULL default 0,
  valcode tinytext NOT NULL,
  validated tinyint unsigned NOT NULL default 0,
  online tinyint unsigned NOT NULL default 0,
  vacation smallint unsigned NOT NULL default 0,
  idle int NOT NULL default 0,
  free int NOT NULL default 0,
  style tinyint unsigned NOT NULL default 0,
#Basic Empire Data
  empire tinytext NOT NULL,
  num mediumint unsigned NOT NULL auto_increment,
  race tinyint unsigned NOT NULL default 1,
  era tinyint unsigned NOT NULL default 1,
  rank mediumint unsigned NOT NULL default 0,
#Clan Data
  clan smallint unsigned NOT NULL default 0,
  forces tinyint unsigned NOT NULL default 0,
  allytime int NOT NULL default 0,
#Military Data
  attacks int unsigned NOT NULL default 0,
  offsucc int unsigned NOT NULL default 0,
  offtotal int unsigned NOT NULL default 0,
  defsucc int unsigned NOT NULL default 0,
  deftotal int unsigned NOT NULL default 0,
  kills int unsigned NOT NULL default 0,
#Turns Data
  turns int unsigned NOT NULL default 0,
  turnsstored int unsigned NOT NULL default 0,
  turnsused int unsigned NOT NULL default 0,
#Basic Stats
  networth bigint unsigned NOT NULL default 0,
  cash bigint unsigned NOT NULL default 100000,
  food bigint unsigned NOT NULL default 10000,
  peasants int unsigned NOT NULL default 500,
#Army Data
  armtrp bigint unsigned NOT NULL default 100,
  lndtrp bigint unsigned NOT NULL default 15,
  flytrp bigint unsigned NOT NULL default 10,
  seatrp bigint unsigned NOT NULL default 0,
  health tinyint unsigned NOT NULL default 100,
#Magic Data
  wizards int unsigned NOT NULL default 0,
  runes int unsigned NOT NULL default 500,
  shield int NOT NULL default 0,
  gate int NOT NULL default 0,
#Industry Percentages
  ind_armtrp tinyint unsigned NOT NULL default 25,
  ind_lndtrp tinyint unsigned NOT NULL default 25,
  ind_flytrp tinyint unsigned NOT NULL default 25,
  ind_seatrp tinyint unsigned NOT NULL default 25,
#Land Data
  land int unsigned NOT NULL default 250,
  shops int unsigned NOT NULL default 5,
  homes int unsigned NOT NULL default 20,
  industry int unsigned NOT NULL default 0,
  barracks int unsigned NOT NULL default 5,
  labs int unsigned NOT NULL default 0,
  farms int unsigned NOT NULL default 15,
  towers int unsigned NOT NULL default 0,
  freeland int unsigned NOT NULL default 205,
#Financial Data
  tax tinyint unsigned NOT NULL default 10,
  savings bigint unsigned NOT NULL default 0,
  loan bigint unsigned NOT NULL default 0,
#Private Market Data
  pmkt_armtrp bigint unsigned NOT NULL default 5000,
  pmkt_lndtrp bigint unsigned NOT NULL default 5000,
  pmkt_flytrp bigint unsigned NOT NULL default 5000,
  pmkt_seatrp bigint unsigned NOT NULL default 5000,
  pmkt_food bigint unsigned NOT NULL default 100000,
  bmperarmtrp smallint unsigned NOT NULL default 0,
  bmperlndtrp smallint unsigned NOT NULL default 0,
  bmperflytrp smallint unsigned NOT NULL default 0,
  bmperseatrp smallint unsigned NOT NULL default 0,
#Misc Data
  aidcred tinyint unsigned NOT NULL default 5,
  msgcred tinyint unsigned NOT NULL default 5,
  msgtime int NOT NULL default 0,
  newstime int NOT NULL default 0,
  PRIMARY KEY (num),
  KEY networth (networth),
  KEY rank (rank)
) TYPE=MyISAM;

###
### Table structure for table 'races'
###

CREATE TABLE races (
  id tinyint unsigned NOT NULL auto_increment,
  name tinytext NOT NULL,
  offense float(5,3) NOT NULL default 1.000,
  defense float(5,3) NOT NULL default 1.000,
  bpt float(5,3) NOT NULL default 1.000,
  costs float(5,3) NOT NULL default 1.000,
  magic float(5,3) NOT NULL default 1.000,
  ind float(5,3) NOT NULL default 1.000,
  pci float(5,3) NOT NULL default 1.000,
  expl float(5,3) NOT NULL default 1.000,
  mkt float(5,3) NOT NULL default 1.000,
  food float(5,3) NOT NULL default 1.000,
  runes float(5,3) NOT NULL default 1.000,
  farms float(5,3) NOT NULL default 1.000,
  PRIMARY KEY (id)
) TYPE=MyISAM;

###
### Predefined lottery entries, MUST be present!
###

INSERT INTO lottery
	( num, ticket, cash     ) VALUES
	( 0  , 0     , 100000000),	# current JP
	( 0  , 1     , 100000000),	# last JP
	( 0  , 2     , 0        ),	# last num
	( 0  , 3     , 0        ),	# last winner
	( 0  , 4     , 0        );	# amount JP grew (if no winner)

###
### Races are defined here.
###

INSERT INTO races
	( name	  , offense, defense, bpt  , costs, magic, ind  , pci  , expl , mkt  , food , runes, farms) VALUES
	('Human'  , 1.000  , 1.000  , 1.000, 1.000, 1.000, 1.000, 1.000, 1.000, 1.000, 1.000, 1.000, 1.000),
	('Elf'    , 0.860  , 0.980  , 0.900, 1.000, 1.180, 0.880, 1.020, 1.120, 1.000, 1.000, 1.120, 0.940),
	('Dwarf'  , 1.060  , 1.160  , 1.160, 1.080, 0.840, 1.120, 1.000, 0.820, 1.080, 1.000, 1.000, 1.000),
	('Troll'  , 1.240  , 0.900  , 1.080, 1.000, 0.880, 1.000, 1.040, 1.140, 1.120, 1.000, 0.920, 0.920),
	('Gnome'  , 0.840  , 1.100  , 1.000, 0.940, 1.000, 0.900, 1.100, 0.880, 0.760, 1.000, 0.880, 1.000),
	('Gremlin', 1.100  , 0.940  , 1.000, 1.000, 0.900, 0.860, 0.800, 1.000, 0.920, 0.860, 1.000, 1.180),
	('Orc'    , 1.160  , 1.000  , 1.040, 1.140, 0.960, 1.080, 1.000, 1.220, 1.000, 1.100, 0.860, 0.920),
	('Drow'   , 1.140  , 1.060  , 0.880, 1.100, 1.180, 1.000, 1.000, 0.840, 1.000, 1.000, 1.060, 0.940),
	('Goblin' , 0.820  , 0.840  , 1.000, 0.820, 1.000, 1.140, 1.000, 1.000, 1.060, 0.920, 1.000, 1.000);

###
### Eras are defined here, MUST BE IN CHRONOLOGICAL ORDER!
###

INSERT INTO eras
	( name    , peasants  , food      , runes     , armtrp   , lndtrp      , flytrp      , seatrp       , wizards    , homes      , shops            , industry    , barracks     , labs           , farms        , towers        , o_armtrp, d_armtrp, o_lndtrp, d_lndtrp, o_flytrp, d_flytrp, o_seatrp, d_seatrp) VALUES
	('Past'   ,'Peasants' ,'Grains'   ,'Mana'     ,'Footmen' ,'Catapults'  ,'Zeppelins'  ,'Galleons'    ,'Wizards'   ,'Huts'      ,'Markets'         ,'Blacksmiths','Keeps'       ,'Mage Towers'   ,'Farms'       ,'Guard Towers' , 1       , 2       , 3       , 2       , 7       , 5       , 7       , 6       ),
	('Present','Civilians','Nutrients','Energy'   ,'Infantry','Tanks'      ,'Jets'       ,'Battleships' ,'Telepaths' ,'Apartments','Business Centers','Factories'  ,'Bases'       ,'PSI Centers'   ,'Plantations' ,'Bunkers'      , 2       , 1       , 2       , 6       , 5       , 3       , 6       , 8       ),
	('Future' ,'Drones'   ,'Batteries','Bandwidth','Cyborgs' ,'Juggernauts','Hovercrafts','Dreadnoughts','Master AIs','Giliads'   ,'E-Commerce Sites','Replicators','Storage Bays','Supercomputers','Power Plants','Laser Turrets', 1       , 2       , 5       , 2       , 6       , 3       , 7       , 7       );

