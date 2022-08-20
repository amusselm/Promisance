<?
include("header.php");

// preset prices; getCosts adjusts them according to your barracks/land ratio and race bonus
$costs[armtrp] = $config[armtrp] * .32;
$costs[lndtrp] = $config[lndtrp] * .34;
$costs[flytrp] = $config[flytrp] * .36;
$costs[seatrp] = $config[seatrp] * .38;
$costs[food] = $config[food] * .20;

function getCosts ($type)
{
	global $users, $urace, $costs, $canbuy, $config;
// when selling units, the bonus INCREASES the price, so you can get more for them
	$costbonus = 1 + (1-$config[mktshop]) * ($users[barracks] / $users[land]) + $config[mktshop] * ($users[shops] / $users[land]);
	if ($type == food)				// food unaffected by cost bonuses
		$costs[$type] = round($costs[$type]);
	else
	{
		$costs[$type] = $costs[$type] * $costbonus;
		if ($costs[$type] > $config[$type] * .5)
			$costs[$type] = $config[$type] * .5;
		$costs[$type] = round($costs[$type] * $urace[mkt]);
	}
}

function sellUnits ($type)
{
	global $users, $uera, $sell, $costs, $config;
	$amount = $sell[$type];
	fixInputNum($amount);
	$cost = $amount * $costs[$type];
	if ($amount == 0)
		return;
	elseif ($amount < 0)
		print "Cannot sell a negative amount of $uera[$type]!<br>\n";
	elseif ($type != food && $amount > ($config[bmperc] - $users["bmper"."$type"])/10000 * $users[$type])
		print "You can't sell that many $uera[$type]!<br>\n";
	elseif ($type == food && $amount > $users[$type])
		print "You don't have that many $uera[$type]!<br>\n";

	else
	{
		$users[cash] += $cost;
		$users["bmper"."$type"] = $users["bmper"."$type"] + ($amount/$users[$type])*10000;
		$users[$type] -= $amount;
		print commas($amount)." $uera[$type] sold for $".commas($cost)."<br>\n";
	}
}

function printRow ($type)
{
	global $users, $uera, $costs, $config;
?>
<tr><td><?=$uera[$type]?></td>
    <td class="aright"><?=commas($users[$type])?></td>
    <td class="aright"><? if($type!=food) print commas(floor(($config[bmperc] - $users["bmper"."$type"])/10000 * $users[$type])); else print commas($users[$type]);?></td>
    <td class="aright">$<?=$costs[$type]?></td>
    <td class="aright"><input type="text" name="sell[<?=$type?>]" size="8" value="0"></td></tr>
<?
}

for ($i = 0; $i < 5; $i++)
	getCosts($trplst[$i]);
if ($do_sell)
{
	for ($i = 0; $i < 5; $i++)
		sellUnits($trplst[$i]);
	saveUserDataNet($users,"networth cash armtrp lndtrp flytrp seatrp food bmperseatrp bmperflytrp bmperlndtrp bmperarmtrp");
}
?>
Also see: <a href="<?=$config[main]?>?action=guide&amp;section=military&amp;era=<?=$users[era]?>">Promisance Guide: Military</a><br>
<form method="post" action="<?=$config[main]?>?action=pvtmarketsell">
<table class="inputtable">
<tr><td colspan="3"><a href="<?=$config[main]?>?action=pvtmarketbuy">Buy Goods</a></td>
    <td colspan="2" class="aright"><a href="<?=$config[main]?>?action=pvtmarketsell">Sell Goods</a></td></tr>
<tr><th class="aleft">Unit</th>
    <th class="aright">Owned</th>
    <th class="aright">Can Sell</th>
    <th class="aright">Price</th>
    <th class="aright">Sell</th></tr>
<?
for ($i = 0; $i < 5; $i++)
	printRow($trplst[$i]);
?>
<tr><td colspan="6" class="acenter"><input type="submit" name="do_sell" value="Sell Goods"></td></tr>
</table>
</form>
<?
TheEnd("");
?>
