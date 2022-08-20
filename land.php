<?
include("header.php");
if ($do_explore)
{
	fixInputNum($explore_turns);
	if ($explore_turns < 0)
		TheEnd("Cannot explore for a negative number of turns!");
	if ($explore_turns > $users[turns])
		TheEnd("You don't have enough turns!");
	$turns = takeTurns($explore_turns,land);
	print "You gained ".commas($landgained)." acres of land in $turns turns.<HR>\n";
}
?>
For each turn you spend exploring, you can get about <b><?=gimmeLand($users[land],$urace[expl],$users[era])?></b> acres of land.<br>
<form method="post" action="<?=$config[main]?>?action=land">
<table class="inputtable">
<tr><td>Spend how many turns exploring?</td>
    <td><input type="text" name="explore_turns" size="5" value="0">
        <input type="hidden" name="do_explore" value="1"></td></tr>
<tr><td colspan="2" class="acenter"><input type="submit" value="Explore"></td></tr>
</table>
</form>
<?
TheEnd("");
?>
