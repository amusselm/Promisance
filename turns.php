<?
include("const.php");
include("funcs.php");
if ($REQUEST_URI)
{
	HTMLbegin("Error");
	print "Access forbidden!<br>\n";
	HTMLend();
	exit;
}
if (!$link = @mysql_connect($dbhost,$dbuser,$dbpass))
{
	print "Error! Database unavailable!\n";
	exit;
}
mysql_select_db($dbname);
$hour = date("H");
$min = date("i");
if ($lockdb)
{
	print "Database is currently locked! No turns given...\n";
		// so everyone doesn't get deleted when turns start running
	mysql_query("UPDATE $playerdb SET idle=$time;");
}
else
{
	print "$datetime: Processing turns...";
	if ($min == $turnoffset)
	{
		if ($hour == 12)
		{
			randomize();

			$lotterynum = mt_rand(1,3 * sqleval("SELECT num FROM $playerdb ORDER BY num DESC LIMIT 1;"));

			$jackpot = sqleval("SELECT cash FROM $lotterydb WHERE num=0 AND ticket=$tick_curjp;");
			$lastjackpot = sqleval("SELECT cash FROM $lotterydb WHERE num=0 AND ticket=$tick_lastjp;");

			mysql_query("UPDATE $lotterydb SET cash=$lotterynum WHERE num=0 AND ticket=$tick_lastnum;");

			if ($lastjackpot > $jackpot)
				$lastjackpot = $config[jackpot];
			mysql_query("UPDATE $lotterydb SET cash=$jackpot WHERE num=0 AND ticket=$tick_lastjp;");
			mysql_query("UPDATE $lotterydb SET cash=($jackpot-$lastjackpot) WHERE num=0 AND ticket=$tick_jpgrow;");

			$win = sqleval("SELECT num FROM $lotterydb WHERE num>0 AND ticket=$lotterynum;");
			if ($win)
			{
				$winner = loadUser($win);
				addNews(101,$winner,$winner,$jackpot);
		               	$winner[cash] += $jackpot;
				saveUserDataNet($winner,"networth cash");
				mysql_query("UPDATE $lotterydb SET cash=$config[jackpot] WHERE num=0 AND ticket=$tick_curjp;");
			}
			else	$win = 0;
			mysql_query("UPDATE $lotterydb SET cash=$win WHERE num=0 AND ticket=$tick_lastwin;");
			mysql_query("DELETE FROM $lotterydb WHERE num>0;");
		}
		mysql_query("OPTIMIZE TABLE lottery, market;");	// the only tables that get deleted from

		mysql_query("UPDATE $playerdb SET aidcred=(aidcred+1) WHERE aidcred<5;");
		mysql_query("UPDATE $playerdb SET attacks=(attacks-1) WHERE attacks>0;");
		mysql_query("UPDATE $playerdb SET vacation=(vacation+1) WHERE vacation>0;");
	}
	mysql_query("UPDATE $playerdb SET turns=(turns+$turnsper) WHERE vacation=0 AND disabled<=2;");
	mysql_query("UPDATE $playerdb SET turns=(turns+1),turnsstored=(turnsstored-1) WHERE vacation=0 AND disabled<=2 AND turnsstored>0;");
	mysql_query("UPDATE $playerdb SET turnsstored=(turnsstored+(turns-$config[maxturns])),turns=$config[maxturns] WHERE turns>$config[maxturns];");
	mysql_query("UPDATE $playerdb SET turnsstored=$config[maxstoredturns] WHERE turnsstored>$config[maxstoredturns];");

	mysql_query("UPDATE $playerdb SET msgcred=(msgcred+1) WHERE msgcred<5;");

	mysql_query("UPDATE $playerdb SET bmperarmtrp=(bmperarmtrp-(100*(1+shops/land))) WHERE bmperarmtrp > (100*(1+shops/land));");
	mysql_query("UPDATE $playerdb SET bmperlndtrp=(bmperlndtrp-(100*(1+shops/land))) WHERE bmperlndtrp > (100*(1+shops/land));");
	mysql_query("UPDATE $playerdb SET bmperflytrp=(bmperflytrp-(100*(1+shops/land))) WHERE bmperflytrp > (100*(1+shops/land));");
	mysql_query("UPDATE $playerdb SET bmperseatrp=(bmperseatrp-(100*(1+shops/land))) WHERE bmperseatrp > (100*(1+shops/land));");
	mysql_query("UPDATE $playerdb SET bmperarmtrp=0 WHERE bmperarmtrp < (100*(1+shops/land));");
	mysql_query("UPDATE $playerdb SET bmperlndtrp=0 WHERE bmperlndtrp < (100*(1+shops/land));");
	mysql_query("UPDATE $playerdb SET bmperflytrp=0 WHERE bmperflytrp < (100*(1+shops/land));");
	mysql_query("UPDATE $playerdb SET bmperseatrp=0 WHERE bmperseatrp < (100*(1+shops/land));");

	mysql_query("UPDATE $playerdb SET forces=(forces - 1) WHERE forces < 11 and forces > 0 ;");  // works the unsharing of troops

	mysql_query("UPDATE $playerdb SET pmkt_armtrp=(pmkt_armtrp+(8*(land + barracks))) WHERE pmkt_armtrp < (250 * (land+(2 * barracks)));");
	mysql_query("UPDATE $playerdb SET pmkt_lndtrp=(pmkt_lndtrp+(5*(land + barracks))) WHERE pmkt_lndtrp < (200 * (land+(2 * barracks)));");
	mysql_query("UPDATE $playerdb SET pmkt_flytrp=(pmkt_flytrp+(3*(land + barracks))) WHERE pmkt_flytrp < (180 * (land+(2 * barracks)));");
	mysql_query("UPDATE $playerdb SET pmkt_seatrp=(pmkt_seatrp+(2*(land + barracks))) WHERE pmkt_seatrp < (150 * (land+(2 * barracks)));");
	mysql_query("UPDATE $playerdb SET pmkt_food=(pmkt_food+(50*(land + farms))) WHERE pmkt_food < (2000 * (land + farms));");
	mysql_query("UPDATE $playerdb SET online=0 WHERE idle<($time-(3600 / $perminutes));");	// set 'em offline after they're idle for 2 turns updates

	mysql_query("UPDATE $playerdb SET idle=$time WHERE password='farm';");	// so the land farms won't idle to death

	/* new players must validate their accounts within 48 hours */
	/* you can not idle for more than 7 days unless you are on vacation or disabled */
	/* dead empires get deleted after 3 days */
	/* empires marked for deletion are deleted immediately */
	$delusers = mysql_query("SELECT * FROM $playerdb WHERE
		(validated=0 AND disabled=1 AND idle<($time-86400*2)) OR
		(disabled<=1 AND vacation=0 AND land>0 AND idle<($time-86400*7)) OR
		(land=0 AND disabled=1 AND ip!='0.0.0.0' AND idle<($time-86400*3)) OR
		(disabled=4)
			;");
	while ($users = mysql_fetch_array($delusers))
	{
		print "Deleting user $users[empire] (#$users[num])...\n";
		if ($users[clan])						// remove user from clan
		{
			$clan = loadClan($users[clan]);
			if ($clan[founder] == $users[num])
			{							// transfer ownership if necessary
				if ($newf = mysql_fetch_array(mysql_query("SELECT * FROM $playerdb WHERE clan=$clan[num] AND num!=$users[num] ORDER BY networth DESC;")))
				{
					mysql_query("UPDATE $clandb SET founder=$newf[num] WHERE num=$clan[num];");
					addNews(116,$newf,$newf,0);
				}
			}
			mysql_query("UPDATE $clandb SET members=members-1 WHERE num=$clan[num];");
		}
		mysql_query("UPDATE $messagedb SET deleted=1 WHERE src=$users[num] OR dest=$users[num];");
										// delete any messages to/from that user
		mysql_query("DELETE FROM $marketdb WHERE seller=$users[num];");	// any of the user's items on the market
		mysql_query("DELETE FROM $lotterydb WHERE num=$users[num];");	// any lottery tickets
		$users[name].= ".DEAD.".$time;
		$users[username] .= ".DEAD.".$time;
		$users[password] = md5($users[password]);
		$users[email] .= ".DEAD.".$time;
		$users[disabled] = $users[validated] = 1;
		$users[land] = $users[shops] = $users[homes] = $users[industry] = $users[barracks] = $users[labs] = $users[farms] = $users[towers] = $users[freeland] = 0;
		$users[ip] = "0.0.0.0";
		$users[clan] = 0;
		$users[idle] = $time;
										// and kill the user
		saveUserDataNet($users,"networth name username password email disabled validated land shops homes industry barracks labs farms towers freeland ip clan idle");
	}

	$delclans = mysql_query("SELECT * FROM $clandb WHERE members=0;");
	while ($clan = mysql_fetch_array($delclans))				// remove all associations with
	{									// empty clans and make clan
		print "Deleting clan $clan[name] ($clan[tag])...\n";		// invisible to game
		mysql_query("UPDATE $clandb SET ally1=0 WHERE ally1=$clan[num];");
		mysql_query("UPDATE $clandb SET ally2=0 WHERE ally2=$clan[num];");
		mysql_query("UPDATE $clandb SET ally3=0 WHERE ally3=$clan[num];");
		mysql_query("UPDATE $clandb SET  war1=0 WHERE  war1=$clan[num];");
		mysql_query("UPDATE $clandb SET  war2=0 WHERE  war2=$clan[num];");
		mysql_query("UPDATE $clandb SET  war3=0 WHERE  war3=$clan[num];");
		mysql_query("UPDATE $clandb SET members=-1 WHERE num=$clan[num];");
										// must keep for news
	}

	print "...done!  ";
}
print "Updating ranks...";
$users = mysql_query("SELECT num FROM $playerdb ORDER BY networth DESC;");
$urank = 0;
while ($user = mysql_fetch_array($users))
{
	$urank++;
	mysql_query("UPDATE $playerdb SET rank=$urank WHERE num=$user[num];");
}
print "done!\n\n";
?>
