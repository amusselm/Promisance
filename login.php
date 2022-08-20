<?
include("funcs.php");

function EndNow ($reason)
{
	HTMLbegincompact("Login");
	print "$reason<br>\n";
	HTMLendcompact();
	exit;
}

if ($do_login)
{
	if ($login_username == "")
		EndNow("You must enter a username!");
	$users = mysql_fetch_array(mysql_query("SELECT * FROM $playerdb WHERE username='$login_username';"));
	if (!$users[num])
		EndNow("No such user!<!--userwrong-->");
	if ($users[vacation] > 0)
	{
		if ($users[vacation] < $config[minvacation]+$config[vacationdelay])
			EndNow("This account is in vacation mode and cannot be played for another ".($config[minvacation]+$config[vacationdelay] - $users[vacation])." hours.");
		else
		{
			$users[vacation] = 0;
			$users[idle] = $time;			// so they don't get deleted from being idle
			saveUserData($users,"vacation idle");
		}
	}
	$password = md5($login_password);
	if ($users[password] == $password)
	{
		setcookie("usernamecookie","$users[username]",time()+200000);
		setcookie("passwordcookie","$users[password]",time()+200000);
		header("Location: ".$config[sitedir].$config[main]."?action=game");
	}
	else	EndNow("Incorrect password! <!--passwrong-->");
}
else
{
	setcookie("usernamecookie","");
	setcookie("passwordcookie","");
	HTMLbegincompact("Login");
?>
<h2><?=$config[servname]?></h2>
Promisance v<?=$version?><br>
<!-- <img style="background-color:#FFFFFF;width:48px;height:16px" src="<?=$config[main]?>?action=count&amp;digits=3&amp;style=1" alt="[num]"> Players Registered<br> -->
<form method="post" action="<?=$config[main]?>?action=login">
<div>
Username: <input type="text" name="login_username" size="8"><br>
Password: <input type="password" name="login_password" size="8"><br>
<input type="submit" name="do_login" value="Login">
</div>
</form>
<a href="<?=$config[main]?>?action=signup"><b>- Create Empire -</b></a><br>
<a href="<?=$config[main]?>?action=top10"><b>- Top 10 Players -</b></a><br>
<!-- login -->
<?
	HTMLendcompact();
}
?>
