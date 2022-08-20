<?
include("header.php");
include("magicfun.php");

?>
<a href="<?=$config[main]?>?action=guide&amp;section=magic&amp;era=<?=$users[era]?>">Promisance Guide: Magic</a><br>
<form method="post" action="<?=$config[main]?>?action=magic">
<table class="inputtable">
<tr><td><select name="spell_num" size="1">
        <option value="0">Select a Spell</option>
<?
for ($i = 1; $i <= 13; $i++)
	if (($sptype[$i] == 'd') && ($spname[$i]))
		printMRow($i);
?>
        </select></td></tr>
<tr><td class="acenter"><input type="submit" name="do_spell" value="Cast Spell"></td></tr>
</table>
</form>
<?
if ($users[shield] > $time)
	print "<i>We currently have a shield against magic which will last for ".round(($users[shield]-$time)/3600,1)." more hours.</i><br>\n";
if ($users[gate] > $time)
	print "<i>We currently have an open time portal which will last for ".round(($users[gate]-$time)/3600,1)." more hours.</i><br>\n";
TheEnd("");
?>
