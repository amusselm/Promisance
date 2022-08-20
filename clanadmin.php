<?
include("header.php");

if ($users[disabled] != 2)
	TheEnd("You are not an administrator!");

// this function generates the drop down box for ally and war lists
function listopt ($item)
{
	global $clandb, $uclan;
?>
<select name="<?=$item?>" size="1">
<option value="0"<?if ($uclan[$item] == 0) print " selected";?>>None</option>
<?	$list = mysql_query("SELECT num,name,tag FROM $clandb WHERE members>0 ORDER BY num DESC;");
	while ($clan = mysql_fetch_array($list))
	{
?>
<option value="<?=$clan[num]?>"<?if ($clan[num] == $uclan[$item]) print " selected";?>><?=$clan[tag]?>: <?=$clan[name]?></option>
<?
	}
?>
</select>
<?
}

?>
<form method="post" action="<?=$config[main]?>?action=clanadmin">
<div>
Clan: <select name="adminclan" size="1">
<?
	$clanlist = mysql_query("SELECT num,name,tag FROM $clandb WHERE members>0 ORDER BY num DESC;");
	while ($clan = mysql_fetch_array($clanlist))
	{
?>
<option value="<?=$clan[num]?>"<?if ($clan[num] == $adminclan) print " selected";?>><?=$clan[tag]?>: <?=$clan[name]?></option>
<?
	}
?>
</select>
<input type="submit" value="Refresh">
</div>
</form>
<?
if (!$GLOBALS[adminclan])
	TheEnd("");
$users[clan] = $GLOBALS[adminclan];
$uclan = loadClan($users[clan]);

if ($do_removeempire)
{
	$enemy = loadUser($modify_empire);
	$enemy[clan] = 0;
	saveUserData($enemy,"clan");
	addNews(114,$users,$enemy,0);
	$uclan[members]--;
	saveClanData($uclan,"members");
	TheEnd("<b>$enemy[empire] (#$enemy[num])</b> has been removed from $uclan[name].");
}
if ($do_changepass)
{
	$uclan[password] = md5($new_password);
	saveClanData($uclan,"password");
	TheEnd("Clan password changed.");
}
if ($do_changeflag)
{
	$uclan[pic] = $new_flag;
	saveClanData($uclan,"pic");
	TheEnd("Clan flag changed.");
}
if ($do_changename)
{
	if (!$new_name)
		TheEnd("No new name specified!");
	$uclan[name] = $new_name;
	saveClanData($uclan,"name");
	TheEnd("Clan name changed.");
}
if ($do_changeurl)
{
	$uclan[url] = $new_url;
	saveClanData($uclan,"url");
	TheEnd("Clan URL changed.");
}
if ($do_changemotd)
{
	$uclan[motd] = $new_motd;
	saveClanData($uclan,"motd");
	TheEnd("Clan MOTD changed.");
}
if ($do_makefounder)
{
	$newfounder = loadUser($modify_empire);
	$uclan[founder] = $newfounder[num];
	saveClanData($uclan,"founder");
	addNews(115,$users,$enemy,0);
	TheEnd("<b>$newfounder[empire] (#$newfounder[num])</b> is now the leader of <b>$uclan[name]</b>.");
}
if ($do_changerelations)
{
	$uclan[ally1] = $ally1;
	$uclan[ally2] = $ally2;
	$uclan[ally3] = $ally3;
	$uclan[war1] = $war1;
	$uclan[war2] = $war2;
	$uclan[war3] = $war3;
	saveClanData($uclan,"ally1 ally2 ally3 war1 war2 war3");
	TheEnd("Clan relations changed.");
}
?>
<br>
<form method="post" action="<?=$config[main]?>?action=clanadmin">
<div>
<input type="hidden" name="adminclan" value=<?=$adminclan?>>
<h3><?=$uclan[name]?> Relations</h3>
<table class="inputtable">
<tr><th><span class="cgood">Ally</span><br>Cannot attack</th>
    <th><span class="cbad">War</span><br>Infinite attacks/magic</th></tr>
<tr><td><?listopt(ally1);?></td>
    <td><?listopt(war1);?></td></tr>
<tr><td><?listopt(ally2);?></td>
    <td><?listopt(war2);?></td></tr>
<tr><td><?listopt(ally3);?></td>
    <td><?listopt(war3);?></td></tr>
<tr><td colspan="2" class="acenter"><input type="submit" name="do_changerelations" value="Change Relations"></td></tr>
</table><br>
<table class="inputtable">
<caption><b>Empire List</b></caption>
<tr><th>Modify?</th>
    <th>Empire</th></tr>
<?
$dblist = mysql_query("SELECT empire,num FROM $playerdb WHERE clan=$uclan[num];");
while ($listclan = mysql_fetch_array($dblist))
{
?>
<tr><td class="acenter"><input type="radio" name="modify_empire" value="<?=$listclan[num]?>"<?if ($listclan[num] == $uclan[founder]) print " CHECKED";?>></td>
    <td class="acenter"><?=$listclan[empire]?> (#<?=$listclan[num]?>)</td></tr>
<?
}
?>
<tr><th><input type="submit" name="do_makefounder" value="Leader"></th>
    <th><input type="submit" name="do_removeempire" value="Remove"></th></tr>
</table><br>
<table class="inputtable">
<tr><th>Change Password:</th>
    <td class="acenter"><input type="password" name="new_password" size="8"></td>
    <td class="acenter"><input type="submit" name="do_changepass" value="Change Password"></td></tr>
<tr><th>Clan Name:</th>
    <td class="acenter"><input type="text" name="new_name" value="<?=$uclan[name]?>" size="32"></td>
    <td class="acenter"><input type="submit" name="do_changename" value="Change Name"></td></tr>
<tr><th>Flag URL:</th>
    <td class="acenter"><input type="text" name="new_flag" value="<?=$uclan[pic]?>" size="32"></td>
    <td class="acenter"><input type="submit" name="do_changeflag" value="Change Flag"></td></tr>
<tr><th>Site URL:</th>
    <td class="acenter"><input type="text" name="new_url" value="<?=$uclan[url]?>" size="32"></td>
    <td class="acenter"><input type="submit" name="do_changeurl" value="Change URL"></td></tr>
</table><br>
Clan MOTD (Message of the Day, all members see on Main Menu, <b>HTML ALLOWED</b>):<br>
<textarea rows="10" cols="60" name="new_motd"><?=$uclan[motd]?></textarea><br>
<input type="submit" name="do_changemotd" value="Change MOTD">
</div>
</form>
<?
TheEnd("");
?>
