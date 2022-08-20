<?
include("header.php");
?>
<b>Promisance Scores Listing</b><br>
Current Game Time: <?=$datetime?><br><br>
<b><?=sqleval("SELECT COUNT(*) FROM $playerdb WHERE online=1;")?></b> of the <b><?=sqleval("SELECT COUNT(*) FROM $playerdb;")?></b> players in the game are currently online.<br>
<b><?=$killed=sqleval("SELECT SUM(kills) FROM $playerdb;")?></b> empires have been destroyed, and <b><?=sqleval("SELECT COUNT(*) FROM $playerdb WHERE land=0;")-$killed?></b> empires have been abandoned.<br>
<b><?=sqleval("SELECT COUNT(*) FROM $playerdb WHERE disabled=3;")?></b> accounts have been disabled by Administration.<br>
<table class="scorestable">
<?
$start = $users[rank] - 15;
if ($start < 10)
	$start = 10;

for ($topten = 0; $topten < 2; $topten++)	// why duplicate the code when we can use it twice?
{
	if ($topten == 0)
		$limit = "10";
	else	$limit = "$start,30";
	$scores = mysql_query("SELECT rank,empire,num,land,networth,clan,race,era,online,disabled,turnsused,vacation FROM $playerdb ORDER BY rank ASC LIMIT $limit;");
	if (mysql_num_rows($scores) == 0) break;
	printScoreHeader($users[era]);
	while ($enemy = mysql_fetch_array($scores))
		printScoreLine();
}
printScoreHeader($users[era]);
?>
</table>
<?
TheEnd("");
?>
