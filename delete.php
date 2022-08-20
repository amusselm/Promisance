<?
include("header.php");

if ($do_delete)
{
	if ($delete_name == $users[name])
	{
		mysql_query("UPDATE $playerdb SET land=0,disabled=4 WHERE num=$users[num];");
?>
Your account has been marked for deletion. Thanks for playing!<br>
<a href="<?=$config[home]?>">Home</a>
<?
		TheEnd("");
	}
	else	// name incorrect
		TheEnd("Name incorrect.  Account remains active.");
}
?>
<form method="post" action="<?=$config[main]?>?action=delete">
<div>
In order to remove your account, you need to provide your real name as you entered it when you signed up.<br>
Real Name: <input type="text" name="delete_name" size="20"><br>
<input type="submit" name="do_delete" value="Delete Account">
</div>
</form>
<?
TheEnd("");
?>
