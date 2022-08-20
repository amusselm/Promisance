<?
include("funcs.php");
$ctags = loadClanTags();
HTMLbegincompact("Top Clans");

$minmembers = 3;

$allusers = mysql_query("SELECT clan,networth FROM $playerdb WHERE land>0;");
$unallied = $utotal = 0;
while ($users = mysql_fetch_array($allusers))
{
	if ($n = $users[clan])
	{
		$members[$n]++;
		$totalnet[$n] += $users[networth];
		$avgnet[$n] = round($totalnet[$n] / $members[$n]);
	}
	else	$unallied++;
	$utotal++;
}
if ($unallied == $utotal)
{
?>
<h3>No clans currently exist!</h3>
<?
	HTMLendcompact();
	exit;
}
?>
<table class="inputtable">
<tr><th colspan="5">Clan Rankings - By <?
switch ($sort_type)
{
case 'members':
	print "Total Members";
	$sortby = $members;
	break;
case 'avgnet':
	print "Average Networth";
	$sortby = $avgnet;
	break;
case 'totalnet':
	print "Total Networth";
	$sortby = $totalnet;
	break;
}
arsort($sortby);
reset($sortby);
while (list($key,$val) = each($sortby))
	$clan[] = $key;
reset($sortby);
reset($clan);
?> (<?=$minmembers?> Member Minimum)</th></tr>
<tr><th>Clan Name</th>
    <th>Tag</th>
    <th><a href="<?=$config[main]?>?action=gameranks&amp;sort_type=members">Members</a></th>
    <th><a href="<?=$config[main]?>?action=gameranks&amp;sort_type=avgnet">Average Networth</a></th>
    <th><a href="<?=$config[main]?>?action=gameranks&amp;sort_type=totalnet">Total Networth</a></th></tr>
<?
$cunlisted = $ctotal = 0;
while (list(,$num) = each($clan))
{
	$uclan = loadClan($num);
	if ($uclan[members] >= $minmembers)
	{
?>
<tr class="acenter">
    <td><?
	if ($uclan[url])
		print "<a href=\"$uclan[url]\" target=\"_blank\">";
	print "$uclan[name]";
	if ($uclan[url])
		print "</a>";	?></td>
    <td><?=$uclan[tag]?></td>
    <td><?=$uclan[members]?></td>
    <td>$<?=commas($avgnet[$uclan[num]])?></td>
    <td>$<?=commas($totalnet[$uclan[num]])?></td></tr>
<?
	}
	else	$cunlisted++;
	$ctotal++;
}
?>
</table>
<?=$cunlisted?>/<?=$ctotal?> (<?=round($cunlisted/$ctotal*100)?>%) clans don't have enough members to make this list.<br>
<?=$unallied?>/<?=$utotal?> (<?=round($unallied/$utotal*100)?>%) empires are independent.<br>
<?
HTMLendcompact();
?>
