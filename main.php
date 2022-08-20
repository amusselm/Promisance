<?
include("header.php");
if ($mark_news)
{
	$users[newstime] = $time;
	saveUserData($users,"newstime");
}

printMainStats($users,$urace,$uera);
if ($users[turnsused] <= $config[protection])
{
?>
<span class="mprotected">Under New Player Protection (<?=$config[protection]?> turns)</span><br>
<?
}
$nextturn = $perminutes - ((date("i") + $turnoffset) % $perminutes);
?>
<b><?=$datetime?></b><br>
You get <?=$turnsper?> <?=plural($turnsper,turns,turn)?> every <?=$perminutes?> <?=plural($perminutes,minutes,minute)?><br>
<?
if ($lockdb)
	print "Turns are currently stopped.<br><br>\n";
else	print "Next ".plural($turnsper,turns,turn)." in $nextturn ".plural($nextturn,minutes,minute)."<br><br>\n";
if ($users[clan])
{
	$uclan = loadClan($users[clan]);
	if ($uclan[motd])
	{
?>
<table class="inputtable" style="width:50%">
<tr><td><hr></td></tr>
<tr><th>Clan News:</th></tr>
<tr><td><tt><?=str_replace("\n","<br>",$uclan[motd])?></tt></td></tr>
<tr><td><hr></td></tr>
</table>
<?
	}
}
$newmsgs = numNewMessages();
$oldmsgs = numTotalMessages() - $newmsgs;
print "<a href=\"$config[main]?action=messages\"><b>";
if ($newmsgs + $oldmsgs)
	print "You have $newmsgs new ".plural($newmsgs,messages,message)." and $oldmsgs old ".plural($oldmsgs,messages,message);
else	print "Send a message";
print "</b></a><br>\n";

if ($all_news)
	$users[newstime] = $time - 86400*7;			// show all news under 1wk old
$hasnews = printNews($users);
if ($all_news)
{
	if (!$hasnews)
		print "<b>No archived news</b><br>\n";
}
else
{
	if ($hasnews)
		print "<a href=\"$config[main]?action=main&amp;mark_news=yes\">Mark News as Read</a><br>\n";
	else	print "<b>No new happenings</b><br>\n";
	print "<a href=\"$config[main]?action=main&amp;all_news=yes\">View News Archive (7 days)</a><br>\n";
}
TheEnd("");
?>
