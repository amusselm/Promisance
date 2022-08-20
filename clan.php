<?
include("header.php");

if ($users[disabled] == 2)	// are they admin?
	TheEnd("Cannot join clans as an admin!");
if ($users[clan])
{
	$uclan = loadClan($users[clan]);
	if ($do_removeself)
	{
		if ($users[allytime] > $time)
			TheEnd("Sorry, we cannot leave our Clan until we've been a part of it for at least 72 hours.");
		if ($uclan[founder] == $users[num])
		{
			$remuser = mysql_query("SELECT num,clan,forces,allytime FROM $playerdb WHERE clan=$uclan[num];");
			while ($enemy = mysql_fetch_array($remuser))
			{
				$enemy[clan] = $enemy[forces] = 0;
				saveUserData($enemy,"clan forces");
				addNews(117,$users,$enemy,0);
			}
			addNews(111,$users,$users,0);
			$uclan[members] = 0;
			saveClanData($uclan,"members");
			TheEnd("All members have been removed from <b>$uclan[name]</b>.  The clan will be deleted shortly.");
		}
		else
		{
			$uclan[members]--;
			$users[clan] = 0;
			addNews(113,$users,loadUser($uclan[founder]),0);
			saveUserData($users,"clan");
			saveClanData($uclan,"members");
			TheEnd("You have been removed from <b>$uclan[name]</b>.");
		}
	}

	if ($do_useforces)
	{
		$users[forces] = 11;
		saveUserData($users,"forces");
		print "You are now using your forces to help fellow clan members.<br>\n";
	}
	if ($do_notuseforces)
	{
		$users[forces] = 10;
		saveUserData($users,"forces");
		print "Your forces will be available for your use in 2 hours.<br>\n";
	}

	if (($uclan[founder] == $users[num]) ||
		($uclan[asst] == $users[num]) ||
		($uclan[fa1] == $users[num]) ||
		($uclan[fa2] == $users[num]))
	{
?>
<form method="post" action="<?=$config[main]?>?action=clanmanage"><input type="submit" value="Clan Management"></form>
<?
	}

	if ($uclan[url])
	{
?><a href="<?=$uclan[url]?>" target="_blank"><?
	}
	if ($uclan[pic])
	{
?><img src="<?=$uclan[pic]?>" style="border:0" alt="$uclan[name]'s Home Page"><?
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
<tr><th class="era<?=$users[era]?>">Clan Info for <i><?=$uclan[tag]?></i></th></tr>
<tr><td class="acenter"><a href="<?=$config[main]?>?action=clanstats&amp;sort_type=avgnet">Top Clans by Average Networth</a></th></tr>
<tr><td class="acenter"><a href="<?=$config[main]?>?action=clanstats&amp;sort_type=members">Top Clans by Membership</a></th></tr>
<tr><td class="acenter"><a href="<?=$config[main]?>?action=clanstats&amp;sort_type=totalnet">Top Clans by Total Networth</a></th></tr>
</table>
<form method="post" action="<?=$config[main]?>?action=clan">
<div>
<h3><?=$uclan[name]?> Relations</h3>
<table class="inputtable">
<tr><th style="width:50%"><span class="cgood">Ally</span><br>Cannot attack<br>Unlimited aid</th>
    <th style="width:50%"><span class="cbad">War</span><br>Unlimited attacks/magic<br>Cannot send aid</th></tr>
<tr><td class="acenter"><?=$ctags["$uclan[ally1]"]?></td>
    <td class="acenter"><?=$ctags["$uclan[war1]"]?></td></tr>
<tr><td class="acenter"><?=$ctags["$uclan[ally2]"]?></td>
    <td class="acenter"><?=$ctags["$uclan[war2]"]?></td></tr>
<tr><td class="acenter"><?=$ctags["$uclan[ally3]"]?></td>
    <td class="acenter"><?=$ctags["$uclan[war3]"]?></td></tr>
</table><br>
<?	if ($users[forces] > 10)
	{ ?>
<table class="inputtable">
<caption><b>Forces Used for Ally Defense</b></caption>
<?	for($i=0;$i < 4; $i++)
	{
?>
<tr><th><?=$uera[$trplst[$i]]?></th>
    <td class="aright"><?=commas(round($users[$trplst[$i]] * 0.10))?></td></tr>
<?
}
?>
</table>
<input type="submit" name="do_notuseforces" value="Don't Use my Forces for Ally Defense"><br>
<?
	}
	else
	{
		if($users[forces] > 0)
		{ ?>
<b>Forces Used for Ally Defense (for <?=($users[forces] * 10)?> minutes longer.)</b>
<table class="inputtable">
<?	for($i=0;$i < 4; $i++)
	{
?>
<tr><th><?=$uera[$trplst[$i]]?></th>
    <td class="aright"><?=commas(round($users[$trplst[$i]] * 0.10))?></td></tr>
<?
}
?>
</table> <?
		}
?>
<input type="submit" name="do_useforces" value="Use my Forces for Ally Defense"><br>
<?
	}
?>
<?=$uclan[name]?> currently has <?=$uclan[members]?> members.<br><br>
<table class="inputtable">
<caption><b>Empire List</b></caption>
<tr><th>Empire</th>
    <th>Networth</th>
    <th>Rank</th>
    <th>Sharing</th></tr>
<?
	$list = mysql_query("SELECT empire,num,forces,rank,networth FROM $playerdb WHERE clan=$uclan[num];");
	while ($listclan = mysql_fetch_array($list))
	{
?>
<tr><td class="acenter"><?=$listclan[empire]?> (#<?=$listclan[num]?>)</td>
    <td class="aright">$<?=commas($listclan[networth])?></td>
    <td class="aright">#<?=$listclan[rank]?></td>
    <td class="acenter"><span class=<?if ($listclan[forces]) print '"cgood">YES'; else print '"cbad">NO';?></span></td></tr>
<?
	}
?>
</table>
<input type="submit" name="do_removeself" value="<?if ($users[num] == $uclan[founder]) print "Disband"; else print "Leave";?> Clan">
</div>
</form>
<?
}
else
{
	if ($do_joinclan)
	{
		$uclan = loadClan($join_num);
		$password = md5($join_pass);
		if ($password == $uclan[password])
		{
			if ($uclan[members] >= (10 + sqleval("SELECT COUNT(*) FROM $playerdb WHERE land!=0;") / 100))
				TheEnd("That clan is currently full. When more players join the game or clan members leave the clan, you may join.");
			$users[clan] = $uclan[num];
			$users[forces] = 0;
			$users[allytime] = $time + 3600*72;
			saveUserData($users,"clan forces allytime");
			$uclan[members]++;
			saveClanData($uclan,"members");
			addNews(112,$users,loadUser($uclan[founder]),0);
			TheEnd("You are now a member of $uclan[name]!");
		}
		else	TheEnd("Incorrect password!");
	}
	if ($do_createclan)
	{
		if ($lockdb)
			TheEnd("Cannot create a clan until the game begins!");
		if (($create_name == "") || ($create_tag == ""))
			TheEnd("You must enter a clan name and tag!");
		if ($create_tag == "None")
			TheEnd("Illegal clan tag!");
		if (sqleval("SELECT COUNT(*) FROM $clandb WHERE tag='$create_tag';"))
			TheEnd("That clan tag has already been used in this game!");

		mysql_query("INSERT INTO $clandb (founder) VALUES ($users[num]);");
		$uclan = loadClan(mysql_insert_id());

		$uclan[name] = trim(HTMLSpecialChars($create_name));
		$uclan[tag] = trim(HTMLSpecialChars($create_tag));
		$uclan[password] = md5($create_pass);
		$uclan[pic] = $create_flag;
		$uclan[url] = $create_url;
		$uclan[motd] = "Welcome to $create_name!";
		saveClanData($uclan,"name tag password pic url motd");
		$users[clan] = $uclan[num];
		$users[allytime] = $time + 3600*72;
		saveUserData($users,"clan allytime");
		addNews(110,$users,$users,0);
		TheEnd("$uclan[name] has been created successfully!");
	}
?>
From this menu, we can join a clan to possibly offer some protection.<br>
<form method="post" action="<?=$config[main]?>?action=clan">
<table class="inputtable">
<tr><th colspan="2">Join a Clan</th></tr>
<tr><td class="aright">Clan:</td>
    <td><select name="join_num" size="1">
<?
$clanlist = mysql_query("SELECT num,name,tag FROM $clandb WHERE members>0 ORDER BY num;");
while ($clan = mysql_fetch_array($clanlist))
{
?>
        <option value="<?=$clan[num]?>"><?=$clan[tag]?> - <?=$clan[name]?></option>
<?
}
?>
    </select></td></tr>
<tr><td class="aright">Password:</td>
    <td><input type="password" name="join_pass" size="8"></td></tr>
<tr><td colspan="2" class="acenter"><input type="submit" name="do_joinclan" value="Join Clan"></td></tr>
</table>
<hr>
<table class="inputtable">
<tr><th colspan="2">Create a Clan</th></tr>
<tr><td class="aright">Clan Tag:</td>
    <td><input type="text" name="create_tag" size="8" maxlength="8"></td></tr>
<tr><td class="aright">Password:</td>
    <td><input type="password" name="create_pass" size="8" maxlength="16"></td></tr>
<tr><td class="aright">Clan Name:</td>
    <td><input type="text" name="create_name" size="16" maxlength="32"></td></tr>
<tr><td class="aright">Flag URL:</td>
    <td><input type="text" name="create_flag" size="25"></td></tr>
<tr><td class="aright">Site URL:</td>
    <td><input type="text" name="create_url" size="25"></td></tr>
<tr><td colspan="2" class="acenter"><input type="submit" name="do_createclan" value="Create Clan"></td></tr>
</table>
</form>
<br>
<a href="<?=$config[main]?>?action=clanstats&amp;sort_type=avgnet">Top Clans by Average Networth</a><br>
<a href="<?=$config[main]?>?action=clanstats&amp;sort_type=totalnet">Top Clans by Total Networth</a><br>
<a href="<?=$config[main]?>?action=clanstats&amp;sort_type=members">Top Clans by Membership</a><br>
<?
}
TheEnd("");
?>
