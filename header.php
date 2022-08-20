<?
include("funcs.php");
randomize();
// load user data
$num = sqleval("SELECT num FROM $playerdb WHERE username='$cookie[usernamecookie]' AND password='$cookie[passwordcookie]';");
if (!$num)
{
	HTMLbegincompact("Error");
?>
You are not logged in.<br><br>
<a href="<?=$config[main]?>?action=login">Click here</a> <!--blah-->to login again.<br>
<?
	HTMLendcompact();
	exit;
}
$users = loadUser($num);
$admin = 0;
$suid = 0;
if ($users[disabled] == 2)
{
	if ($do_updatenet)
	{
		$userlist = mysql_query("SELECT * FROM $playerdb;");
		while ($user = mysql_fetch_array($userlist))
			mysql_query("UPDATE $playerdb SET networth=".getNetworth($user)." WHERE num=$user[num];");
	}
	if ($do_updateranks)
	{
		$i = 1;
		$userlist = mysql_query("SELECT num FROM $playerdb ORDER BY networth DESC;");
		while ($user = mysql_fetch_array($userlist))
			mysql_query("UPDATE $playerdb SET rank=".$i++." WHERE num=$user[num];");
	}
	if ($do_setuser)
	{
		if ($whichuser == 1)
		{
			setcookie("admincookie","");
			$cookie[admincookie] = "";
		}
		else
		{
			setcookie("admincookie","$whichuser",time()+200000);
			$cookie[admincookie] = $whichuser;
		}
	}
	$admin = 1;
	HTMLbeginfull($action);		// gotta start the page early for the admin
?>
<form method="post" action="<?=$config[main]?>">
<div>
<input type="hidden" name="action" value="<?=$action?>">
#<input type="text" name="whichuser" size="4"<?if ($cookie[admincookie]) print " value=\"$cookie[admincookie]\"";?>>
<input type="submit" name="do_setuser" value="Set User"><br>
<input type="submit" name="do_updatenet" value="Update Networths"> <input type="submit" name="do_updateranks" value="Update Rankings">
</div>
</form>
<?
	if ($cookie[admincookie])
	{
		$suid = 1;
		$users = loadUser($cookie[admincookie]);
	}
	else
	{
?>
<a href="<?=$config[main]?>?action=clanadmin">Clan Administration</a><br>
<a href="<?=$config[main]?>?action=useradmin">User Administration</a><br>
<a href="<?=$config[main]?>?action=msgadmin">View Empire Communications</a><br>
<?
	}
	if (!$users[num])
		TheEnd("No such user!");
}
$urace = loadRace($users[race]);
$uera = loadEra($users[era]);
$ctags = loadClanTags();
$rtags = loadRaceTags();
$etags = loadEraTags();

if (!$admin)
	HTMLbeginfull($action);		// start the page here for non-admins
if (($users[vacation] > 0) && ($users[vacation] < $config[minvacation]+$config[vacationdelay]))
	TheEnd("This account is in vacation mode and cannot be played for another ".($config[minvacation]+$config[vacationdelay] - $users[vacation])." hours.");
?>
<span style="font-size:x-large"><?=$config[servname]?></span> - Currently logged in as <span style="font-size:large"><?=$users[empire]?></span> (#<?=$users[num]?>)<br>
<b>Game News:</b> <?=$config[news]?><br>
<a href="<?=$config[main]?>?action=guide&amp;section=<?=$action?>">[Game Guide]</a> - <a href="<?=$config[main]?>?action=<?=$action?>">[Refresh]</a><br>
<?
printStatsBar();
if (($action != "delete") && ($action != "validate") && ($action != "revalidate"))
{
	switch ($users[disabled])
	{
	case 0:	if ($users[land] == 0)
		{
			$users[idle] = $time;
			$users[disabled] = 1;
			if (!$suid)
				saveUserData($users,"idle disabled");
?>
You arrive at your empire, only to find it is in ruins.<br>
A messenger staggers toward you and tells you what happened...<br><br>
<?
			printNews($users);
?>
<a href="<?=$config[main]?>?action=delete">Delete Account</a>
<?
			TheEnd("");
		}
		elseif (($users[turnsused] > $config[valturns]) && ($users[validated] == 0))
		{
			$users[idle] = $time;
			$users[disabled] = 1;
			if (!$suid)
				saveUserData($users,"idle disabled");
?>
It is now necessary to validate your account. If you did not receive your validation email, you can have it resent.<br>
<form method="post" action="<?=$config[main]?>?action=validate">
<table class="inputtable">
<tr><td>Enter Validation Code:</td>
    <td class="aright"><input type="text" size="32" name="valcode"<?if ($suid) print " value=\"$users[valcode]\"";?>></td></tr>
<tr><th colspan="2" class="acenter"><input type="submit" name="do_validate" value="Validate"></th></tr>
</table>
</form>
<a href="<?=$config[main]?>?action=revalidate">Resend Code</a><br>
<a href="<?=$config[main]?>?action=delete">Delete Account</a>
<?
			TheEnd("");
		}
		break;
	case 1:	if ($users[validated] == 0)
		{
?>
You are not validated and cannot continue from here. If you did not receive the validation e-mail, you can have it resent.<br>
<form method="post" action="<?=$config[main]?>?action=validate">
<table class="inputtable">
<tr><td>Enter Validation Code:</td>
    <td class="aright"><input type="text" size="32" name="valcode"<?if ($suid) print " value=\"$users[valcode]\"";?>></td></tr>
<tr><th colspan="2" class="acenter"><input type="submit" name="do_validate" value="Validate"></th></tr>
</table>
</form>
<a href="<?=$config[main]?>?action=revalidate">Resend Code</a><br>
<a href="<?=$config[main]?>?action=delete">Delete Account</a>
<?
			TheEnd("");
		}
		elseif ($users[land] == 0)
		{
			if ($action == "messages")
				break;
?>
Your empire has been destroyed.<br>
There is nothing more to do here except recall the events that led to your destruction...<br><br>
<?
			printNews($users);
?>
<a href="<?=$config[main]?>?action=delete">Delete Account</a>
<?
			TheEnd("");
		}
		break;
	case 2:	break;
	case 3:	if ($users[ismulti])
			print "This account has been disabled due to usage of multiple accounts.<br>\n";
		else	print "This account has been disabled due to cheating.<br>\n";
		TheEnd("Please contact $config[adminemail] to explain your actions and possibly regain control of your account.");
		break;
	case 4:	TheEnd("Your account has been marked for deletion and will be erased shortly. Thanks for playing!");
		break;
	}
	if (!$suid)
	{
		$users[online] = 1;
		$users[IP] = $REMOTE_ADDR;
		saveUserData($users,"online IP");
	}
}
?>
