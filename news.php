<?
include("header.php");
include("magicfun.php");
$newslimit = 100;

if ($do_search)
{
	switch ($search_type)
	{
	case num:
		if (!$search_num)
			$dbh = "1";
		elseif ($search_by == 'Attacker')
			$dbh = "num_s=$search_num";
		elseif ($search_by == 'Defender')
			$dbh = "num_d=$search_num";
		else	$dbh = "num_s=$search_num OR num_d=$search_num";
		break;
	case clan:
		if ($search_by == 'Attacker')
			$dbh = "clan_s=$search_clan";
		elseif ($search_by == 'Defender')
			$dbh = "clan_d=$search_clan";
		else	$dbh = "clan_s=$search_clan OR clan_d=$search_clan";
		break;
	}
	$news = mysql_query("SELECT time,num_s,p1.empire AS name_s,c1.tag AS clan_s,num_d,p2.empire AS name_d,c2.tag AS clan_d,event,data0 FROM $newsdb LEFT JOIN $playerdb AS p1 ON (num_s=p1.num) LEFT JOIN $playerdb AS p2 ON (num_d=p2.num) LEFT JOIN $clandb AS c1 ON (clan_s=c1.num) LEFT JOIN $clandb AS c2 ON (clan_d=c2.num) WHERE ($dbh) AND ((event>=202 AND event<=212) OR (event>=302 AND event<=307)) ORDER BY time DESC LIMIT $newslimit;");
?>
<h2><?=mysql_num_rows($news)?> events matched:</h2>
<table class="inputtable" style="width:90%">
<tr><th>Date/Time</th>
    <th>Attacker</th>
    <th>Defender</th>
    <th>Outcome</th></tr>
<?
	while ($results = mysql_fetch_array($news))
	{
?>
<tr><td><?=date("r",$results[time])?></td>
    <td><?=$results[name_s]?> (#<?=$results[num_s]?>)<br>Clan: <?=$results[clan_s]?></td>
    <td><?=$results[name_d]?> (#<?=$results[num_d]?>)<br>Clan: <?=$results[clan_d]?></td>
    <td><?
		$rlevel = floor($results[event] / 100);
		$rcode = floor($results[event] % 100);
		$rdata = $results[data0];

		if ($rlevel == 2)
		{
			print "Spell: $spname[$rcode]<br>";
			if ($rcode == 11)
				if ($rdata < 0)
					print "Failed";
				elseif ($rdata == 0)
					print "Defense Held";
				else	print "$rdata Acres";
			elseif ($rdata < 0)
				print "Failed";
			elseif ($rdata == 0)
				print "Shielded";
			else	print "Successful";
		}
		else
		{
			switch ($rcode)
			{
			case 2:	print "Standard Attack";	break;
			case 3:	print "Surprise Attack";	break;
			case 4:	print "Guerilla Strike";	break;
			case 5:	print "Stone Bombardment";	break;
			case 6:	print "Aerial Assault";		break;
			case 7:	print "Hydro Assault";		break;
			}
			if ($results[data0] > 0)
				print "<br>$results[data0] Acres";
			else	print "<br>Defense Held";
		}
?></td></tr>
<tr><td colspan="4"><hr></td></tr>
<?
	}
?>
</table>
<?
}
?>
<form method="post" action="<?=$config[main]?>?action=news">
<table class="inputtable">
<tr><th class="aleft">Search by:</th>
    <td><label><input type="radio" name="search_by" value="Attacker"> Attacker</label> <label><input type="radio" name="search_by" value="Defender"> Defender</label> <label><input type="radio" name="search_by" value="Either" checked> Doesn't matter</label></td></tr>
<tr><th class="aleft"><label><input type="radio" name="search_type" value="num" checked> Number</label></th>
    <td><input type="text" name="search_num" size="5"></td></tr>
<tr><th class="aleft"><label><input type="radio" name="search_type" value="clan"> Clan</label></th>
    <td><select name="search_clan" size="1">
        <option value="0">None - Unallied Empires</option>
<?
$clanlist = mysql_query("SELECT num,name,tag FROM $clandb ORDER BY num;");
while ($clan = mysql_fetch_array($clanlist))
{
?>
        <option value="<?=$clan[num]?>"><?=$clan[tag]?> - <?=$clan[name]?></option>
<?
}
?>
    </select></td></tr>
<tr><td colspan="2" class="acenter"><input type="submit" name="do_search" value="Search News"></td></tr>
</table>
</form>
<?
TheEnd("");
?>
