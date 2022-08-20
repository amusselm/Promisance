<? // All the constants in the game
$dbhost = 'mysql.sourceforge.net';			// MySQL server hostname
$dbuser = 'promisance';			// MySQL username
$dbpass = 'garfield';			// MySQL password
$dbname = 'promisance';			// MySQL database name
$version = '4.0';		// Game version number
$playerdb = 'Players';		// Player table name
$clandb = 'Clan';		// Clan table name
$racedb = 'Races';		// Races table name
$eradb = 'Eras';		// Eras table name
$lotterydb = 'Lottery';		// Lottery table name
$marketdb = 'Market';		// Public Market table name
$messagedb = 'Messages';	// Messages table name
$newsdb = 'News';		// News table name

$config[protection] = 200;	// Duration of protection
$config[initturns] = 100;	// Turns given on signup
$config[maxturns] = 250;	// Max accumulated turns
$config[maxstoredturns] = 100;	// Max stored turns
$config[valturns] = 150;	// How long before validation is necessary

$config[minvacation] = 72;	// Minimum vacation duration
$config[vacationdelay] = 12;	// Delay before empire is protected

$signupsclosed = 0;		// Signups closed?
$lockdb = 0;			// Lock the players database?
$lastweek = 0;			// Last week of the game? don't allow loans

$turnsper = 1;			// X turns
$perminutes = 10;		// per Y minutes
$turnoffset = 0;		// in case we don't run exactly 0 minutes after the hour
				// Note: perminutes must divide evenly into 60

$maxtickets = 3;		// Maximum # of lottery tickets per empire

$tick_curjp = 0;		// DO NOT MODIFY THESE
$tick_lastjp = 1;		// DO NOT MODIFY THESE
$tick_lastnum = 2;		// DO NOT MODIFY THESE
$tick_lastwin = 3;		// DO NOT MODIFY THESE
$tick_jpgrow = 4;		// DO NOT MODIFY THESE

$trplst[0] = 'armtrp';		// an array of troop names so for loops can be used when all types are referenced
$trplst[1] = 'lndtrp';		// DO NOT MODIFY THESE
$trplst[2] = 'flytrp';		// DO NOT MODIFY THESE
$trplst[3] = 'seatrp';		// DO NOT MODIFY THESE
$trplst[4] = 'food';		// this commonly follows the troop listings

$config[armtrp] = 500;		// Base market costs
$config[lndtrp] = 1000;
$config[flytrp] = 2000;
$config[seatrp] = 3000;
$config[food] = 30;

$config[loanbase] = 5;		// Base savings/loan rates
$config[savebase] = 4;
$config[buildings] = 2500;	// Base building cost
$config[market] = 6;		// Hours to arrive on market
$config[bmperc] = 8450;		// Percentage of troops that can be sold on black market (divide by 100 for percentage)
$config[mktshops] = 0.20;	// Percentage of black market cost bonus for which shops are responsible
$config[indc] = 2.8;		// Industry output multiplier
$config[jackpot] = 100000000;	// Base jackpot

$styles = array(1=>"promisance.css");
$stylenames = array(1=>"Default Theme");

				// News text
$config[news] = '<span style="color:white">Welcome to Promisance!</span>';
				// Name of primary script file. DO NOT MODIFY
$config[main] = 'promisance.php';
				// Site/path in which the game resides
$config[sitedir] = '';
				// server title
$config[servname] = 'Promisance';
				// where we go when we logout
$config[home] = 'http://www.promisance.com';
				// link to forum for this game
$config[forums] = 'http://www.promisance.com';
				// administrative contact
$config[adminemail] = 'ppurgett@users.sourceforge.net';
				// From address of validation emails
$config[valemail] = 'nobody@promisance.com';

$time = time();			// not really constants, but stuff used for all pages
$datetime = date('r');
$cookie = $HTTP_COOKIE_VARS;
?>
