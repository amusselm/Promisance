<?
include("header.php");

if ($lockdb)
	TheEnd("Public market currently disabled!");

function buyUnits ($type)
{
	global $playerdb, $marketdb, $users, $uera, $buy, $buyprice, $datetime, $time;
	$amount = $buy[$type];
	fixInputNum($amount);
	$price = $buyprice[$type];
	fixInputNum($price);
	if ($amount == 0)				// did I specify a value?
		return;
	$market = mysql_fetch_array(mysql_query("SELECT * FROM $marketdb WHERE type='$type' AND price<=$price AND seller!=$users[num] AND time<=$time ORDER BY price ASC, time ASC LIMIT 1;"));
	if (!$market[amount])
		return;
	if ($market[price] < $price)
	{
		$price = $market[price];
		print "While you were browsing, cheaper $uera[$type] arrived on the market!<br>\n";
		if ($amount > $market[amount])
			print "However, only ".commas($market[amount])." are available.<br>\n";
	}
	if ($amount > $market[amount])
		$amount = $market[amount];
	if ($amount < 0)
		print "Cannot purchase a negative amount of $uera[$type]!<br>\n";
	elseif ($amount * $price > $users[cash])
		print "You don't have enough money to buy that many $uera[$type]!<br>\n";
	else
	{
		$enemy = loadUser($market[seller]);
		$spent = $amount * $price;
		$sales = round($spent * .95);
		$users[cash] -= $spent;
		$enemy[cash] += $sales;
		$users[$type] += $amount;
		mysql_query("UPDATE $marketdb SET amount=(amount-$amount) WHERE id=$market[id];");
		mysql_query("DELETE FROM $marketdb WHERE amount=0;");
		print commas($amount)." $uera[$type] purchased from the market for $".commas($spent).".<br>\n";
		switch ($type)
		{
		case food:	addNews(100,$users,$enemy,1,$amount,$spent,$sales);	break;
		case armtrp:	addNews(100,$users,$enemy,2,$amount,$spent,$sales);	break;
		case lndtrp:	addNews(100,$users,$enemy,3,$amount,$spent,$sales);	break;
		case flytrp:	addNews(100,$users,$enemy,4,$amount,$spent,$sales);	break;
		case seatrp:	addNews(100,$users,$enemy,5,$amount,$spent,$sales);	break;
		}
		saveUserDataNet($enemy,"networth cash");
	}
}

function getCosts ($type)
{
	global $marketdb, $users, $costs, $avail, $canbuy, $time;
	$market = mysql_fetch_array(mysql_query("SELECT * FROM $marketdb WHERE type='$type' AND seller!=$users[num] AND time<=$time ORDER BY price ASC, time ASC LIMIT 1;"));
	if ($market[id])
	{
		$costs[$type] = $market[price];
		$avail[$type] = $market[amount];
		$canbuy[$type] = floor($users[cash] / $market[price]);
		if ($canbuy[$type] > $market[amount])
			$canbuy[$type] = $market[amount];
	}
	else	$costs[$type] = $avail[$type] = $canbuy[$type] = 0;
}

function printRow ($type)
{
	global $users, $uera, $costs, $avail, $canbuy;
?>
<tr><td><?=$uera[$type]?></td>
    <td class="aright"><?=commas($users[$type])?></td>
    <td class="aright"><?=commas($avail[$type])?></td>
    <td class="aright"><input type="hidden" name="buyprice[<?=$type?>]" value="<?=$costs[$type]?>">$<?=commas($costs[$type])?></td>
    <td class="aright"><?=commas($canbuy[$type])?></td>
    <td class="aright"><input type="text" name="buy[<?=$type?>]" size="6" value="0"></td></tr>
<?
}

if ($users[turnsused] <= $config[protection])
	TheEnd("Cannot trade on the public market while under protection!");

for ($i = 0; $i < 5; $i++)
	getCosts($trplst[$i]);
if ($do_buy)
{
	for ($i = 0; $i < 5; $i++)
		buyUnits($trplst[$i]);
	saveUserDataNet($users,"networth cash armtrp lndtrp flytrp seatrp food");
}
for ($i = 0; $i < 5; $i++)
	getCosts($trplst[$i]);
?>
Also see: <a href="<?=$config[main]?>?action=guide&amp;section=military&amp;era=<?=$users[era]?>">Promisance Guide: Military</a><br>
<form method="post" action="<?=$config[main]?>?action=pubmarketbuy">
<table class="inputtable">
<tr><td colspan="3"><a href="<?=$config[main]?>?action=pubmarketbuy">Buy Goods</a></td>
    <td colspan="3" class="aright"><a href="<?=$config[main]?>?action=pubmarketsell">Sell Goods</a></td></tr>
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
