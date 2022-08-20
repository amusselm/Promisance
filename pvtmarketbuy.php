<?
include("header.php");

function getCosts ($type)
{
	global $users, $config, $urace, $costs, $canbuy;
	$mkttype = "pmkt_".$type;
	$costbonus = 1 - ((1-$config[mktshops])*($users[barracks] / $users[land]) + $config[mktshops]*($users[shops] / $users[land]));
	if ($type == food)
		$costs[$type] = $config[$type];
	else
	{
		$costs[$type] = $config[$type] * $costbonus;
		if ($costs[$type] < $config[$type] * .7)
			$costs[$type] = $config[$type] * .7;
		$costs[$type] = round($costs[$type] * $urace[mkt]);
	}
	$canbuy[$type] = floor($users[cash] / $costs[$type]);
	if ($canbuy[$type] > $users[$mkttype])
		$canbuy[$type] = $users[$mkttype];
}

function buyUnits ($type)
{
	global $costs, $users, $uera, $buy, $canbuy;
	$amount = $buy[$type];
	fixInputNum($amount);
	$cost = $amount * $costs[$type];
	$mkttype = "pmkt_".$type;
	if ($amount == 0)
		return;
	elseif ($amount < 0)
		print "Cannot purchase a negative amount of $uera[$type]!<br>\n";
	elseif ($amount > $users[$mkttype])
		print "Not enough $uera[$type] available!<br>\n";
	elseif ($cost > $users[cash])
		print "Not enough money to buy $amount $uera[$type]!<br>\n";
	else
	{
		$users[cash] -= $cost;
		$users[$type] += $amount;
		$users[$mkttype] -= $amount;
		$canbuy[$type] -= $amount;
		print commas($amount)." $uera[$type] purchased for $".commas($cost).".<br>\n";
	}
}

function printRow ($type)
{
	global $users, $uera, $costs, $canbuy;
	$mkttype = "pmkt_".$type;
?>
<tr><td><?=$uera[$type]?></td>
    <td class="aright"><?=commas($users[$type])?></td>
    <td class="aright"><?=commas($users[$mkttype])?></td>
    <td class="aright">$<?=commas($costs[$type])?></td>
    <td class="aright"><?=commas($canbuy[$type])?></td>
    <td class="aright"><input type="text" name="buy[<?=$type?>]" size="8" value="0"></td></tr>
<?
}

for ($i = 0; $i < 5; $i++)
	getCosts($trplst[$i]);
if ($do_buy)
{
	for ($i = 0; $i < 5; $i++)
		buyUnits($trplst[$i]);
	saveUserDataNet($users,"networth cash armtrp lndtrp flytrp seatrp food pmkt_armtrp pmkt_lndtrp pmkt_flytrp pmkt_seatrp pmkt_food");
}
for ($i = 0; $i < 5; $i++)
	getCosts($trplst[$i]);
?>
Also see: <a href="<?=$config[main]?>?action=guide&amp;section=military&amp;era=<?=$users[era]?>">Promisance Guide: Military</a><br>
<form method="post" action="<?=$config[main]?>?action=pvtmarketbuy">
<table class="inputtable">
<tr><td colspan="3"><a href="<?=$config[main]?>?action=pvtmarketbuy">Buy Goods</a></td>
    <td colspan="3" class="aright"><a href="<?=$config[main]?>?action=pvtmarketsell">Sell Goods</a></td></tr>
<tr><th class="aleft">Unit</th>
    <th class="aright">Owned</th>
    <th class="aright">Avail</th>
    <th class="aright">Cost</th>
    <th class="aright">Afford</th>
    <th class="aright">Buy</th></tr>
<?
for ($i = 0; $i < 5; $i++)
	printRow($trplst[$i]);
?>
<tr><td colspan="6" class="acenter"><input type="submit" name="do_buy" value="Purchase Goods"></td></tr>
</table>
</form>
<?
TheEnd("");
?>
