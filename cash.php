<?
include("header.php");
if ($do_cash)
{
	fixInputNum($cash_turns);
	if ($cash_turns < 0)
		TheEnd("Cannot cash for a negative number of turns!");
	if ($cash_turns > $users[turns])
		TheEnd("You don't have enough turns!");
	$turns = takeTurns($cash_turns,cash);
?>
You earned a total of $<?=commas($cashgained)?> in <?=$turns?> turns.<hr>
<?
}
?>
<form method="post" action="<?=$config[main]?>?action=cash">
<table class="inputtable">
<tr><td>Spend how many turns generating cash?</td>
    <td><input type="text" name="cash_turns" size="5" value="0">
        <input type="hidden" name="do_cash" value="1"></td></tr>
<tr><td colspan="2" class="acenter"><input type="submit" value="Get Cash"></td></tr>
</table>
</form>
<?
TheEnd("");
?>
