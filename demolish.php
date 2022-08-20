<?
include("header.php");

$destroyrate = round(($users[land] * 0.02) + 2);
//if ($destroyrate > 400)	$destroyrate = 400;
$destroyrate = floor($urace[bpt] * $destroyrate);
$salvage = round(($config[buildings] + ($users[land] * .1)) / 5);

$totbuildings = $users[land] - $users[freeland];

function getDestroyAmount ($type)
{
	global $config, $users, $candestroy, $destroyrate;
	$candestroy[$type] = $destroyrate * $users[turns];
	if ($candestroy[$type] > $users[$type]) $candestroy[$type] = $users[$type];
	$candestroy[all] += $candestroy[$type];
	if ($candestroy[all] > $users[turns] * $destroyrate)
		$candestroy[all] = $users[turns] * $destroyrate;
}

function destroyStructures ($type, $salvage)
{
	global $users, $demolish, $candestroy, $totaldestroyed, $totalsalvaged;
	fixInputNum($demolish[$type]);
	$amount = $demolish[$type];
	if ($amount < 0)
		TheEnd("Cannot demolish a negative number of structures!");
	if ($amount > $candestroy[$type])
		TheEnd("You don't have that many!");
	$users[$type] -= $amount;
	if ($type == land)
		$users[freeland] -= $amount;
	else
	{
		$users[freeland] += $amount;
		$totaldestroyed += $amount;
	}
	$users[cash] += $amount * $salvage;
	$totalsalvaged += $amount * $salvage;
}

function printRow ($type)
{
	global $users, $uera, $candestroy;
?>
<tr><td><?=$uera[$type]?></td>
    <td class="aright"><?=commas($users[$type])?></td>
    <td class="aright"><?=commas($candestroy[$type])?></td>
    <td class="aright"><input type="text" name="demolish[<?=$type?>]" size="5" value="0"></td></tr>
<?
}

getDestroyAmount(homes);
getDestroyAmount(shops);
getDestroyAmount(industry);
getDestroyAmount(barracks);
getDestroyAmount(labs);
getDestroyAmount(farms);
getDestroyAmount(towers);
$candestroy[land] = $users[freeland];
if ($do_demolish)
{
	destroyStructures(homes,$salvage);
	destroyStructures(shops,$salvage);
	destroyStructures(industry,$salvage);
	destroyStructures(barracks,$salvage);
	destroyStructures(labs,$salvage);
	destroyStructures(farms,$salvage);
	destroyStructures(towers,$salvage);
	destroyStructures(land,0);

	$turns = ceil($totaldestroyed / $destroyrate);
	if ($users[turns] < $turns)
		TheEnd("Not enough turns!");
	saveUserData($users,"homes shops industry barracks labs farms towers land freeland cash");
	takeTurns($turns,demolish);
?>
You demolished <?=commas($totaldestroyed)?> structures in <?=$turns?> turns and made $<?=commas($totalsalvaged)?>.<hr>
<?
	getDestroyAmount(homes);
	getDestroyAmount(shops);
	getDestroyAmount(industry);
	getDestroyAmount(barracks);
	getDestroyAmount(labs);
	getDestroyAmount(farms);
	getDestroyAmount(towers);
	$candestroy[land] = $users[freeland];
}
?>
<a href="<?=$config[main]?>?action=guide&amp;section=structures&amp;era=<?=$users[era]?>">Promisance Guide: Structures</a><br><br>
Each structure demolished frees up one acre of land and you get $<?=commas($salvage)?> money for salvage.<br>
We can demolish <?=commas($destroyrate)?> structures per turn.<br>
With our resources, we can demolish <span class="cbad"><?=commas($candestroy[all])?></span> structures.<br><br>
<form method="post" action="<?=$config[main]?>?action=demolish">
<table class="inputtable">
<tr><th class="aleft">Structure</th>
    <th class="aright">We Own</th>
    <th class="aright">We Can Demolish</th>
    <th class="aright">Demolish</th></tr>
<?
printRow(shops);
printRow(homes);
printRow(barracks);
printRow(industry);
printRow(labs);
printRow(farms);
printRow(towers);
?>
<tr><td>Drop Unused Land</td>
    <td class="aright"><?=commas($users[freeland])?></td>
    <td class="aright"><?=commas($candestroy[land])?></td>
    <td class="aright"><input type="text" name="demolish[land]" size="5" value="0"></td></tr>
<tr><td colspan="4" class="acenter"><input type="submit" name="do_demolish" value="Begin Demolition"></td></tr>
</table>
</form>
<a href="<?=$config[main]?>?action=build">Build Structures</a>
<?
TheEnd("");
?>
