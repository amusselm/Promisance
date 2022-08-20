<?
include("header.php");

// this function generates the drop down box for ally and war lists
function listopt ($item)
{
	global $clandb, $uclan;
?>
<select name="<?=$item?>" size="1">
<option value="0"<?if ($uclan[$item] == 0) print " selected";?>>None</option>
<?
	$list = mysql_query("SELECT num,name,tag FROM $clandb WHERE members>0 ORDER BY num DESC;");
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

function clanids()
{
	global $uclan;
?>
<table class="inputtable">
<tr><th>Change Password:</th>
    <td class="acenter">New password: <input type="password" name="new_password" size="8"><br>
                        Verify password: <input type="password" name="new_password_verify" size="8"></td>
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
<?
}

function motd()
{
	global $uclan;
?>
Clan MOTD (Message of the Day, all members see on Main Menu, displayed exactly as seen below, NO HTML):<br>
<textarea rows="10" cols="60" name="new_motd"><?=$uclan[motd]?></textarea><br>
<input type="submit" name="do_changemotd" value="Change MOTD">
<?
}

function playeropts()
{
	global $uclan, $playerdb, $users;
?>
<?=$uclan[name]?> currently has <?=$uclan[members]?> members.<br><br>
<table class="inputtable">
<caption><b>Empire List</b></caption>
<tr><th>Modify?</th>
    <th>Empire</th>
    <th>Networth</th>
    <th>Rank</th>
    <th>Sharing</th></tr>
<?
	$dblist = mysql_query("SELECT empire,num,forces,rank,networth FROM $playerdb WHERE clan=$uclan[num];");
	while ($listclan = mysql_fetch_array($dblist))
	{
?>
<tr><td class="acenter"><input type="radio" name="modify_empire" value="<?=$listclan[num]?>"<?if ($listclan[num] == $uclan[founder]) print " CHECKED";?>></td>
    <td class="acenter"><?=$listclan[empire]?> (#<?=$listclan[num]?>)</td>
    <td class="aright">$<?=commas($listclan[networth])?></td>
    <td class="aright">#<?=$listclan[rank]?></td>
    <td class="acenter"><span class=<?if ($listclan[forces]) print '"cgood">YES'; else print '"cbad">NO';?></span></td></tr>
<?
	}
?>
<tr><td class="acenter"><?
	if ($users[num] == $uclan[founder])
	{
?>
    <input type="submit" name="do_makefounder" value="Leader"><br>
    <input type="submit" name="do_remasst" value="Remove Asst. Leader"><input type="submit" name="do_makeasst" value="Make Asst. Leader"><br>
<?
	}
?>
    <input type="submit" name="do_remfa1" value="Remove FA1"><input type="submit" name="do_makefa1" value="Make FA1"><br>
    <input type="submit" name="do_remfa2" value="Remove FA2"><input type="submit" name="do_makefa2" value="Make FA2"><br>
    <input type="submit" name="do_removeempire" value="Remove"></td></tr>
</table><br>
<?
}

function relations()
{
?>
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
<?
}

if ($users[clan] == 0)
	TheEnd("You are not in a clan!");

$uclan = loadClan($users[clan]);

if (($uclan[founder] != $users[num]) && ($uclan[fa1] != $users[num]) && ($uclan[fa2] != $users[num]) && ($uclan[asst] != $users[num]))
	TheEnd("You do not have administrative authority in your clan!");

if ($do_removeempire)
{
	$enemy = loadUser($modify_empire);
	if ($enemy[clan] != $uclan[num])
		TheEnd("That empire is not in your clan!");
	if ($enemy[num] == $uclan[founder])
		TheEnd("The leader must formally disband the clan.");
	$enemy[clan] = 0;
	saveUserData($enemy,"clan");
	addNews(114,$users,$enemy,0);
	$uclan[members]--;
	saveClanData($uclan,"members");
	TheEnd("You have removed <b>$enemy[empire] (#$enemy[num])</b> from your clan.");
}
if ($do_changepass)
{
	if ($new_password == $new_password_verify)
	{
		$uclan[password] = md5($new_password);
		saveClanData($uclan,"password");
		TheEnd("Clan password changed.");
	}
	else	TheEnd("Passwords don't match!");
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
	$uclan[name] = trim(HTMLSpecialChars($new_name));
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
	$uclan[motd] = HTMLSpecialChars($new_motd);	// don't allow HTML tags
	saveClanData($uclan,"motd");
	TheEnd("Clan news changed.");
}
if ($do_makefounder)
{
	if ($users[num] != $uclan[founder])
		theEnd("Only the clan leader can change the leader.");
	$newfounder = loadUser($modify_empire);
	if ($newfounder[clan] == $users[clan])
	{
		$uclan[founder] = $newfounder[num];
		saveClanData($uclan,"founder");
		addNews(115,$users,$newfounder,0);
		TheEnd("<b>$newfounder[empire] (#$newfounder[num])</b> is now the leader of <b>$uclan[name]</b>.");
	}
	else	TheEnd("That empire is not a member of your clan!");
}
function rempos($pos)
{
	global $uclan, $users;
	$oldpos = loadUser($uclan[$pos]);
	if ($oldpos[num])
	{
		$uclan[$pos] = 0;
		saveClanData($uclan,"$pos");
		addNews(119,$users,$oldpos,0);
		return "<b>$oldpos[empire] (#$oldpos[num])</b> has been removed from authority for <b>$uclan[name]</b>.";
	}
	else	return "That position is already empty!";
}
function changepos($pos)
{
	global $modify_empire, $users, $uclan;
	$newpos = loadUser($modify_empire);
	if (($newfa[num] == $uclan[fa1]) || ($newfa[num] == $uclan[fa2]) || ($newfa[num] == $uclan[asst]))
		TheEnd("That empire already has a position of authority.");
	if ($newpos[clan] == $users[clan])
	{
		rempos($pos);
		$uclan[$pos] = $newpos[num];
		saveClanData($uclan,"$pos");
		addNews(118,$users,$newpos,0);
		if ($pos == "asst")
			TheEnd("<b>$newpos[empire] (#$newpos[num])</b> is now the Assistant Leader for <b>$uclan[name]</b>.");
		else
			TheEnd("<b>$newpos[empire] (#$newpos[num])</b> is now a Minister of Foreign Affairs for <b>$uclan[name]</b>.");
	}
	else	TheEnd("That empire is not a member of your clan!");
}
if ($do_makeasst)
	changepos(asst);
if ($do_makefa1)
	changepos(fa1);
if ($do_makefa2)
	changepos(fa2);
if ($do_remasst)
	theEnd(rempos(asst));
if ($do_makeasst)
	changepos(asst);
if ($do_remfa1)
	theEnd(rempos(fa1));
if ($do_remfa2)
	theEnd(rempos(fa2));
if ($do_changerelations)
{
	$uclan[ally1] = $ally1;
	$uclan[ally2] = $ally2;
	$uclan[ally3] = $ally3;
	$uclan[war1] = $war1;
	$uclan[war2] = $war2;
	$uclan[war3] = $war3;
	saveClanData($uclan,"ally1 ally2 ally3 war1 war2 war3");
	TheEnd("You have changed the relations for your clan.");
}

if ($uclan[url])
{
?><a href="<?=$uclan[url]?>" target="_blank"><?
}
if ($uclan[pic])
{
?><img src="<?=$uclan[pic]?>" style="border:0" alt="<?=$uclan[name]?>'s Home Page"><?
}
elseif ($uclan[url])
{
?><?=$uclan[name]?>'s Home Page<?
}
if ($uclan[url])
{
?></a><?
}
?>
<br>
<table style="background-color:#1F1F1F">
<tr><th class="era<?=$users[era]?>">Clan Administration for <i><?=$uclan[tag]?></i></th></tr>
<tr><td class="acenter"><a href="<?=$config[main]?>?action=clanstats&amp;sort_type=avgnet">Top Clans by Average Networth</a></td></tr>
<tr><td class="acenter"><a href="<?=$config[main]?>?action=clanstats&amp;sort_type=members">Top Clans by Membership</a></td></tr>
<tr><td class="acenter"><a href="<?=$config[main]?>?action=clanstats&amp;sort_type=totalnet">Top Clans by Total Networth</a></td></tr>
</table>
<form method="post" action="<?=$config[main]?>?action=clanmanage">
<div>
<?relations();?>
<?if (($uclan[founder] == $users[num]) || ($uclan[asst] == $users[num])) playeropts();?>
<?if (($uclan[founder] == $users[num]) || ($uclan[asst] == $users[num])) clanids();?>
<?motd();?>
</div>
</form>
<?
TheEnd("");
?>
