<?
include("const.php");
if ((stristr($HTTP_REFERER,$SERVER_NAME)) || ($action == "gameranks") || ($action == "top10") || ($action == "login") || ($action == "signup") || ($action == "count") || (($action == "game") && ($HTTP_REFERER)))
{
	if (!$link = @mysql_connect($dbhost,$dbuser,$dbpass))
	{
		include("html.php");
		HTMLbegincompact("Database Error!");
		print "The game database is currently unavailable. Please try again later.\n";
		HTMLendcompact();
		exit;
	}
	mysql_select_db($dbname);
	if ($action == "game")
		$action = "main";
	include("$action.php");
}
else
{
	include("html.php");
	HTMLbegincompact("Error!");
?>
<table>
<tr><th style="color:#00006F;background-color:#FFFF9F">Security Violation</th></tr>
<tr><td>We have determined that you are accessing the game the wrong way, or an error might have occurred.<br>
<?
	if (!$HTTP_REFERER)
		print "You may NOT access in-game pages via bookmarks!<br>\n";
	else	print "You attempted to view this page from $HTTP_REFERER, which is not on $SERVER_NAME.<br>\n";
?>
If this error persists take the following steps in the following order:<br>
1) Return to <?=$config[home]?> and re-login.<br>
2) Upgrade your internet browser.<br>
3) Contact the game administrator at <?=$config[adminemail]?><br>
4) Contact your ISP.<br></td></tr>
</table>
<?
	HTMLendcompact();
}
?>
