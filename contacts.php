<?
include("header.php");
?>
<table class="menus" style="width:100%">
<tr><th>Tag</th>
    <th>Name</th>
    <th>Leader</th>
    <th>Assistant Leader</th>
    <th>Minister of Foreign Affairs 1</th>
    <th>Minsiter of Foreign Affairs 2</th></tr>
<?
$clanlist = mysql_query("SELECT num,name,tag,founder,fa1,fa2,asst FROM $clandb WHERE members>0 ORDER BY tag;");
while ($clan = mysql_fetch_array($clanlist))
{
	$leader = mysql_fetch_array(mysql_query("SELECT num,empire FROM $playerdb WHERE num=$clan[founder];"));
	if ($clan[fa1])
		$fa1 = mysql_fetch_array(mysql_query("SELECT num,empire FROM $playerdb WHERE num=$clan[fa1];"));
	if ($clan[fa2])
		$fa2 = mysql_fetch_array(mysql_query("SELECT num,empire FROM $playerdb WHERE num=$clan[fa2];"));
	if ($clan[asst])
		$asst = mysql_fetch_array(mysql_query("SELECT num,empire FROM $playerdb WHERE num=$clan[asst];"));
?>
<tr><td><?=$clan[tag]?></td>
    <td><?=$clan[name]?></td>
    <td><?=$leader[empire]?> (#<?=$leader[num]?>)</td>
    <td><?
	if ($clan[asst])
		print "$asst[empire] (#$asst[num])";
	else	print "none";	?></td>
    <td><?
	if ($clan[fa1])
		print "$fa1[empire] (#$fa1[num])";
	else	print "none";	?></td>
    <td><?
	if ($clan[fa2])
		print "$fa2[empire] (#$fa2[num])";
	else	print "none";	?></td></tr>
<?
}
?>
</table>
<?
theEnd("");
?>
