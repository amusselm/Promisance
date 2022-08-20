<?
include("header.php");

if ($users[disabled] != 2)
	TheEnd("You are not an administrator!");

if ($do_modify == 1)
{
	reset($modify);
	while (list(,$modify_num) = each($modify))
	{
		if ($modify_setdisabledmulti)
		{
			$modify_setmulti = 1;
			$modify_setdisabled = 1;
		}
		if ($modify_clrdisabledmulti)
		{
			$modify_clrmulti = 1;
			$modify_clrdisabled = 1;
		}
		if ($modify_setmulti)
		{
			print "$modify_num marked as multi!<BR>\n";
			mysql_query("UPDATE $playerdb SET ismulti=1 WHERE num=$modify_num;");
		}
		if ($modify_clrmulti)
		{
			print "$modify_num no longer marked as multi!<BR>\n";
			mysql_query("UPDATE $playerdb SET ismulti=0 WHERE num=$modify_num;");
		}
		if ($modify_setdisabled)
		{
			print "$modify_num disabled!<BR>\n";
			mysql_query("UPDATE $playerdb SET disabled=3 WHERE num=$modify_num;");
		}
		if ($modify_clrdisabled)
		{
			print "$modify_num no longer disabled!<BR>\n";
			mysql_query("UPDATE $playerdb SET disabled=0,idle=$time WHERE num=$modify_num;");
		}
		if ($modify_admin)
		{
			print "Granting $modify_num administrative privileges!<BR>\n";
			mysql_query("UPDATE $playerdb SET disabled=2 WHERE num=$modify_num;");
		}
		if ($modify_delete)
		{
			print "Deleting $modify_num!<BR>\n";
			mysql_query("UPDATE $playerdb SET land=0,disabled=4 WHERE num=$modify_num;");
			$users[kills]++;
		}
	}
	saveUserData($users,"kills");
}

if (!$sortby)
	$sortby = "ip";
$multis = mysql_query("SELECT num,empire,clan,ip,name,username,email,idle,disabled,turnsused,validated,land,ismulti FROM $playerdb WHERE ip!='0.0.0.0' ORDER BY $sortby, num ASC;");
$ctags = loadClanTags();
?>
<form method="post" action="<?=$config[main]?>?action=useradmin">
<table border=1>
<tr><th class="aright"><a href="<?=$config[main]?>?action=useradmin&amp;sortby=num">Num</a></th>
    <th class="aleft"><a href="<?=$config[main]?>?action=useradmin&amp;sortby=empire">Empire</a></th>
    <th class="acenter"><a href="<?=$config[main]?>?action=useradmin&amp;sortby=clan">Clan</a></th>
    <th class="aright"><a href="<?=$config[main]?>?action=useradmin&amp;sortby=ip">IP</a></th>
    <th class="acenter"><a href="<?=$config[main]?>?action=useradmin&amp;sortby=name">Name</a></th>
    <th class="acenter"><a href="<?=$config[main]?>?action=useradmin&amp;sortby=username">Username</a></th>
    <th class="acenter"><a href="<?=$config[main]?>?action=useradmin&amp;sortby=email">E-mail</a></th>
    <th class="aright"><a href="<?=$config[main]?>?action=useradmin&amp;sortby=idle">Idle</a></th>
    <th class="aright">Status</th>
    <th class="aright">Modify</th></tr>
<?
while ($multi = mysql_fetch_array($multis))
{
	$idle = $time - $multi[idle];
	if ($multi[$sortby] == $lastsort)
		if ($multi[ismulti])
			if ($multi[disabled] == 3)
				print '<tr class="cbad">'."\n";
			else	print '<tr class="cgood">'."\n";
		else	print '<tr class="cwarn">'."\n";
	else	print "<tr>\n";
?>
    <th class="aright"><?=$multi[num]?></th>
    <td class="aleft"><?=$multi[empire]?></td>
    <td class="acenter"><?=$ctags["$multi[clan]"]?></td>
    <td class="aright"><?=$multi[ip]?></td>
    <td class="acenter"><?=$multi[name]?></td>
    <td class="acenter"><?=$multi[username]?></td>
    <td class="acenter"><?=$multi[email]?></td>
    <td class="aright"><?=gmdate("d",$idle)-1?>:<?=gmdate("H:i:s",$idle)?></td>
    <td class="aright"><?
	switch ($multi[disabled])
	{
	case 0:	if ($multi[land] == 0)
			print "Dead (uninformed)";
		elseif ($multi[ismulti])
			print "Multi (legal)";
		elseif ($multi[validated])
			print "Normal";
		elseif ($multi[turnsused] > $config[valturns])
			print "Unvalidated (uninformed)";
		else	print "New account";
		break;
	case 1:	if ($multi[validated] == 0)
			print "Unvalidated (informed)";
		elseif ($multi[land] == 0)
			print "Dead (informed)";
		break;
	case 2:	print "Admin";
		break;
	case 3:	if ($multi[ismulti])
			print "Multi (disabled)";
		else	print "Cheater";
		break;
	case 4:	print "Deleted";
		break;
	}
?></td>
    <td class="aright"><input type="checkbox" name="modify[]" value="<?=$multi[num]?>"<?if ($multi[num] == $users[num]) print " disabled";?>></td></tr>
<?
	
	$lastsort = $multi[$sortby];
}
?>
<tr><th colspan="10" class="aright">
        <input type="hidden" name="do_modify" value="1">
        <input type="hidden" name="sortby" value="<?=$sortby?>">
        Multi: <input type="submit" name="modify_setmulti" value="Set"> / <input type="submit" name="modify_clrmulti" value="Clr"><br>
        Disabled: <input type="submit" name="modify_setdisabled" value="Set"> / <input type="submit" name="modify_clrdisabled" value="Clr"><br>
        Disable Multi: <input type="submit" name="modify_setdisabledmulti" value="Set"> / <input type="submit" name="modify_clrdisabledmulti" value="Clr"><br>
        Delete Account: <input type="submit" name="modify_delete" value="NUKE"><br>
        Make Admin (Clr Disabled to undo): <input type="submit" name="modify_admin" value="ADMIN"></th></tr>
</table>
</form>
<?
HTMLendfull();
?>
