<?
include("header.php");
if ($do_farm)
{
	fixInputNum($farm_turns);
	if ($farm_turns < 0)
		TheEnd("Cannot farm for a negative number of turns!");
	if ($farm_turns > $users[turns])
		TheEnd("You don't have enough turns!");
	$turns = takeTurns($farm_turns,farm);
?>
You gained a total of <?=commas($foodgained)?> <?=$uera[food]?> in <?=$turns?> turns.<hr>
<?
}
?>
<form method="post" action="<?=$config[main]?>?action=farm">
<table class="inputtable">
<tr><td>Spend how many turns farming?</td>
    <td><input type="text" name="farm_turns" size="5" value="0">
        <input type="hidden" name="do_farm" value="1"></td></tr>
<tr><td colspan="2" class="acenter"><input type="submit" value="Farm"></td></tr>
</table>
</form>
<?
TheEnd("");
?>
