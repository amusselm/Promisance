<?
include("header.php");
if ($do_search)
{
	$query = "";
	if (!$searchlimit)
	{
		$searchlimit = 25;
	}
	if (!$order_by)
	{
		$order_by = "rank";
	}

	if ($search_type == "string")
	{
		SQLquotes($search_string);
	        $query .= " empire LIKE '%$search_string%'";
	}
	elseif ($search_type == "num")
	{
		if ($search_num)
		        $query .= " num=$search_num";
		else	TheEnd("No empire number specified!");
	}
	elseif ($search_type == "clan")
	        $query .= " clan=$search_clan";
	elseif ($search_type == "online")
		$query .= " online=1";
	else	TheEnd("No search type specified!");
	if (($search_era) && ($search_era != -1))
		$query .= " and era=$search_era";
	if (($search_max_nw) && ($search_nw_max))
	{
		fixInputNum($search_max_nw);
		$query .= " and networth<=$search_max_nw";
	}
	if (($search_min_nw) && ($search_nw_min))
	{
		fixInputNum($search_min_nw);
		$query .= " and networth>=$search_min_nw";
	}
	if (($search_race) && ($search_race != -1))
	{
		fixInputNum($search_min_nw);
		$query .= " and race=$search_race";
	}
	if ($search_dead)
	{
		$query .= " and ip!='0.0.0.0'";
	}
	$dbstr = mysql_query("SELECT rank,empire,num,land,networth,clan,race,era,online,disabled,turnsused,vacation,offsucc,offtotal,defsucc,deftotal,kills FROM $playerdb WHERE $query ORDER BY $order_by LIMIT $searchlimit;");
	if ($numrows = mysql_num_rows($dbstr))
	{
?>
Color Key: <span class="mprotected">Protected/Vacation</span>, <span class="mdead">Dead</span>, <span class="mally">Ally</span>, <span class="mdisabled">Disabled</span>, <span class="madmin">Administrator</span>, <span class="mself">You</span><br>
Stats Key: O = Offensive Actions (success%), D = Defenses (success%), K = Number of empires killed<br>
<table class="scorestable">
<?
		printSearchHeader($users[era]);
		while ($enemy = mysql_fetch_array($dbstr))
			printSearchLine();
		printSearchHeader($users[era]);
?>
</table>
<?
		if ($numrows > $searchlimit)
			print "Search limit reached.<br>\n";
		else	print "Found $numrows empires matching your criteria.<br>\n";
	}
	else	print "No empires found.<br>\n";
}
?>
<form method="post" action="<?=$config[main]?>?action=search">
<table class="inputtable">
<tr><td>
    <table class="inputtable">
    <tr><th class="aleft"><label>In Era:</label></th>
        <td><select name="search_era" size="1">
        <option value="-1">Any</option>
<?
$eralist = mysql_query("SELECT id,name FROM $eradb;");
while ($era = mysql_fetch_array($eralist))
{
?>
        <option value="<?=$era[id]?>"><?=$era[name]?></option>
<?
}
?>
        </select></td></tr>
    <tr><th class="aleft"><label>Race:</label></th>
        <td><select name="search_race" size="1">
            <option value="-1" selected>Any</option>
<?
$races = mysql_query("select id,name from $racedb;");
while ($race = mysql_fetch_array($races))
{
?>
            <option value="<?=$race[id]?>"><?=$race[name]?></option>
<?
}
?>
            </select></td></tr>
    <tr><th class="aleft"><label>Maximum Networth:</label><input type="checkbox" name="search_nw_max" <?if($search_nw_max) print" checked";?>)></th>
        <td>$<input type="text" name="search_max_nw" size="9" value="<?if ($search_max_nw) echo $search_max_nw; else echo commas(10*$users[networth]);?>"></td></tr>
    <tr><th class="aleft"><label>Minimum Networth:</label><input type="checkbox" name="search_nw_min" <?if($search_nw_min) print" checked";?>></th>
        <td>$<input type="text" name="search_min_nw" size="9" value="<?if ($search_min_nw) echo $search_min_nw; else echo commas($users[networth]/10);?>"></td></tr>
    <tr><th class="aleft">Order by:</th>
        <td><input type="radio" name="order_by" value="rank" checked>Networth</td></tr>
    <tr><th></th>
        <td><input type="radio" name="order_by" value="num">Empire Number</td></tr>
    <tr><th></th>
        <td><input type="radio" name="order_by" value="empire">Empire Name</td></tr>
    <tr><th></th>
        <td><input type="radio" name="order_by" value="clan">Clan</td></tr>
    <tr><th>Exclude dead empires:</th>
        <td><input type="checkbox" name="search_dead"></td></tr>
    <tr><th class="aleft"><label>Maximum Results:</label></th>
        <td><input type="text" name="searchlimit" size="4" value="<?if ($searchlimit) echo $searchlimit; else echo 25;?>"></td></tr>
    </table></td>
    <td><table class="inputtable">
        <tr><th class="aleft"><label><input type="radio" name="search_type" value="num"> Empire Number:</label></th>
            <td><input type="text" name="search_num" size="4"></td></tr>
        <tr><th class="aleft"><label><input type="radio" name="search_type" value="string" checked> String Search:</label></th>
            <td><input type="text" name="search_string" size="15"></td></tr>
        <tr><th class="aleft"><label><input type="radio" name="search_type" value="clan"> Clan Tag Search:</label></th>
            <td><select name="search_clan" size="1">
                <option value="0">None - Unallied Empires</option>
<?
$clanlist = mysql_query("SELECT num,name,tag FROM $clandb WHERE members>0 ORDER BY num;");
while ($clan = mysql_fetch_array($clanlist))
{
?>
                <option value="<?=$clan[num]?>"><?=$clan[tag]?> - <?=$clan[name]?></option>
<?
}
?>
            </select></td></tr>
        <tr><th class="aleft"><label><input type="radio" name="search_type" value="online"> Online Search</label></th>
            <td></td></tr>
        </table>
    </td></tr>
<tr><td colspan="2"><input type="submit" name="do_search" value="Search"></td></tr>
</table>
</form>
<?
TheEnd("");
?>
