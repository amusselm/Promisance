<?
include("html.php");

// adds commas to a number
function commas ($str)
{
	return number_format($str,0,".",",");
}

// replace ' with '' to avoid malformed SQL queries
function sqlQuotes (&$str)
{
	$str = str_replace("'","''",stripslashes($str));
}

// remove commas, make integer
function fixInputNum (&$num)
{
	$num = round(str_replace(",","",$num));
	$num = round(abs($num));
}

// randomize the mt_rand() function
function randomize ()
{
	mt_srand((double)microtime()*1000000);
}

// pluralize a string
function plural ($num, $plur, $sing)
{
	if ($num != 1)
		return $plur;
	else	return $sing;
}

// evaluate an SQL query, return first cell of first row
// useful for "SELECT count(*) ..." queries
function sqleval ($query)
{
	$data = mysql_fetch_row(mysql_query($query));
	return $data[0];
}

// prints a number, colored according to its positivity/negativity.
// Set nosign=1 to omit the +/-
function printCNum ($amt, $prefix, $nosign)
{
	$pos = "+";
	$neg = "-";
	if ($nosign)
		$neg = $pos = "";
?>
<span class=<?
       	if ($amt < 0)
		print '"cbad">'.$neg.$prefix;
	elseif ($amt > 0)
		print '"cgood">'.$pos.$prefix;
	else	print '"cneutral">';
	print	commas(abs($amt));?></span><?
}

// returns the requested networth
function getNetworth (&$user)
{
	global $config;
	return floor(($user[armtrp] * 1) + ($user[lndtrp] * $config[lndtrp] / $config[armtrp]) + ($user[flytrp] * $config[flytrp] / $config[armtrp]) + ($user[seatrp] * $config[seatrp] / $config[armtrp]) + ($user[wizards] * 2) + ($user[peasants] * 3) + (($user[cash] + $user[savings]/2 - $user[loan]*2) / (5 * $config[armtrp])) + ($user[land] * 500) + ($user[freeland] * 100) + ($user[food] * $config[food] / $config[armtrp]));
}

function pci ($user, $race)
{
	return round(25 * (1 + $user[shops] / $user[land]) * $race[pci]);
}

// loads the information for the specified user number
function loadUser ($num)
{
	global $playerdb;
	return mysql_fetch_array(mysql_query("SELECT * FROM $playerdb WHERE num=$num;"));
}

// loads the information for the specified race number
function loadRace ($race)
{
	global $racedb;
	return mysql_fetch_array(mysql_query("SELECT * FROM $racedb WHERE id=$race;"));
}

// loads the information for the specified era number
function loadEra ($era)
{
	global $eradb;
	return mysql_fetch_array(mysql_query("SELECT * FROM $eradb WHERE id=$era;"));
}

// loads the information for the specified clan number
function loadClan ($num)
{
	global $clandb;
	return mysql_fetch_array(mysql_query("SELECT * FROM $clandb WHERE num=$num;"));
}

// Loads all clan tags into an associative array
function loadClanTags ()
{
	global $clandb;
	$clans = mysql_query("SELECT num,tag FROM $clandb;");
	while ($clan = mysql_fetch_array($clans))
		$ctags["$clan[num]"] = $clan[tag];
	$ctags["0"] = "None";
	return $ctags;
}

// Loads all race names into an associative array, intended for the scorelists
function loadRaceTags ()
{
	global $racedb;
	$races = mysql_query("SELECT id,name FROM $racedb;");
	while ($race = mysql_fetch_array($races))
		$rtags["$race[id]"] = $race[name];
	return $rtags;
}

// Loads all era names, intended for the scorelists
function loadEraTags ()
{
	global $eradb;
	$eras = mysql_query("SELECT id,name FROM $eradb;");
	while ($era = mysql_fetch_array($eras))
		$etags["$era[id]"] = $era[name];
	return $etags;
}

// Example: saveUserData($users,"cash networth armtrp lndtrp flytrp seatrp etc");
function saveUserData (&$user, $data)
{
	global $playerdb, $lockdb;
	if ($lockdb)
		return;
	$items = explode(" ",$data);
	$update = "";
	$i = 0;
	while ($tmp = $items[$i++])
	{
		$data = $user[$tmp];
		if (is_numeric($data))
			$update .= "$tmp=$data";
		else
		{
			sqlQuotes($data);
			$update .= "$tmp='$data'";
		}
		if ($items[$i]) $update .= ",";
	}
	if (!mysql_query("UPDATE $playerdb SET $update WHERE num=$user[num];"))
		print "FATAL ERROR: Failed to update player data $update for user #$user[num]!<!--prob1--><BR>\n";
}

// Saves data for a particular user, updating their networth in the process
// MUST specify "networth" among the fields to save!
// Example: saveUserDataNet($users,"networth cash armtrp lndtrp flytrp seatrp etc");
function saveUserDataNet (&$user, $data)
{
	$user[networth] = getNetworth($user);
	return saveUserData($user, $data);
}

// Example: saveClanData($uclan,"name motd ally1 war3");
function saveClanData (&$clan, $data)
{
	global $clandb, $lockdb;
	if ($lockdb)
		return;
	$items = explode(" ",$data);
	$update = "";
	$i = 0;
	while ($tmp = $items[$i++])
	{
		$data = $clan[$tmp];
		if (is_numeric($data))
			$update .= "$tmp=$data";
		else
		{
			sqlQuotes($clan[$tmp]);
			$update .= "$tmp='$data'";
		}
		if ($items[$i]) $update .= ",";
	}
	if (!mysql_query("update $clandb set $update where num=$clan[num];"))
		print "FATAL ERROR: Failed to update clan data $update for clan #$clan[num]!<BR>\n";
}

// function to return amount of land
function gimmeLand($currland, $bonus, $era)
{
	if ($era == 1)
		$multip = 1;
	elseif ($era == 2)
		$multip = 1.4;
	elseif ($era == 3)
		$multip = 1.8;

	return ceil((1 / ($currland * .00022 + .25)) * 20 * $bonus * $multip);
}

function calcSizeBonus ($networth)
{
	if ($networth <= 100000)
		$size = 0.524;
	elseif ($networth <= 500000)
		$size = 0.887;
	elseif ($networth <= 1000000)
		$size = 1.145;
	elseif ($networth <= 10000000)
		$size = 1.294;
	elseif ($networth <= 100000000)
		$size = 1.454;
	else	$size = 1.674;
	return $size;
}

function printStatsBar ()
{
	global $users, $uera, $config;
?>
<table style="width:100%">
<tr class="era<?=$users[era]?>" style="font-size:medium">
    <td class="acenter"><a href="<?=$config[main]?>?action=messages"><?if (numNewMessages() > 0) print "<b>New Mail!</b>"; else print "Mailbox";?></a></td>
    <td class="acenter">Turns: <?=$users[turns]?></td>
    <td class="acenter">Money: $<?=commas($users[cash])?></td>
    <td class="acenter">Land: <?=commas($users[land])?></td>
    <td class="acenter"><?=$uera[runes]?>: <?=commas($users[runes])?></td>
    <td class="acenter"><?=$uera[food]?>: <?=commas($users[food])?></td>
    <td class="acenter">Health: <?=$users[health]?>%</td>
    <td class="acenter">Networth: $<?=commas($users[networth])?></td></tr>
</table>
<?
}

// Take a specified number of turns performing the given action
// Valid actions (so far): cash, land, war; others will be added as necessary
function takeTurns ($numturns, $action)
{
	global $config, $trplst, $time;
	global $users, $urace, $uera, $landgained, $cashgained, $foodgained, $warflag;
	if ($users[era] == 1)
	{
		$urace[ind] *= .95;
		$urace[runes] *= 1.2;
	}
	if ($users[era] == 3)
		$urace[ind] *= 1.15;

	if ($numturns > $users[turns])
		TheEnd('<span class="cbad">FATAL ERROR</span>: attempted to use more turns than available!');

	if (($action == 'cash') || ($action == 'land') || ($action == 'farm'))	// Actions which can be aborted
		$nonstop = 0;
	else	$nonstop = 1;

	$taken = 0;
	while ($taken < $numturns)					// use up specified number of turns
	{
		$taken++;
		$users[networth] = getNetworth($users);
		if ($action == 'land')					// exploring?
		{
			$tmp = gimmeLand($users[land],$urace[expl],$users[era]);
			$users[land] += $tmp;
			$users[freeland] += $tmp;
			$landgained += $tmp;
		}
		$size = calcSizeBonus($users[networth]);		// size bonus/penalty
		$loanrate = $config[loanbase] + $size;			// update savings and loan
		$saverate = $config[savebase] - $size;
		$users[loan] *= 1 + ($loanrate / 52 / 100);
		if ($users[turnsused] > $config[protection])		// no savings interest while under protection
			$users[savings] *= 1 + ($saverate / 52 / 100);
		$users[loan] = round($users[loan]);
		$users[savings] = round($users[savings]);
		if ($users[savings] > ($users[networth] * 10))
			$users[savings] = $users[networth] * 10;
// income
		$income = round(((pci($users,$urace) * ($users[tax] / 100) * ($users[health] / 100) * $users[peasants]) + ($users[shops] * 500)) / $size);
		if ($action == 'cash')					// cashing?
			$income = round($income * 1.25);
		$wartax = 0;
		if ($warflag)						// war tax?
			$wartax = $networth / 1000;
// expenses
		$loanpayed = round($users[loan] / 200);
		$expenses = round(($users[armtrp] * 1) + ($users[lndtrp] * 2.5) + ($users[flytrp] * 4) + ($users[seatrp] * 7) + ($users[land] * 8) + ($users[wizards] * .5));
		$expbonus = round($expenses * ($urace[costs] - ($users[barracks] / $users[land])));
		if ($expbonus > $expenses / 2)				// expenses bonus limit
			$expbonus = round($expenses / 2);
		$expenses -= $expbonus;
		$money = $income - ($expenses + $loanpayed + $wartax);
		$cashgained += $money;
		$users[loan] -= $loanpayed;
		$users[cash] += $money;
// build extra units
		$armtrp = ceil(($users[industry] * ($users[ind_armtrp] / 100)) * 1.2 * $urace[ind] * $config[indc]);
		$users[armtrp] += $armtrp;
		$lndtrp = ceil(($users[industry] * ($users[ind_lndtrp] / 100)) * 0.6 * $urace[ind] * $config[indc]);
		$users[lndtrp] += $lndtrp;
		$flytrp = ceil(($users[industry] * ($users[ind_flytrp] / 100)) * 0.3 * $urace[ind] * $config[indc]);
		$users[flytrp] += $flytrp;
		$seatrp = ceil(($users[industry] * ($users[ind_seatrp] / 100)) * 0.2 * $urace[ind] * $config[indc]);
		$users[seatrp] += $seatrp;
// update food
		$foodpro = round(($users[freeland] * 5) + ($users[farms] * 75) * $urace[farms]);
		$foodcon = round((($users[armtrp] * .05) + ($users[lndtrp] * .03) + ($users[flytrp] * .02) + ($users[seatrp] * .01) + ($users[peasants] * .01) + ($users[wizards] * .25)) * $urace[food]);
		if ($action == 'farm')					// farming?
			$foodpro = round(1.25 * $foodpro);
		$food = $foodpro - $foodcon;
		$users[food] += $food;
		$foodgained += $food;
// health
		if (($users[health] < 100 - (($users[tax] - 10) / 2)) && ($users[health] < 100))
			$users[health]++;
// taxes
		$taxrate = $users[tax] / 100;
		if ($users[tax] > 40)
			$taxpenalty = ($taxrate - 0.40) / 2;
		if ($users[tax] < 20)
			$taxpenalty = ($taxrate - 0.20) / 2;
// update population
		$popbase = round((($users[land] * 2) + ($users[freeland] * 5) + ($users[homes] * 60)) / (0.95 + $taxrate + $taxpenalty));

		if ($users[peasants] != $popbase)
			$peasants = ($popbase - $users[peasants]) / 20;
		if ($peasants > 0)	$peasmult = (4 / (($users[tax] + 15) / 20)) - (7 / 9);
		if ($peasants < 0)	$peasmult = 1 / ((4 / (($users[tax] + 15) / 20)) - (7 / 9));
		$peasants = round($peasants * $peasmult * $peasmult);
		$users[peasants] += $peasants;
// gain magic energy
		$runes = 0;
		if (($users[labs] / $users[land]) > .15)
			$runes = mt_rand(round($users[labs] * 1.1),round($users[labs] * 1.5));
		else	$runes = round($users[labs] * 1.1);
		$runes = round($runes * $urace[runes]);
		$users[runes] += $runes;
		$wizards = 0;
// These values in the midst of adjustment
		if ($users[wizards] < ($users[labs] * 25))
			$wizards = round($users[labs] * 0.45);
		elseif ($users[wizards] < ($users[labs] * 50))
			$wizards = round($users[labs] * 0.30);
		elseif ($users[wizards] < ($users[labs] * 90))
			$wizards = round($users[labs] * 0.15);
		elseif ($users[wizards] < ($users[labs] * 100))
			$wizards = round($users[labs] * 0.10);
		elseif ($users[wizards] > ($users[labs] * 175))
			$wizards = round($users[wizards] * -.05);
		$users[wizards] += $wizards;

// print status report
?>
<table class="empstatus">
<tr><td style="vertical-align:top"><table>
    <tr class="inputtable"><th colspan="2">Economic Status</th></tr>
    <tr><th>Income:</th>
        <td>$<?=commas($income)?></td></tr>
    <tr><th>Expenses:</th>
        <td>$<?=commas($expenses)?></td></tr>
    <tr><th>War Tax:</th>
        <td>$<?=commas($wartax)?></td></tr>
    <tr><th>Loan Pay:</th>
        <td>$<?=commas($loanpayed)?></td></tr>
    <tr><th>Net:</th>
        <td><?printCNum($money,"$",0);?></td></tr>
    </table></td>
    <td style="vertical-align:top"><table>
    <tr class="inputtable"><th colspan="2">Agricultural Status</th></tr>
    <tr><th>Produced:</th>
        <td><?=commas($foodpro)?></td></tr>
    <tr><th>Consumed:</th>
        <td><?=commas($foodcon)?></td></tr>
    <tr><th>Net:</th>
        <td><?printCNum($food,"",0);?></th></tr>
    </table></td>
    <td style="vertical-align:top"><table>
    <tr class="inputtable"><th colspan="2">Population & Military Status</th></tr>
    <tr><th><?=$uera[peasants]?>:</th>
        <td><?printCNum($peasants,"",0);?></td></tr>
    <tr><th><?=$uera[wizards]?>:</th>
        <td><?printCNum($wizards,"",0);?></td></tr>
    <tr><th><?=$uera[runes]?>:</th>
        <td><?printCNum($runes,"",0);?></td></tr>
    <tr><th><?=$uera[armtrp]?>:</th>
        <td><?printCNum($armtrp,"",0);?></td></tr>
    <tr><th><?=$uera[lndtrp]?>:</th>
        <td><?printCNum($lndtrp,"",0);?></td></tr>
    <tr><th><?=$uera[flytrp]?>:</th>
        <td><?printCNum($flytrp,"",0);?></td></tr>
    <tr><th><?=$uera[seatrp]?>:</th>
        <td><?printCNum($seatrp,"",0);?></td></tr>
    </table></td></tr>
</table>
<?
		if (($users[tax] > 40) && ($peasants < 0))
		{
?>
<span class="cbad">Your high tax rate is angering your residents.</span><br>
<?
		}
		elseif (($users[tax] < 20) && ($peasants > 0))
		{
?>
<span class="cgood">Your low tax rate is encouraging immigration.</span><br>
<?
		}
?>
<hr style="width:50%">
<?
		$users[turnsused]++;
		$users[turns]--;
// ran out of money/food? lose 3% of all units
		if (($users[food] < 0) || ($users[cash] < 0))
		{
			$users[peasants] = round($users[peasants] * .97);
			for ($i = 0; $i < 4; $i++)
				$users[$trplst[$i]] = round($users[$trplst[$i]] * .97);
			$users[wizards] = round($users[wizards] * .97);
			if ($users[food] < 0)	$users[food] = 0;
			if ($users[cash] < 0)	$users[cash] = 0;
?>
<span class="cbad">Due to lack of <?
			if ($users[food] == 0)
			{
				print "food";
				if ($users[cash] == 0)
					print " and cash";
			}
			else	print "cash";
?>, 3% of your population and military have left!<?
			if ($nonstop)
			{
?></span><br>
<?
			}
			else
			{
?> Turns were stopped!</span><br>
<?
				break;
			}
		}
	}
	$users[idle] = $time;
	saveUserDataNet($users,"networth land freeland savings loan cash armtrp lndtrp flytrp seatrp food health peasants runes wizards turnsused turns idle");
	return $taken;
}

function TheEnd ($reason)	// End current action
{
	global $users, $config, $version;
?>
<?=$reason?><br>
<?
	if ($users[num])
		printStatsBar();
?>
<a href="http://www.promisance.com" target="_blank">Promisance</a>&trade; v<?=$version?> - Copyright &copy; 1999-2002 PC Purgett<br>
<a href="http://qm.ath.cx/" target="_blank">QM Promisance</a>&trade; v3.1 - Copyright &copy; 2001 by <a href="mailto:quietust@ircN.org">The Quietust</a> &amp; <a href="mailto:lord_of_fire1@yahoo.com">Morvandium</a><br>
<a href="<?=$config[main]?>?action=credits">-- Full Credits --</a><br>
<?
	HTMLendfull();
	exit;
}

function printScoreHeader ($color)
{
?>
<tr class="era<?=$color?>">
    <th style="width:5%" class="aright">Rank</th>
    <th style="width:40%">Empire</th>
    <th style="width:10%" class="aright">Land</th>
    <th style="width:15%" class="aright">Networth</th>
    <th style="width:10%">Clan</th>
    <th style="width:10%">Race</th>
    <th style="width:10%">Era</th></tr>
<?
}

function printScoreLine ()
{
	global $users, $enemy, $ctags, $rtags, $etags, $racedb, $eradb, $config;
	$color = "normal";
	if ($enemy[num] == $users[num])
		$color = "self";
	elseif ($enemy[land] == 0)
		$color = "dead";
	elseif ($enemy[disabled] == 2)
		$color = "admin";
	elseif ($enemy[disabled] == 3)
		$color = "disabled";
	elseif (($enemy[turnsused] <= $config[protection]) || ($enemy[vacation] > $config[vacationdelay]))
		$color = "protected";
	elseif (($users[clan]) && ($enemy[clan] == $users[clan]))
		$color = "ally";
?>
<tr class="m<?=$color?>">
    <td class="aright"><?if ($enemy[online]) echo "*";?><?=$enemy[rank]?></td>
    <td class="acenter"><?=$enemy[empire]?> (#<?=$enemy[num]?>)</td>
    <td class="aright"><?=commas($enemy[land])?></td>
    <td class="aright">$<?=commas($enemy[networth])?></td>
    <td class="acenter"><?=$ctags["$enemy[clan]"]?></td>
    <td class="acenter"><?=$rtags["$enemy[race]"]?></td>
    <td class="acenter"><?=$etags["$enemy[era]"]?></td></tr>
<?
}

function printSearchHeader ($color)
{
?>
<tr class="era<?=$color?>">
    <th style="width:5%" class="aright">Rank</th>
    <th style="width:25%">Empire</th>
    <th style="width:10%" class="aright">Land</th>
    <th style="width:15%" class="aright">Networth</th>
    <th style="width:10%">Clan</th>
    <th style="width:10%">Race</th>
    <th style="width:5%">Era</th>
    <th style="width:8%">O</th>
    <th style="width:8%">D</th>
    <th style="width:4%">K</th></tr>
<?
}

function printSearchLine ()
{
	global $users, $enemy, $ctags, $rtags, $etags, $racedb, $eradb, $config;
	$color = "normal";
	if ($enemy[num] == $users[num])
		$color = "self";
	elseif ($enemy[land] == 0)
		$color = "dead";
	elseif ($enemy[disabled] == 2)
		$color = "admin";
	elseif ($enemy[disabled] == 3)
		$color = "disabled";
	elseif (($enemy[turnsused] <= $config[protection]) || ($enemy[vacation] > $config[vacationdelay]))
		$color = "protected";
	elseif (($users[clan]) && ($enemy[clan] == $users[clan]))
		$color = "ally";
?>
<tr class="m<?=$color?>">
    <td class="aright"><?if ($enemy[online]) echo "*";?><?=$enemy[rank]?></td>
    <td class="acenter"><?=$enemy[empire]?> (#<?=$enemy[num]?>)</td>
    <td class="aright"><?=commas($enemy[land])?></td>
    <td class="aright">$<?=commas($enemy[networth])?></td>
    <td class="acenter"><?=$ctags["$enemy[clan]"]?></td>
    <td class="acenter"><?=$rtags["$enemy[race]"]?></td>
    <td class="acenter"><?=$etags["$enemy[era]"]?></td>
    <td class="acenter"><?=$enemy[offtotal]?> (<?if ($enemy[offtotal]) echo round($enemy[offsucc]/$enemy[offtotal]*100); else echo 0;?>%)</td>
    <td class="acenter"><?=$enemy[deftotal]?> (<?if ($enemy[deftotal]) echo round($enemy[defsucc]/$enemy[deftotal]*100); else echo 0;?>%)</td>
    <td class="acenter"><?=$enemy[kills]?></td></tr>
<?
}

function printMainStats ($user, $race, $era)
{
	global $config;
?>
<table style="width:75%">
<tr class="era<?=$user[era]?>"><th colspan="3"><?=$user[empire]?> (#<?=$user[num]?>)</th></tr>
    <tr><td style="width:40%">
    <table class="empstatus" style="width:100%">
        <tr><th>Turns</th><?              ?><td><?=$user[turns]?> (max <?=$config[maxturns]?>)</td></tr>
        <tr><th>Turns Stored</th><?       ?><td><?=$user[turnsstored]?> (max <?=$config[maxstoredturns]?>)</td></tr>
        <tr><th>Rank</th><?               ?><td>#<?=$user[rank]?></td></tr>
        <tr><th><?=$era[peasants]?></th><??><td><?=commas($user[peasants])?></td></tr>
        <tr><th>Land Acres</th><?         ?><td><?=commas($user[land])?></td></tr>
        <tr><th>Money</th><?              ?><td>$<?=commas($user[cash])?></td></tr>
        <tr><th><?=$era[food]?></th><?    ?><td><?=commas($user[food])?></td></tr>
        <tr><th><?=$era[runes]?></th><?   ?><td><?=commas($user[runes])?></td></tr>
        <tr><th>Networth</th><?           ?><td>$<?=commas($user[networth])?></td></tr>
    </table></td>
    <td style="width:20%"></td>
    <td style="width:40%">
    <table class="empstatus" style="width:100%">
        <tr><th>Era</th><?               ?><td><?=$era[name]?></td></tr>
        <tr><th>Race</th><?              ?><td><?=$race[name]?></td></tr>
        <tr><th>Health</th><?            ?><td><?=$user[health]?>%</td></tr>
        <tr><th>Tax Rate</th><?          ?><td><?=$user[tax]?>%</td></tr>
        <tr><th><?=$era[armtrp]?></th><? ?><td><?=commas($user[armtrp])?></td></tr>
        <tr><th><?=$era[lndtrp]?></th><? ?><td><?=commas($user[lndtrp])?></td></tr>
        <tr><th><?=$era[flytrp]?></th><? ?><td><?=commas($user[flytrp])?></td></tr>
        <tr><th><?=$era[seatrp]?></th><? ?><td><?=commas($user[seatrp])?></td></tr>
        <tr><th><?=$era[wizards]?></th><??><td><?=commas($user[wizards])?></td></tr>
    </table></td></tr>
</table>
<?
}

function numNewMessages ()
{
	global $messagedb, $users;
	return sqleval("SELECT COUNT(*) FROM $messagedb WHERE dest=$users[num] AND time>$users[msgtime] AND deleted=0;");
}
function numTotalMessages ()
{
	global $messagedb, $users;
	return sqleval("SELECT COUNT(*) FROM $messagedb WHERE dest=$users[num] AND deleted=0;");
}

// The final 8 arguments are optional
function addNews ($event, $src, $dest, $data0 /*, $data1, $data2, $data3, $data4, $data5, $data6, $data7, $data8*/)
{
	global $newsdb, $time, $lockdb;
	if ($lockdb)
		return;
	$data_l = "data0";
	$data_n = "$data0";

	$numargs = func_num_args();

	for ($i = 4; $i < $numargs; $i++)
	{
		$n = func_get_arg($i);
		$data_l .= ",data".($i-3);
		$data_n .= ",".$n;
	}
	mysql_query("INSERT INTO $newsdb (time,num_s,clan_s,num_d,clan_d,event,$data_l) VALUES ($time,$src[num],$src[clan],$dest[num],$dest[clan],$event,$data_n);");
}

function printNews (&$user)
{
	global $newsdb, $playerdb, $clandb, $uera, $time;

	$news = mysql_query("SELECT time,num_s,p1.empire AS name_s,c1.name AS clan_s,p1.era AS era_s,num_d,p2.empire AS name_d,c2.name AS clan_d,p2.era AS era_d,event,data0,data1,data2,data3,data4,data5,data6,data7,data8 FROM $newsdb LEFT JOIN $playerdb AS p1 ON (num_s=p1.num) LEFT JOIN $playerdb AS p2 ON (num_d=p2.num) LEFT JOIN $clandb AS c1 ON (clan_s=c1.num) LEFT JOIN $clandb AS c2 ON (clan_d=c2.num) WHERE num_d=$user[num] AND time>$user[newstime] ORDER BY time ASC;");
	if (!mysql_num_rows($news))
		return 0;
?>
<table class="inputtable" border>
<tr><th>Time</th>
    <th colspan="2">Event</th></tr>
<?
	while ($new = mysql_fetch_array($news))
	{
?>
<tr style="vertical-align:top"><th><?
		$hours = ($time-$new[time])/3600;
		if ($hours > 24)
		{
			$days = floor($hours/24);
			print $days." days, ";
			$hours -= $days*24;
		}
		print round($hours,1)." hours ago";
?></th>
<?
		$eera = loadEra($new[era_s]);
		switch ($new[event])
		{
		case 100:
			switch ($new[data0])
			{
			case 1:	$type = 'food';		break;
			case 2:	$type = 'armtrp';	break;
			case 3:	$type = 'lndtrp';	break;
			case 4:	$type = 'flytrp';	break;
			case 5:	$type = 'seatrp';	break;
			}
?>    <td colspan="2"><span class="cgood">You sold <?=commas($new[data1])?> <?=$uera[$type]?> on the market for $<?=commas($new[data3])?></span></td></tr>
<?			break;
		case 101:
?>    <td colspan="2"><span class="cgood">The winning lottery ticket number is announced. You look at your lottery ticket and it matches! You have won $<?=commas($new[data0])?>!</span></td></tr>
<?			break;
		case 102:
?>    <td><span class="cgood"><?=$new[name_s]?> (#<?=$new[num_s]?>) has sent you <?=commas($new[data0])?> <?=$uera[seatrp]?> carrying...</span></td>
    <td><?
			if ($new[data1])	print commas($new[data1])." $uera[armtrp]<br>\n";
			if ($new[data2])	print commas($new[data2])." $uera[lndtrp]<br>\n";
			if ($new[data3])	print commas($new[data3])." $uera[flytrp]<br>\n";
			if ($new[data4])	print "$".commas($new[data4])."<br>\n";
			if ($new[data5])	print commas($new[data5])." $uera[runes]<br>\n";
			if ($new[data6])	print commas($new[data6])." $uera[food]<br>\n";
?>    </td></tr>
<?			break;
		case 110:
?>    <td colspan="2"><span class="cgood">You founded <?=$new[clan_d]?>.</span></td></tr>
<?			break;
		case 111:
?>    <td colspan="2"><span class="cwarn">You disbanded <?=$new[clan_d]?>.</span></td></tr>
<?			break;
		case 112:
?>    <td colspan="2"><span class="cgood"><?
			if ($new[num_s] == $new[num_d])
				print "You";
			else	print "$new[name_s] (#$new[num_s])";
?> joined <?=$new[clan_d]?>.</span></td></tr>
<?			break;
		case 113:
?>    <td colspan="2"><span class="cwarn"><?
			if ($new[num_s] == $new[num_d])
				print "You";
			else	print "$new[name_s] (#$new[num_s])";
?> left <?=$new[clan_d]?>.</span></td></tr>
<?			break;
		case 114:
?>    <td colspan="2"><span class="cwarn"><?=$new[name_s]?> (#<?=$new[num_s]?> removed you from <?=$new[clan_d]?>.</span></td></tr>
<?			break;
		case 115:
?>    <td colspan="2"><span class="cgood"><?=$new[name_s]?> (#<?=$new[num_s]?> made you leader of <?=$new[clan_d]?>.</span></td></tr>
<?			break;
		case 116:
?>    <td colspan="2"><span class="cgood">You inherited leadership of <?=$new[clan_d]?>.</span></td></tr>
<?			break;
		case 117:
?>    <td colspan="2"><span class="cwarn"><?=$new[name_s]?> (#<?=$new[num_s]?> dropped you from the disbanded <?=$new[clan_d]?>.</span></td></tr>
<?			break;
		case 118:
?>    <td colspan="2"><span class="cgood"><?=$new[name_s]?> (#<?=$new[num_s]?> made you a Minister of Foreign Affairs of <?=$new[clan_d]?>.</span></td></tr>
<?			break;
		case 119:
?>    <td colspan="2"><span class="cwarn"><?=$new[name_s]?> (#<?=$new[num_s]?> has removed you from your position as Minister of Foreign Affairs of <?=$new[clan_d]?>.</span></td></tr>
<?			break;
		case 201:
?>    <td colspan="2"><span class=<?
			if ($new[data0] < 0)
				print '"cwarn">'."You find $new[name_s] (#$new[num_s]) attempting to view your empire!";
			else	print '"cbad">'."You find another empire viewing your stats!";
?></span></td></tr>
<?			break;
		case 202:
?>    <td colspan="2"><span class=<?
			if ($new[data0] < 0)
				print '"cwarn">'."$new[name_s] (#$new[num_s]) tried to eliminate your forces!";
			else
			{
				print '"cbad">';
				if ($new[data0] == 0)
					print "1";
				else	print "3";
				print "% of your forces were eliminated by $new[name_s] (#$new[num_s])!";
			}
?></span></td></tr>
<?			break;
		case 204:
?>    <td colspan="2"><span class=<?
			if ($new[data0] < 0)
				print '"cwarn">'."You notice $new[name_s] (#$new[num_s]) trying to cause storms on your land!";
			else
			{
				print '"cbad">'."Storms have blown away ".commas($new[data1])." $uera[food] and $".commas($new[data2]);
				if ($new[data0] == 0)
					print ", though your shield protected most of your goods.";
				else	print "!";
			}
?></span></td></tr>
<?			break;
		case 205:
?>    <td colspan="2"><span class=<?
			if ($new[data0] < 0)
				print '"cwarn">'."You find $new[name_s] (#$new[num_s]) attempting to disturb your $uera[runes]!";
			else
			{
				print '"cbad">'."Lightning destroyed ".commas($new[data1])." of your $uera[runes]";
				if ($new[data0] == 0)
					print ", though your shield absorbed most of the damage.";
				else	print "!";
			}
?></span></td></tr>
<?			break;
		case 206:
?>    <td colspan="2"><span class=<?
			if ($new[data0] < 0)
				print '"cwarn">'."You find $new[name_s] (#$new[num_s]) attempting to send monsters into your empire!";
			else
			{
				print '"cbad">'."Monsters have destroyed part of your empire";
				if ($new[data0] == 0)
					print ", though your shield kept most of them out.";
				else	print "!";
			}
?></span></td></tr>
<?			break;
		case 211:
			if ($new[data0] < 0)
			{
?>    <td colspan="2"><span class="cwarn">You find <?=$new[name_s]?> (#<?=$new[num_s]?>) attempting to fight with your <?=$uera[wizards]?>!</span></td></tr>
<?			}
			elseif ($new[data0] == 0)
			{
?>    <td><span class="cwarn">You find <?=$new[name_s]?> (#<?=$new[num_s]?>) losing a fight with your wizards!</span></td>
    <td>You killed <?=$new[data2]?> <?=$eera[wizards]?>, losing <?=$new[data1]?> <?=$uera[wizards]?> in the process.</td></tr>
<?			}
			else
			{
?>    <td><span class="cbad">Your <?=$uera[wizards]?> were defeated by <?=$new[name_s]?> (#<?=$new[num_s]?>) and you lost <?=$new[data0]?> acres of land!</span></td>
    <td>You lost <?=$new[data1]?> <?=$uera[wizards]?>, but you managed to kill <?=$new[data2]?> of your attacker's <?=$eera[wizards]?>.</td></tr>
<?			}
			break;
		case 212:
?>    <td colspan="2"><span class=<?
			if ($new[data0] < 0)
				print '"cwarn">'."You find $new[name_s] (#$new[num_s]) trying to embezzle your money!";
			else
			{
				print '"cbad">'."Someone stole $".commas($new[data1])." from your treasury";
				if ($new[data0] == 0)
					print ", though your shield prevented them from stealing more.";
				else	print "!";
			}
?></span></td></tr>
<?			break;
		case 300:
?>    <td colspan="2"><span class="cwarn">Your forces came to the aid of <?=sqleval("SELECT empire FROM $playerdb WHERE num=$new[data0];")?> (#<?=$new[data0]?>) in defense from <?=$new[name_s]?> (#<?=$new[num_s]?>)!</span></td></tr>
<?			break;
		case 301:
?>    <td colspan="2"><span class="cbad">As <?=$new[name_s]?> (#<?=$new[num_s]?>) delivers their final blow, your empire collapses...</span></td></tr>
<?			break;
		case 302:
		case 303:
?>    <td><span class="cbad"><?=$new[name_s]?> (#<?=$new[num_s]?>) attacked you!</span></td>
    <td><?
			if ($new[data0])
				print "Your enemy captured $new[data0] acres of land and destroyed:<br>";
			else	print "You held your defense and your enemy was repelled, but you lost:<br>";

			if ($new[data1]) print commas($new[data1])." $uera[armtrp]<br>";
			if ($new[data2]) print commas($new[data2])." $uera[lndtrp]<br>";
			if ($new[data3]) print commas($new[data3])." $uera[flytrp]<br>";
			if ($new[data4]) print commas($new[data4])." $uera[seatrp]<br>";
			print "You managed to destroy:<br>";
			if ($new[data5]) print commas($new[data5])." $eera[armtrp]<br>";
			if ($new[data6]) print commas($new[data6])." $eera[lndtrp]<br>";
			if ($new[data7]) print commas($new[data7])." $eera[flytrp]<br>";
			if ($new[data8]) print commas($new[data8])." $eera[seatrp]<br>";
?></td></tr>
<?			break;
		case 304:
		case 305:
		case 306:
		case 307:
			switch ($new[event])
			{
			case 304:	$unit = armtrp;	break;
			case 305:	$unit = lndtrp;	break;
			case 306:	$unit = flytrp;	break;
			case 307:	$unit = seatrp;	break;
			}

?>    <td><span class="cbad"><?=$new[name_s]?> (#<?=$new[num_s]?>) attacked you!</span></td>
    <td><?
			if ($new[data0])
				print "Your enemy captured $new[data0] acres of land and destroyed:<br>";
			else	print "You held your defense and your enemy was repelled, but you lost:<br>";

			if ($new[data1]) print commas($new[data1])." $uera[$unit]<br>";
			print "You managed to destroy:<br>";
			if ($new[data2]) print commas($new[data2])." $eera[$unit]<br>";
?></td></tr>
<?			break;
		}
	}
?>
</table>
<?
	return 1;
}
?>
