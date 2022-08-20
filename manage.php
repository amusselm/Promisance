<?
include("header.php");
// killUnits(unit,0.10,0.30,3) will destroy 30%-90% of unit
// use multiple rolls to get numbers closer to the middle
function killUnits ($type, $minpc, $maxpc, $rolls)
{
	global $users;
	$losspct = 0;
	$min = round(1000000 * $minpc);
	$max = round(1000000 * $maxpc);
	for ($i = 0; $i < $rolls; $i++)
		$losspct += mt_rand($min,$max);
	$losspct /= 1000000;
	$loss = round($users[$type] * $losspct);
	$users[$type] -= $loss;
	return $loss;
}

if (($do_polymorph) && ($yes_polymorph))
{
	if ($users[turns] < $config[initturns])
		TheEnd("You don't have enough turns!");
	if ($users[health] < 75)
		TheEnd("You don't have enough health!");
	if ($users[wizards] < $users[land]*3)
		TheEnd("You don't have enough $uera[wizards]!");
	if ($new_race != $users[race])
	{
		$users[health] -= 50;
		$users[turns] -= $config[initturns];
		print "Your $uera[wizards] concentrate, and your people begin to change form...<br>";
		print commas(killUnits(armtrp,0.10,0.15,1))." $uera[armtrp], ".
			commas(killUnits(lndtrp,0.10,0.15,1))." $uera[lndtrp], ".
			commas(killUnits(flytrp,0.10,0.15,1))." $uera[flytrp], ".
			commas(killUnits(seatrp,0.10,0.15,1))." $uera[seatrp], ".
			commas(killUnits(peasants,0.15,0.20,1))." $uera[peasants], and ".
			commas(killUnits(wizards,0.10,0.15,1))." $uera[wizards] died from system shock.<br>\n";
		$buildloss = 0;
		$buildloss += killUnits(homes,0.08,0.27,2);
		$buildloss += killUnits(shops,0.08,0.27,2);
		$buildloss += killUnits(industry,0.08,0.27,2);
		$buildloss += killUnits(barracks,0.08,0.27,2);
		$buildloss += killUnits(labs,0.08,0.27,2);
		$buildloss += killUnits(farms,0.08,0.27,2);
		$buildloss += killUnits(towers,0.08,0.27,2);
		$users[freeland] += $buildloss;
		$size = calcSizeBonus($users[networth]);
		print commas($buildloss)." structures, ".
			commas(killUnits(food,0.05*$size,0.15*$size,3))." $uera[food], ".
			commas(killUnits(runes,0.05*$size,0.15*$size,3))." $uera[runes], and $".
			commas(killUnits(cash,0.05*$size,0.15*$size,3))." were lost during the chaos.<br>\n";
		$users[race] = $new_race;
		$urace = loadRace($users[race]);
		$users[networth] = getNetworth($users);
		saveUserDataNet($users,"networth armtrp lndtrp flytrp seatrp wizards homes shops industry barracks labs farms towers freeland food runes cash turns health race");
	}
}
if ($do_changetax)
{
	fixInputNum($new_tax);
	if ($new_tax < 5)
		TheEnd("Cannot set your tax that low!");
	if ($new_tax > 70)
		TheEnd("Cannot set your tax that high!");
	$users[tax] = $new_tax;
	saveUserData($users,"tax");
	print "Tax rate updated!<br>\n";
}
if ($do_changestyle)
{
	$users[style] = $color_setting;
	saveUserData($users,"style");
	print "Color settings updated.<br>Changes will take effect as soon as you load a new page.<br>\n";
}
if ($do_changeindustry)
{
	fixInputNum($ind_armtrp);
	fixInputNum($ind_lndtrp);
	fixInputNum($ind_flytrp);
	fixInputNum($ind_seatrp);

	$total = $ind_armtrp + $ind_lndtrp + $ind_flytrp + $ind_seatrp;
	if (($ind_armtrp > 100) || ($ind_armtrp < 0))		// infantry
		TheEnd("Cannot set industry at that level!");
	elseif (($ind_lndtrp > 100) || ($ind_lndtrp < 0))	// tanks
		TheEnd("Cannot set industry at that level!");
	elseif (($ind_flytrp > 100) || ($ind_flytrp < 0))	// helis
		TheEnd("Cannot set industry at that level!");
	elseif (($ind_seatrp > 100) || ($ind_seatrp < 0))	// ships
		TheEnd("Cannot set industry at that level!");
	elseif ($total > 100)					// total
		TheEnd("Cannot set industry at that level!");
	$users[ind_armtrp] = $ind_armtrp;
	$users[ind_lndtrp] = $ind_lndtrp;
	$users[ind_flytrp] = $ind_flytrp;
	$users[ind_seatrp] = $ind_seatrp;
	saveUserData($users,"ind_armtrp ind_lndtrp ind_flytrp ind_seatrp");
	print "Industry settings updated!<br>\n";
}
if ($do_changepass)
{
	if (($new_password) && ($new_password == $new_password_verify))
	{
		mysql_query("update $playerdb set password=MD5('$new_password') where num=$users[num];");
		TheEnd("Password changed. Please logout and login again.");
	}
	else	print "Error! Passwords do not match!<br>\n";
}
if (($do_setvacation) && ($yes_vacation))
{
	if ($lastweek)
		TheEnd("Vacation disabled during last week of game!");
	$users[vacation] = 1;
	SaveUserData($users,"vacation");
	TheEnd("Vacation setting saved; your account is now locked. Your empire will be frozen in $config[vacationdelay] hours.");
}
?>
<a href="<?=$config[main]?>?action=guide&amp;section=manage&amp;era=<?=$users[era]?>">Promisance Guide: Empire Management</a><br>
<table style="width:100%">
<tr><td class="acenter" style="width:30%">
        <form method="post" action="<?=$config[main]?>?action=manage">
        <table class="inputtable">
        <tr><th>Polymorph</th></tr>
        <tr><td class="acenter">Requires <?=$config[initturns]?> turns,<br><?=$users[land]*3?> <?=$uera[wizards]?>, and<br>at least 75% health</td></tr>
<?
	$races = mysql_query("select id,name from $racedb;");
	while ($race = mysql_fetch_array($races))
	{
?>
        <tr><td><label><input type="radio" name="new_race" value="<?=$race[id]?>"<?if ($race[id] == $users[race]) print " checked";?>> <?=$race[name]?></label></td></tr>
<?
	}
?>
        <tr><td class="acenter"><label><input type="checkbox" name="yes_polymorph" value="1">Yes, I really<br>want to polymorph!</td></tr>
        <tr><th><input type="submit" name="do_polymorph" value="Begin Polymorph"></th></tr>
        </table>
        </form>
    </td>
    <td class="acenter" style="width:30%">
        <form method="post" action="<?=$config[main]?>?action=manage">
        <table class="inputtable">
        <tr><td>Tax Rate:</td>
            <td class="aright"><input type="text" name="new_tax" size="3" value="<?=$users[tax]?>">%</td></tr>
        <tr><th colspan="2"><input type="submit" name="do_changetax" value="Change Tax Rate"></th></tr>
        </table>
        </form>
        <form method="post" action="<?=$config[main]?>?action=manage">
        <table class="inputtable">
        <tr><th colspan="2">Industry Settings</th></tr>
        <tr><td><?=$uera[armtrp]?></td>
            <td class="aright"><input type="text" name="ind_armtrp" size="3" value="<?=$users[ind_armtrp]?>">%</td></tr>
        <tr><td><?=$uera[lndtrp]?></td>
            <td class="aright"><input type="text" name="ind_lndtrp" size="3" value="<?=$users[ind_lndtrp]?>">%</td></tr>
        <tr><td><?=$uera[flytrp]?></td>
            <td class="aright"><input type="text" name="ind_flytrp" size="3" value="<?=$users[ind_flytrp]?>">%</td></tr>
        <tr><td><?=$uera[seatrp]?></td>
            <td class="aright"><input type="text" name="ind_seatrp" size="3" value="<?=$users[ind_seatrp]?>">%</td></tr>
        <tr><th colspan="2"><input type="submit" name="do_changeindustry" value="Update Industry"></th></tr>
        </table>
        </form>
        <form method="post" action="<?=$config[main]?>?action=manage">
        <table class="inputtable">
        <tr><th>Color Scheme</th></tr>
<?
$i = 0;
while ($stylenames[++$i])
{
?>
        <tr><td><label><input type="radio" name="color_setting" value="<?=$i?>"<?if ($users[style] == $i) print " checked";?>><?=$stylenames[$i]?></label></td></tr>
<?
}
?>
        <tr><th><input type="submit" name="do_changestyle" value="Change Style"></th></tr>
        </table>
        </form>
    </td>
    <td class="acenter" style="width:40%">
        <form method="post" action="<?=$config[main]?>?action=manage">
        <table class="inputtable">
        <tr><th colspan="2">Change Password</th></tr>
        <tr><td>New:</td>
            <td><input type="password" name="new_password" size="8"></td></tr>
        <tr><td>Verify:</td>
            <td><input type="password" name="new_password_verify" size="8"></td></tr>
        <tr><th colspan="2"><input type="submit" name="do_changepass" value="Change Password"></th></tr>
        </table>
        </form>
<?
if (!$lastweek)
{
?>
        <form method="post" action="<?=$config[main]?>?action=manage">
        <table class="inputtable">
        <tr><th>Vacation</th></tr>
        <tr><td class="acenter">
                Vacation mode will be set for a minimum of <?=$config[minvacation]?> hours.<br>
                When set, you will be immediately locked out of your account,<br>
                and your empire will be frozen after <?=$config[vacationdelay]?> hours.</td></tr>
        <tr><td class="acenter"><label><input type="checkbox" name="yes_vacation" value="1">Yes, I really want to go on vacation!</label></td></tr>
        <tr><th><input type="submit" name="do_setvacation" value="Go on Vacation"></th></tr>
        </table>
        </form>
<?
}
else	print "Vacation is disabled during last week of game.<br>\n";
?>
    </td></tr>
</table>
<?
TheEnd("");
?>
