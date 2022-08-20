<?
include("header.php");

if ($lockdb)
	TheEnd("Public market currently disabled!");

function printRow ($type)
{
	global $users, $uera, $costs, $basket;
?>
<tr><td><?=$uera[$type]?></td>
    <td class="aright"><?=commas($users[$type])?></td>
    <td class="aright"><?=commas($basket[$type])?></td>
    <td class="aright">$<input type="text" name="sellprice[<?=$type?>]" value="<?=$costs[$type]?>" size="5"></td>
    <td class="aright"><input type="text" name="sell[<?=$type?>]" value="0" size="8"></td></tr>
<?
}

function getCosts ($type)
{
	global $marketdb, $config, $users, $costs, $time;
	$market = mysql_fetch_array(mysql_query("SELECT * FROM $marketdb WHERE type='$type' AND seller!=$users[num] AND time<=$time ORDER BY price ASC, time ASC LIMIT 1;"));
	if ($market[price])
		$costs[$type] = $market[price];
	else	$costs[$type] = $config[$type];
}

function calcBasket ($type, $percent)
{
	global $marketdb, $users, $uera, $basket, $config, $time;
	$onsale = 0;
	$goods = mysql_query("SELECT * FROM $marketdb WHERE type='$type' AND seller=$users[num] ORDER BY amount DESC;");
	while ($market = mysql_fetch_array($goods))
	{
		$onsale += $market[amount];
?>
<tr><td><?=$uera[$type]?></td>
    <td class="aright"><?=commas($market[amount])?></td>
    <td class="aright">$<?=commas($market[price])?></td>
    <td class="aright"><?
		if (($market[time] -= $time) < 0)
		{
			?>On Sale for <?=round($market[time]/-3600,1)?> hour(s) - <a href="<?=$config[main]?>?action=pubmarketsell&amp;do_removeunits=yes&amp;remove_id=<?=$market[id]?>">Remove</a><?
		}
		else
		{
			?>In Transit, <?=round($market[time]/3600,1)?> hour(s) remaining<?
		}
?></td></tr>
<?
	}
	$basket[$type] = round(($users[$type] + $onsale) * $percent) - $onsale;
	if ($basket[$type] < 0)
		$basket[$type] = 0;
}

function sellUnits ($type)
{
	global $marketdb, $users, $uera, $sell, $sellprice, $config, $basket, $time;
	$minprice = $config[$type] * 0.2;
	$maxprice = $config[$type] * 2.5;
	$amount = $sell[$type];
	fixInputNum($amount);
	$price = $sellprice[$type];
	fixInputNum($price);
	if (($amount == 0) || ($price == 0))
		return;
	if ($amount < 0)
		print "Cannot sell a negative number of $uera[$type]!<br>\n";
	elseif ($amount > $basket[$type])
		print "Cannot sell that many $uera[$type]!<br>\n";
	elseif ($price < $minprice)
		print "Cannot sell $uera[$type] that cheap!<br>\n";
	elseif ($price > $maxprice)
		print "Cannot sell $uera[$type] for that high of a price!<br>\n";
	else
	{
		$users[$type] -= $amount;
		$basket[$type] -= $amount;
		mysql_query("INSERT INTO $marketdb (type,seller,amount,price,time) VALUES ('$type',$users[num],$amount,$price,$time+3600*$config[market]);");
?>
<tr><td><?=$uera[$type]?></td>
    <td class="aright"><?=commas($amount)?></td>
    <td class="aright">$<?=commas($price)?></td>
    <td class="aright">In Transit, <?=$config[market]?> hour(s) remaining</td></tr>
<?
	}
}

function removeUnits ($id)
{
	global $marketdb, $users, $uera;
	$market = mysql_fetch_array(mysql_query("SELECT * FROM $marketdb WHERE id=$id;"));
	if ($market[seller] != $users[num])
		print "No such shipment!<br>\n";
	else
	{
		$amount = $market[amount];
		$type = $market[type];
		mysql_query("DELETE FROM $marketdb WHERE id=$id;");
		$users[$type] += floor($market[amount] * .8);
		print "You have removed ".commas($amount)." $uera[$type] from the market.<br>\n";
		saveUserDataNet($users,"networth $type");
	}
}

if ($users[turnsused] <= $config[protection])
	TheEnd("Cannot trade on the public market while under protection!");

if ($do_removeunits)
	removeUnits($remove_id);
?>
<table class="inputtable">
<caption>On the market or on the way we have:</caption>
<tr><th class="aleft">Unit</th>
    <th class="aright">Quantity</th>
    <th class="aright">Price</th>
    <th class="aright">Status</th></tr>
<?
for ($i = 0; $i < 4; $i++)
	calcBasket($trplst[$i],0.25);
calcBasket(food,0.90);
if ($do_sell)
{
	for ($i = 0; $i < 5; $i++)
		sellUnits($trplst[$i]);
	saveUserDataNet($users,"networth armtrp lndtrp flytrp seatrp food");
}
?>
    <tr><td colspan="4"><hr></td></tr>
</table>
Also see: <a href="<?=$config[main]?>?action=guide&amp;section=military&amp;era=<?=$users[era]?>">Promisance Guide: Military</a><br>
It will take <?=$config[market]?> hours for your goods to reach the market.<br>
<form method="post" action="<?=$config[main]?>?action=pubmarketsell">
<?
for ($i = 0; $i < 5; $i++)
	getCosts($trplst[$i]);
?>
<table class="inputtable">
<tr><td colspan="2"><a href="<?=$config[main]?>?action=pubmarketbuy">Buy Goods</a></td>
    <td>&nbsp;</td>
    <td colspan="2" class="aright"><a href="<?=$config[main]?>?action=pubmarketsell">Sell Goods</a></td></tr>
<tr><th class="aleft">Unit</th>
    <th class="aright">Owned</th>
    <th class="aright">Can Sell</th>
    <th class="aright">Price</th>
    <th class="aright">Sell</th></tr>
<?
for ($i = 0; $i < 5; $i++)
	printRow($trplst[$i]);
?>
<tr><td colspan="5" class="acenter"><input type="submit" name="do_sell" value="Sell Goods"></td></tr>
</table>
</form>
<?
TheEnd("");
?>
