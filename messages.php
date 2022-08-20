<?
include("header.php");

if ($lockdb)
	TheEnd("Messaging is currently disabled!");

if ($do_message)
{
	if ((!$msg_replyto) && ($users[msgcred] == 0))
		TheEnd("You have run out of credits. Please wait a few minutes.");
	$msg_body = HTMLSpecialChars($msg_body);	// don't allow HTML tags
	SQLquotes($msg_body);
	mysql_query("INSERT INTO $messagedb (time,src,dest,msg) VALUES ($time,$users[num],$msg_dest,'$msg_body');");
	if ($msg_replyto)
	{
		mysql_query("UPDATE $messagedb SET replied=1 WHERE id=$msg_replyto;");
		print "Reply sent to empire #$msg_dest.<hr>\n";
	}
	else
	{
		$users[msgcred]--;
		saveUserData($users,"msgcred");
		print "Message sent to empire #$msg_dest.<hr>\n";
	}
}
$replyid = 0;
if ($do_reply)
{
	$replyid = $msg_id;
?>
<form method="post" action="<?=$config[main]?>?action=messages">
<div>
<input type="hidden" name="msg_replyto" value="<?=$msg_id?>">
<input type="hidden" name="msg_dest" value="<?=$msg_src?>">
<textarea rows="10" cols="60" name="msg_body"></textarea><br>
<input type="submit" name="do_message" value="Send Reply to empire #<?=$msg_src?>">
</div>
</form>
<?
}
if ($do_delete)
{
	mysql_query("UPDATE $messagedb SET deleted=1 WHERE id=$msg_id AND dest=$users[num];");
	print "Message deleted!<hr>\n";
}
if ($do_deleteall)
{
	mysql_query("UPDATE $messagedb SET deleted=1 WHERE dest=$users[num];");
	print "All messages deleted!<hr>\n";
}

$msgs = mysql_query("SELECT * FROM $messagedb WHERE dest=$users[num] AND deleted=0 ORDER BY time ASC;");
if (mysql_num_rows($msgs))
{
	while ($message = mysql_fetch_array($msgs))
	{
		if (($replyid == 0) || ($replyid == $message[id]))
		{
			$enemy = loadUser($message[src]);
?>
<form method="post" action="<?=$config[main]?>?action=messages">
<div>
<input type="hidden" name="msg_id" value="<?=$message[id]?>">
<input type="hidden" name="msg_src" value="<?=$message[src]?>">
<table style="width:100%">
<tr><td>At <b><?=date("r",$message[time])?></b>, a messenger from <b><?=$enemy[empire]?> (#<?=$enemy[num]?>)</b> brought this to you...</td></tr>
<tr><td><tt><?=str_replace("\n","<br>",$message[msg])?></tt></td></tr>
<?
			if ($replynum == 0)
			{
?><tr><td><?
				if ($message[replied] == 0)
				{
?><input type="submit" name="do_reply" value="Reply"><?
				}
?><input type="submit" name="do_delete" value="Delete"></td></tr>
<?			}
?>
</table>
</div>
</form>
<?
		}
	}
?>
<form method="post" action="<?=$config[main]?>?action=messages">
<div>
<input type="submit" name="do_deleteall" value="Delete all messages">
</div>
</form>
<?
}
else	print "No new messages...<hr>\n";
$users[msgtime] = $time;
saveUserData($users,"msgtime");
?>
We currently have <?=$users[msgcred]?> message credits remaining.<br><br>
<form method="post" action="<?=$config[main]?>?action=messages">
<div>
Send a message to <input type="text" name="msg_dest" size="4"> (country number, no # sign)<br>
<textarea rows="15" cols="60" name="msg_body"></textarea><br>
<input type="submit" name="do_message" value="Send Message">
</div>
</form>
<?
TheEnd("");
?>
