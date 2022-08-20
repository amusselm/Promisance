<?
include("funcs.php");

function printTop10Header ()
{
	global $config;
?>
<tr class="era0">
    <th style="width:5%" class="aright"><a href="<?=$config[main]?>?action=top10&amp;sort=rank">Rank</a></th>
    <th style="width:25%">Empire</th>
    <th style="width:10%" class="aright">Land</th>
    <th style="width:15%" class="aright">Networth</th>
    <th style="width:10%">Clan</th>
    <th style="width:10%">Race</th>
    <th style="width:5%">Era</th>
    <th style="width:8%"><a href="<?=$config[main]?>?action=top10&amp;sort=offtotal">O</a></th>
    <th style="width:8%"><a href="<?=$config[main]?>?action=top10&amp;sort=deftotal">D</a></th>
    <th style="width:4%"><a href="<?=$config[main]?>?action=top10&amp;sort=kills">K</a></th></tr>
<?
}

$ctags = loadClanTags();
HTMLbegincompact("Top 10");
switch ($sort)
{
	case "rank":		$sort = "rank ASC";			break;
	case "offtotal":	$sort = "offtotal DESC, rank ASC";	break;
	case "deftotal":	$sort = "deftotal DESC, rank ASC";	break;
	case "kills":		$sort = "kills DESC, rank ASC";		break;
	default:		$sort = "rank ASC";			break;
}
?>
<b>Promisance Top 10 Listing</b><br>
Current Game Time: <?=$datetime?><br><br>
<b><?=sqleval("SELECT COUNT(*) FROM $playerdb WHERE online=1;")?></b> of the <b><?=sqleval("SELECT COUNT(*) FROM $playerdb;")?></b> players in the game are currently online.<br>
<b><?=$killed=sqleval("SELECT SUM(kills) FROM $playerdb;")?></b> empires have been destroyed, and <b><?=sqleval("SELECT COUNT(*) FROM $playerdb WHERE land=0;")-$killed?></b> empires have been abandoned.<br>
<b><?=sqleval("SELECT COUNT(*) FROM $playerdb WHERE disabled=3;")?></b> accounts have been disabled by Administration.<br><br>
<b>Rankings are updated every <?=$perminutes?> minute<?=plural($perminutes,"s","")?>.</b><br><br>
Color Key: <span class="mprotected">Protected/Vacation</span>, <span class="mdead">Dead</span>, <span class="mdisabled">Disabled</span>, <span class="madmin">Administrator</span><br>
Stats Key: O = Offensive Actions (success%), D = Defenses (success%), K = Number of empires killed<br>
Empires whose ranks are prefixed with a * are currently online.<br>
<table class="scorestable">
<?
printTop10Header();
$rtags = loadRaceTags();
$etags = loadEraTags();
$users[num] = 0;	// so we can use printScoreLine() and not worry
$users[clan] = 0;	// about it using the ingame-specific colors
$top10 = mysql_query("SELECT rank,empire,num,land,networth,clan,online,disabled,turnsused,vacation,race,era,offsucc,offtotal,defsucc,deftotal,kills FROM $playerdb ORDER BY $sort LIMIT 10;");
while ($enemy = mysql_fetch_array($top10))
	printSearchLine();
printTop10Header();
?>
</table>
<?
HTMLendcompact();
?>
