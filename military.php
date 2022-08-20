<?
include("header.php");
include("magicfun.php");
$atknames[Standard] = "Standard Attack";
$atknames[Surprise] = "Surprise Attack";
$atknames[armtrp] = "Guerilla Strike";
$atknames[lndtrp] = "Stone Bombardment";
$atknames[flytrp] = "Aerial Assault";
$atknames[seatrp] = "Hydro Assault";

// era of troops, quantity of troops, type of troops, offense or defense
function CalcPoints($era, $quantity, $ttype, $atype)
{
	$type = $atype."_".$ttype;
	return $quantity * $era[$type];
}

function ClanCheck()  //  Need to set warflag and netmult
{
	global $warflag, $netmult, $users, $uclan, $enemy;
	if ($users[clan] == $enemy[clan])
		TheEnd("Cannot attack empires in your clan!");
	if (($uclan[ally1] == $enemy[clan]) || ($uclan[ally2] == $enemy[clan]) || ($uclan[ally3] == $enemy[clan]))
		TheEnd("Your Generals quietly ignore your orders to attack an Ally.");
	if (($uclan[war1] == $enemy[clan]) || ($uclan[war2] == $enemy[clan]) || ($uclan[war3] == $enemy[clan]))
	{
		$warflag = 1.2;
		$netmult = 50;
	}
}

function ReadInput ($type)
{
	global $users, $usent, $enemy, $esent, $sendall, $trplst;

	for ($i = 0; $i < 4; $i++)
	{
		$trp = $trplst[$i];
		$esent[$trp] = $enemy[$trp];
		if ($enemy[forces] > 0)			// if enemy shares forces, he can't use them for defense
			$esent[$trp] *= 0.9;
		if ($sendall)				// send everything?
			$usent[$trp] = $users[$trp];
	}

	if (($type != 'Standard') && ($type != 'Surprise'))
	{
		for ($i = 0; $i < 4; $i++)
		{
			$trp = $trplst[$i];
			if ($type != $trp)
			{
				$usent[$trp] = 0;
				$esent[$trp] = 0;
			}
		}
	}
	for ($i = 0; $i < 4; $i++)
		CheckQuantity($trplst[$i]);
}

function CheckQuantity($type)
{
	global $users, $uera, $usent;
	fixInputNum($usent[$type]);
	$esent[$type] = round($esent[$type]);
	if ($usent[$type] < 0)
		TheEnd("Cannot attack with a negative number of units!");
	if ($usent[$type] > $users[$type])
		TheEnd("You do not have that many $uera[$type]!");
}

function Attack($type)
{
	global $users, $uera, $usent, $enemy, $eera, $esent, $playerdb, $datetime, $time, $trplst, $warflag;
	$uoffense = 0;
	$edefense = 0;
	for ($i = 0; $i < 4; $i++)
	{
		$uoffense += CalcPoints($uera,$usent[$trplst[$i]],$trplst[$i],o);
		$edefense += CalcPoints($eera,$esent[$trplst[$i]],$trplst[$i],d);
	}
	if ($uoffense == 0)
		TheEnd("Must attack with something!");

	$uoffense *= $users[health] / 100;
	$edefense *= $enemy[health] / 100;

	if ($warflag)
		$uoffense *= 1.2;

	if ($users[era] != $enemy[era])			// only step through time gate if necessary
	{
		if ($users[gate] > $time)		// your time gate?
			print "Stepping through your open time gate,<br>\n";
		elseif ($enemy[gate] > $time)		// or enemy's time gate?
			print "Stepping through your enemy's open time gate,<br>\n";
	}

	if ($type == "Surprise")			// surprise attack?
	{
		$offpts *= 1.25;
		$helping = 0;
		$users[health] -= 5;
	}
	elseif (($enemy[clan]) && ($enemy[forces] == 1))	// enemy has allies and sharing forces?
	{
		$dbally = mysql_query("SELECT armtrp,lndtrp,flytrp,seatrp,num,era,race,clan,gate FROM $playerdb WHERE clan=$enemy[clan] AND forces>0 AND num!=$enemy[num] AND land>0;");
		$helping = mysql_num_rows($dbally);
	}
	if ($helping)					// add up allies
	{
		print "$helping empires rushing to defend your target,<br>\n";
		$emaxdefense = $edefense * 2;
		while ($ally = mysql_fetch_array($dbally))
		{
			$ad = 0;
			if (($enemy[gate] > $time) || ($ally[gate] > $time) || ($enemy[era] == $ally[era]))
			{						// defense is limited to eras as well
				addNews(300,$users,$ally,$enemy[num]);
				$arace = loadRace($ally[race]);		// adjust according to ally race
				$aera = loadEra($ally[era]);		// and era

				for($i = 0; $i < 4; $i++)
					$ad += allyHelp($trplst[$i],$helping) * ($ally[health] / 100);

				$ad = round($ad * $arace[defense]);
				$edefense += $ad;
			}
		}
		if ($edefense > $emaxdefense)				// limit ally defense
			$edefense = $emaxdefense;
	}
	$tdefense = $enemy[towers] * 500 * min(1,$enemy[armtrp] / (100*$users[towers]+1));   // and add in towers
	$edefense += $tdefense;
	if ($warflag == 0)						// war == infinite attacks
		$enemy[attacks]++;
	dobattle($uoffense,$edefense,$type,$tdefense);
}

function AllyHelp ($type, $numallies)
{
	global $enemy, $esent, $ally, $aera;
	$amt = round($ally[$type] * .1);
	if ($amt > $esent[$type] / $numallies)
		$amt = $esent[$type] / $numallies;
	return CalcPoints($aera,$amt,$type,d);
}

/*
dobattle(Offense_Points, Defense_Points, Attack_Type)
This function:
determines who won
calls detloss() to determine troop losses
calls dealland() if attack was successful
*/
function dobattle ($op, $dp, $type, $towp)
{
	$emod = sqrt($op/($dp+1));		// modification to enemy losses
	$umod = sqrt(($dp-$towp)/($op+1));		// modification to attacker losses (towers not included)
	switch ($type)
	{
	case armtrp:
		detloss(.1155, .0705, $umod, $emod, armtrp);
		break;

	case lndtrp:
		detloss(.0985, .0530, $umod, $emod, lndtrp);
		break;

	case flytrp:
		detloss(.0688, .0445, $umod, $emod, flytrp);
		break;

	case seatrp:
		detloss(.0450, .0355, $umod, $emod, seatrp);
		break;

	case Surprise:
		$umod *= 1.2;			// fall through

	case Standard:
		detloss(.1455, .0805, $umod, $emod, armtrp);
		detloss(.1285, .0730, $umod, $emod, lndtrp);
		detloss(.0788, .0675, $umod, $emod, flytrp);
		detloss(.0650, .0555, $umod, $emod, seatrp);
		break;
	}
	if($op > $dp * 1.05)
	{
		dealland($type);
	}
	printedreport();
}

/*
This function determines the loss of specific types of troops
It handles the attacker and defender in one run through
*/
function detloss($uper, $eper, $umod, $emod, $type)
{
	global $uloss, $eloss, $usent, $esent;
	if ($usent[$type] > 0)		// can't lose more than you send... send none, lose none
		$uloss[$type] = min(mt_rand(0,(ceil($usent[$type] * $uper * $umod)+1)), $usent[$type]);
	else	$uloss[$type] = 0;

	$maxkill = round(.9*$usent[$type]) + mt_rand(0, round(.2*$usent[$type] + 1)); // max kills determination (90% - 110%)

	if ($esent[$type] > 0)		// he can't lose more than he defended with, or attacker can kill
		$eloss[$type] = min(mt_rand(0,ceil($esent[$type] * $eper * $emod)), $esent[$type], $maxkill);
	else	$eloss[$type] = 0;	// no troops, no losses
}

function LossCalc(&$player, &$ploss)
{
	global $trplst;
	for ($i = 0; $i < 4; $i++)
		$player[$trplst[$i]] -= $ploss[$trplst[$i]];
}

function DealLand($type)
{
	global $landloss, $buildgain, $enemy, $users;

	// destroy structures
	destroyBuildings('homes',7,70,$type);
	destroyBuildings('shops',7,70,$type);
	destroyBuildings('industry',7,50,$type);
	destroyBuildings('barracks',7,70,$type);
	destroyBuildings('labs',7,60,$type);
	destroyBuildings('farms',7,30,$type);
	destroyBuildings('towers',7,60,$type);
	destroyBuildings('freeland',10,0,$type);	// 3rd argument MUST be 0 - calculate gained freeland below
	$users[freeland] += $landloss - $buildgain;

	// update total land counts
	$users[land] += $landloss;
	$enemy[land] -= $landloss;
}

// To handle destroying buildings during successful attacks
function destroyBuildings ($type, $pcloss, $pcgain, $atktype)
{
	global $landloss, $buildgain, $enemy, $users;
	$pcloss /= 100;
	$pcgain /= 100;

	if (($atktype == 'lndtrp') || ($atktype == 'flytrp') || ($atktype == 'seatrp'))
	{				// these attacks destroy extra buildings, but fewer are gained
		if ($atktype == 'flytrp')
		{
			$pcloss *= 1.25;
			$pcgain *= 0.72;
		}
		elseif (($type == 'towers') || ($type == 'labs'))
		{
			$pcloss *= 1.3;
			$pcgain *= 9/13;
		}
		else	$pcgain *= 0.9;
	}

	if ($enemy[$type] > 0)
		$loss = mt_rand(1,ceil($enemy[$type] * $pcloss + 2));
	if ($loss > $enemy[$type])
		$loss = $enemy[$type];
	$gain = ceil($loss * $pcgain);

	$enemy[$type] -= $loss;
	$landloss += $loss;
	if ($atktype == Standard)	// only gain buildings with standard attack
	{
		$users[$type] += $gain;
		$buildgain += $gain;
	}
}

function printedreport()
{
	global $users, $uloss, $uera, $enemy, $eloss, $eera, $landloss, $buildgain, $trplst;
	if ($landloss)
		print "Your army breaks through $enemy[empire]'s defenses and captures $landloss acres of land!  In the effort, you lost:<br>\n";
	else	print "After a failing struggle, your army is repelled by $enemy[empire]'s defenses.  In the attempt, you lost:<br>\n";
	for ($i = 0; $i < 4; $i++)
	{
		$trp = $trplst[$i];
		if ($uloss[$trp]) print commas($uloss[$trp])." $uera[$trp]<br>\n";
	}
	print "In their defense, $enemy[empire] lost:<br>\n";
	for ($i = 0; $i < 4; $i++)
	{
		$trp = $trplst[$i];
		if ($eloss[$trp]) print commas($eloss[$trp])." $eera[$trp]<br>\n";
	}
	if ($buildgain)
		print "You also captured $buildgain structures!<br>\n";
	if ($enemy[land] == 0)
	{
?><span class="cgood"><b><?=$enemy[empire]?> (#<?=$enemy[num]?>)</b> has been destroyed!</span><br>
<?		$users[kills]++;
	}
}

// To print the attack table
function printRow ($type)
{
	global $users, $uera;
?>
<tr><td><?=$uera[$type]?></td>
    <td class="aright"><?=commas($users[$type])?></td>
    <td class="aright"><input type="text" name="usent[<?=$type?>]" size="8" value="0"></td></tr>
<?
}

// *************
// End Functions
// *************

if ($users[turnsused] <= $config[protection])	// are they under protection?
	TheEnd("Cannot use offensive actions while under protection!");
if ($users[disabled] == 2)			// are they admin?
	TheEnd("Administrative accounts cannot use offensive actions!");

if ($do_attack)
{
	if ($users[turns] < 2)						// enough turns?
		TheEnd("Not enough turns!");
	if (!$target)							// specified target?
		TheEnd("You must specify a target!");
	if ($target == $users[num])					// attacking self?
		TheEnd("Cannot attack yourself!");
	if ($users[health] <= 1)
		TheEnd("You do not have enough health to attack.");
	$enemy = loadUser($target);
	$erace = loadRace($enemy[race]);
	$eera = loadEra($enemy[era]);					// load enemy info

	if ($enemy[land] == 0)
		TheEnd("That empire has already been destroyed!");
	if (($enemy[era] != $users[era]) && ($users[gate] <= $time) && ($enemy[gate] <= $time))
		TheEnd("Need to open a Time Gate first!");
	if ($enemy[disabled] >= 2)
		TheEnd("Cannot attack disabled empires!");
	if ($enemy[turnsused] <= $config[protection])
		TheEnd("Cannot attack empires under new player protection!");
	if ($enemy[vacation] > $config[vacationdelay])
		TheEnd("Cannot attack empires on vacation!");
	$warflag = 0;
	$netmult = 20;

	$uclan = loadClan($users[clan]);

	if ($enemy[clan])
		ClanCheck();

	if ($enemy[networth] > $users[networth] * $netmult)
		TheEnd("Your Generals flatly refuse to attack such a strong opponent!");
	if ($users[networth] > $enemy[networth] * $netmult)
		TheEnd("Your Generals politely refuse your orders to attack a defenseless empire!");

	if ($warflag == 0)
	{
		if ($enemy[attacks] > 20)
			TheEnd("Too many recent attacks on that empire.  Try again in one hour.");
		$revolt = 1;
		if ($users[networth] > $enemy[networth] * 2.5)
		{						// Shame is less powerful than fear
?><span class="cwarn">Your military is shamed by your attack on such a weak opponent. Many desert!</span><br>
<?			$revolt = 1 - $users[networth] / $enemy[networth] / 125;
		}
		elseif ($enemy[networth] > $users[networth] * 2.5)
		{
?><span class="cwarn">Your military trembles at your attack on such a strong opponent. Many desert!</span><br>
<?			$revolt = 1 - $enemy[networth] / $users[networth] / 100;
		}
		if ($revolt < .9)
			$revolt = .9;
		for ($i = 0; $i < 4; $i++)
			$users[$trplst[$i]] = round($users[$trplst[$i]] * $revolt);
	}

	readInput($attacktype);

	Attack($attacktype);

	// record losses

	losscalc($users, $uloss);
	losscalc($enemy, $eloss);

	if (!$landloss)
		$landloss = 0;
	switch ($attacktype)
	{
	case 'Standard':addNews(302,$users,$enemy,$landloss,$eloss[armtrp],$eloss[lndtrp],$eloss[flytrp],$eloss[seatrp],$uloss[armtrp],$uloss[lndtrp],$uloss[flytrp],$uloss[seatrp]);	break;
	case 'Surprise':addNews(303,$users,$enemy,$landloss,$eloss[armtrp],$eloss[lndtrp],$eloss[flytrp],$eloss[seatrp],$uloss[armtrp],$uloss[lndtrp],$uloss[flytrp],$uloss[seatrp]);	break;
	case 'armtrp':	addNews(304,$users,$enemy,$landloss,$eloss[armtrp],$uloss[armtrp]);	break;
	case 'lndtrp':	addNews(305,$users,$enemy,$landloss,$eloss[lndtrp],$uloss[lndtrp]);	break;
	case 'flytrp':	addNews(306,$users,$enemy,$landloss,$eloss[flytrp],$uloss[flytrp]);	break;
	case 'seatrp':	addNews(307,$users,$enemy,$landloss,$eloss[seatrp],$uloss[seatrp]);	break;
	}
	if ($enemy[land] == 0)
		addNews(301,$users,$enemy,0);

	$users[attacks] -= 2;
	if ($users[attacks] < 0)
		$users[attacks] = 0;
	$users[offtotal]++;
	if ($landloss)
		$users[offsucc]++;
	else	$enemy[defsucc]++;
	$enemy[deftotal]++;
	$users[health] -= 8;
	saveUserDataNet($users,"networth armtrp lndtrp flytrp seatrp land homes shops industry barracks labs farms towers freeland offsucc offtotal attacks health kills");
	saveUserDataNet($enemy,"networth armtrp lndtrp flytrp seatrp land homes shops industry barracks labs farms towers freeland defsucc deftotal attacks");

	taketurns(2,attack);
}
?>
<form method="post" action="<?=$config[main]?>?action=military">
<table class="inputtable">
<tr><td colspan="3" class="acenter">Empire number to attack? <input type="text" name="target" size="5"></td></tr>
<tr><td colspan="3" class="acenter">Attack Type: <select name="attacktype" size="1">
        <option value="Standard"><?=$atknames[Standard]?></option>
        <option value="Surprise"><?=$atknames[Surprise]?> (no allies)</option>
        <option value="armtrp"><?=$atknames[armtrp]?></option>
        <option value="lndtrp"><?=$atknames[lndtrp]?></option>
        <option value="flytrp"><?=$atknames[flytrp]?></option>
        <option value="seatrp"><?=$atknames[seatrp]?></option>
        </select></td></tr>
<tr><th class="aleft">Unit</th>
    <th class="aright">Owned</th>
    <th class="aright">Send</th></tr>
<?
for($i = 0; $i < 4; $i++)
	printRow($trplst[$i]);
?>
<tr><td colspan="3" class="acenter"><input type="checkbox" name="sendall" value="1">Send Everything</td></tr>
<tr><td colspan="3" class="acenter"><input type="submit" name="do_attack" value="Send Attack"></td></tr>
</table>
</form>
<hr>
<form method="post" action="<?=$config[main]?>?action=military">
<table class="inputtable">
<tr><td class="acenter">Empire to cast spell on? <input type="text" name="target" size="5"></td></tr>
<tr><td><select name="spell_num" size="1">
        <option value="0">Select a Spell</option>
<?
for ($i = 1; $i <= 12; $i++)
	if ($sptype[$i] == 'o')
		printMRow($i);
?>
        </select></td></tr>
<tr><td class="acenter"><input type="submit" name="do_spell" value="Cast Spell"></td></tr>
</table>
</form>
<?
if ($users[shield] > $time)
	print "<i>We currently have a shield against magic which will last for ".round(($users[shield]-$time)/3600,1)." more hours.</i><br>\n";
print "<i>The health of our forces and people is at $users[health]%!</i><br>\n";
if ($users[gate] > $time)
	print "<i>We currently have an open time portal which will last for ".round(($users[gate]-$time)/3600,1)." more hours.</i><br>\n";
TheEnd("");
?>
