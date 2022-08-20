<?
include("header.php");

function printRow ($type)
{
	global $users, $uera, $canbuild;
?>
<tr><td><?=$uera[$type]?></td>
    <td class="aright"><?=commas($users[$type])?></td>
    <td class="aright"><?=commas($canbuild)?></td>
    <td class="aright"><input type="text" name="build[<?=$type?>]" size="5" value="0"></td></tr>
<?
}

function getBuildAmounts ()
{
	global $users, $config, $urace, $buildcost, $buildrate, $canbuild;
	$buildcost = round($config[buildings] + ($users[land] * 0.1));
	$buildrate = $users[land] * 0.015 + 4;
//	if ($buildrate > 400)	$buildrate = 400;
	$buildrate = round($urace[bpt] * $buildrate);
	$canbuild = floor($users[cash] / $buildcost);		// limit by money
	if ($canbuild > $buildrate * $users[turns])		// by turns
		$canbuild = $buildrate * $users[turns];
	if ($canbuild > $users[freeland])			// and by land
		$canbuild = $users[freeland];
}

function buildStructures ($type, $cost)
{
	global $users, $build, $totalbuild, $totalspent;
	$amount = $build[$type];
	fixInputNum($amount);
	if ($amount < 0)
		TheEnd("Cannot build a negative number of structures!");
	$users[$type] += $amount;
	$totalbuild += $amount;
	$users[freeland] -= $amount;
	$users[cash] -= $amount * $cost;
	$totalspent += $amount * $cost;
}

getBuildAmounts();
if ($do_build)
{	// nothing gets saved until later; if one has invalid input, it'll get caught and will prevent the build
	$totalbuild = $totalspent = 0;
	buildStructures(homes,$buildcost);
	buildStructures(shops,$buildcost);
	buildStructures(industry,$buildcost);
	buildStructures(barracks,$buildcost);
	buildStructures(labs,$buildcost);
	buildStructures(farms,$buildcost);
	buildStructures(towers,$buildcost);
	if ($totalbuild > $canbuild)	// this takes into account turns, money, AND free land all at once
		TheEnd("You can't build that many!");
	if ($users[cash] < 0)		// in case we decide to add variable building costs
		TheEnd("You don't have enough money!");
	$turns = ceil($totalbuild / $buildrate);
	saveUserData($users,"homes shops industry barracks labs farms towers freeland cash");

	takeTurns($turns,build);
?>
You built <?=commas($totalbuild)?> total structures in <?=commas($turns)?> turns and spent $<?=commas($totalspent)?>.<hr>
<?
	getBuildAmounts();
} 
?>
Each structure consumes one acre of free land and costs $<?=commas($buildcost)?> to build.<br>
You can build <?=commas($buildrate)?> structures per turn.<br>
With our resources, we can build <span class="cgood"><?=commas($canbuild)?></span> structures.<br><br>
<form method="post" action="<?=$config[main]?>?action=build">
<table class="inputtable">
<tr><th class="aleft">Structure</th>
    <th class="aright">We Own</th>
    <th class="aright">We Can Build</th>
    <th class="aright">Build</th></tr>
<?
printRow(shops);
printRow(homes);
printRow(barracks);
printRow(industry);
printRow(labs);
printRow(farms);
printRow(towers);
?>
<tr><td>Unused Land</td>
    <td class="aright"><?=commas($users[freeland])?></td>
    <td colspan="2"></td></tr>
<tr><td colspan="4" class="acenter"><input type="submit" name="do_build" value="Begin Construction"></td></tr>
</table>
</form>
<a href="<?=$config[main]?>?action=demolish">Demolish Structures</a>
<?
TheEnd("");
?>
