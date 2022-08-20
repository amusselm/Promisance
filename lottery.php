<?
include("header.php");

if ($lockdb)
	TheEnd("Lottery is currently not available!");

$tickcost = round($users[networth] / 10);

$jackpot = sqleval("SELECT cash FROM $lotterydb WHERE num=0 AND ticket=$tick_curjp;");
$lastjackpot = sqleval("SELECT cash FROM $lotterydb WHERE num=0 AND ticket=$tick_lastjp;");
$lastnum = sqleval("SELECT cash FROM $lotterydb WHERE num=0 AND ticket=$tick_lastnum;");
$lastwin = sqleval("SELECT cash FROM $lotterydb WHERE num=0 AND ticket=$tick_lastwin;");
$jackpotgrow = sqleval("SELECT cash FROM $lotterydb WHERE num=0 AND ticket=$tick_jpgrow;");

$tickets = mysql_query("SELECT * FROM $lotterydb WHERE num=$users[num];");
if ($do_ticket)
{
	if (mysql_num_rows($tickets) >= $maxtickets)
		TheEnd("You can't buy any more tickets!");
        if ($users[cash] < $tickcost)
		TheEnd("You don't have enough for a ticket!");
	else
	{
		$tmp = sqleval("SELECT num FROM $playerdb ORDER BY num DESC LIMIT 1;");
                $ticknum = mt_rand(1,3 * $tmp);
                $jackpot += $tickcost;
		mysql_query("INSERT INTO $lotterydb (num,ticket,cash) VALUES ($users[num],$ticknum,$tickcost);");
		mysql_query("UPDATE $lotterydb SET cash=$jackpot WHERE num=0 AND ticket=$tick_curjp;");
		$users[cash] -= $tickcost;
		saveUserDataNet($users,"networth cash");
	}
	$tickets = mysql_query("SELECT * FROM $lotterydb WHERE num=$users[num];");
}
?>
Buying a lottery ticket is a good way to make extra money for our empire.<br>
When we buy a lottery ticket, that money goes into the jackpot, which goes up when nobody wins.<br>
Tickets cost $<?=commas($tickcost)?> and are valid for one drawing.<br>
Lottery drawings are held every day at noon.<br><br>
Current Jackpot: $<?=commas($jackpot)?><br><br>
<?=sqleval("SELECT COUNT(*) FROM $lotterydb WHERE num!=0;")?> tickets have been bought for the next drawing.<br>
The last lottery number was #<?=$lastnum?>,<br>
<?
if ($lastwin)
{
	$enemy = loadUser($lastwin);
	print "which $enemy[empire] (#$enemy[num]) had, winning a total of $".commas($lastjackpot)."!<br>\n";
}
else	print "which nobody had, increasing the jackpot by another $".commas($jackpotgrow)."!<br>\n";
if (mysql_num_rows($tickets))
{
	print "You have the following lottery tickets:";
	while ($ticket = mysql_fetch_array($tickets))
		print " #$ticket[ticket]";
}
else	print "You currently have no lottery tickets";
print ".<br>\n";

if ($users[cash] < $tickcost)
	print "You check your empire's treasury and find you don't have enough for a lottery ticket.<br>\n";
elseif (mysql_num_rows($tickets) < $maxtickets)
{
?>
<form method="post" action="<?=$config[main]?>?action=lottery">
<div>
<input type="submit" name="do_ticket" value="Buy a Lottery Ticket!">
</div>
</form>
<?
}
TheEnd("");
?>
