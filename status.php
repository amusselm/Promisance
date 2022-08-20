<?
include("header.php");

$offpts = ($users[armtrp] * $uera[o_armtrp]) + ($users[lndtrp] * $uera[o_lndtrp]) + ($users[flytrp] * $uera[o_flytrp]) + ($users[seatrp] * $uera[o_seatrp]);
$defpts = ($users[armtrp] * $uera[d_armtrp]) + ($users[lndtrp] * $uera[d_lndtrp]) + ($users[flytrp] * $uera[d_flytrp]) + ($users[seatrp] * $uera[d_seatrp]);

$offpts = round($offpts * $urace[offense]);
$defpts = round(($defpts + ($users[towers] * 500)) * $urace[defense]);
$size = calcSizeBonus($users[networth]);

$foodpro = round((5 * $users[freeland]) + ($users[farms] * 75) * $urace[farms]);
$foodcon = round((($users[armtrp] * .05) + ($users[lndtrp] * .03) + ($users[flytrp] * .02) + ($users[seatrp] * .01) + ($users[peasants] * .01) + ($users[wizards] * .25)) * $urace[food]);
$foodnet = $foodpro - $foodcon;

if ($users[clan])
	$uclan = loadClan($users[clan]);
else	$uclan[ally1] = $uclan[ally2] = $uclan[ally3] = $uclan[war1] = $uclan[war2] = $uclan[war3] = 0;

$income = round(((pci($users,$urace) * ($users[tax] / 100) * ($users[health] / 100) * $users[peasants]) + ($users[shops] * 500)) / $size);
$loanpayment = round($users[loan] / 200);
$expenses = round(($users[armtrp] * 1) + ($users[lndtrp] * 2.5) + ($users[flytrp] * 4) + ($users[seatrp] * 7) + ($users[land] * 8));
$expbonus = round($expenses * ($urace[costs] - ($users[barracks] / $users[land])));
if ($expbonus > $expenses / 2)				// expenses bonus limit
	$expbonus = round($expenses / 2);
$expenses -= $expbonus;
$netincome = $income - $expenses;

$savrate = $config[savebase] - $size;
$loanrate = $config[loanbase] + $size;
?>
<table style="width:100%">
<tr><th colspan="3" class="era<?=$users[era]?>"><?=$users[empire]?> (#<?=$users[num]?>)</th></tr>
<tr><td style="vertical-align:top;width:33%">
        <table class="empstatus" style="width:100%">
        <tr><th colspan="2" class="era<?=$users[era]?>" style="text-align:center">Statistics</th></tr>
        <tr><th>Turns Used:</th>
            <td><?=commas($users[turnsused])?></td></tr>
        <tr><th>Money:</th>
            <td>$<?=commas($users[cash])?></td></tr>
        <tr><th>Rank:</th>
            <td>#<?=$users[rank]?></td></tr>
        <tr><th>Networth:</th>
            <td>$<?=commas($users[networth])?></td></tr>
        <tr><th>Population:</th>
            <td><?=commas($users[peasants])?></td></tr>
        <tr><th>Race:</th>
            <td><?=$urace[name]?></td></tr>
        <tr><th>Era:</th>
            <td><?=$uera[name]?></td></tr>
        </table>
    </td>
    <td style="vertical-align:top;width:33%">
        <table class="empstatus" style="width:100%">
        <tr><th colspan="2" class="era<?=$users[era]?>" style="text-align:center">Agriculture</th></tr>
        <tr><th>Est. Production:</th>
            <td><?=commas($foodpro)?></td></tr>
        <tr><th>Est. Consumption:</th>
            <td><?=commas($foodcon)?></td></tr>
        <tr><th>Net:</th>
            <td><?printCNum($foodnet,"",0);?></td></tr>
        </table>
    </td>
    <td style="vertical-align:top;width:33%">
        <table class="empstatus" style="width:100%">
        <tr><th colspan="2" class="era<?=$users[era]?>" style="text-align:center">Relations</th></tr>
        <tr><th>Member of Clan:</th>
            <td><?=$ctags["$users[clan]"]?></td></tr>
        <tr><th>Allies:</th>
            <td><?=$ctags["$uclan[ally1]"]?>,<?=$ctags["$uclan[ally2]"]?>,<?=$ctags["$uclan[ally3]"]?></td></tr>
        <tr><th>War:</th>
            <td><?=$ctags["$uclan[war1]"]?>,<?=$ctags["$uclan[war2]"]?>,<?=$ctags["$uclan[war3]"]?></td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><th>Offensive Actions:</th>
            <td><?=$users[offtotal]?> (<?if ($users[offtotal]) echo round($users[offsucc]/$users[offtotal]*100); else echo 0;?>%)</td></tr>
        <tr><th>Defenses:</th>
            <td><?=$users[deftotal]?> (<?if ($users[deftotal]) echo round($users[defsucc]/$users[deftotal]*100); else echo 0;?>%)</td></tr>
        <tr><th>Kills:</th>
            <td><?=$users[kills]?></td></tr>
        </table>
    </td>
</tr>
<tr><td style="vertical-align:top;width:33%">
        <table class="empstatus" style="width:100%">
        <tr><th colspan="2" class="era<?=$users[era]?>" style="text-align:center">Land Division</th></tr>
        <tr><th><?=$uera[shops]?>:</th>
            <td><?=commas($users[shops])?></td></tr>
        <tr><th><?=$uera[homes]?>:</th>
            <td><?=commas($users[homes])?></td></tr>
        <tr><th><?=$uera[industry]?>:</th>
            <td><?=commas($users[industry])?></td></tr>
        <tr><th><?=$uera[barracks]?>:</th>
            <td><?=commas($users[barracks])?></td></tr>
        <tr><th><?=$uera[labs]?>:</th>
            <td><?=commas($users[labs])?></td></tr>
        <tr><th><?=$uera[farms]?>:</th>
            <td><?=commas($users[farms])?></td></tr>
        <tr><th><?=$uera[towers]?>:</th>
            <td><?=commas($users[towers])?></td></tr>
        <tr><th>Unused Land:</th>
            <td><?=commas($users[freeland])?></td></tr>
        </table>
    </td>
    <td style="vertical-align:top;width:33%">
        <table class="empstatus" style="width:100%">
        <tr><th colspan="2" class="era<?=$users[era]?>" style="text-align:center">Finances</th></tr>
        <tr><th>Est. Income:</th>
            <td>$<?=commas($income)?></td></tr>
        <tr><th>Est. Expenses:</th>
            <td>$<?=commas($expenses)?></td></tr>
        <tr><th>Net:</th>
            <td><?printCNum($netincome,"$",0);?></td></tr>
        <tr><th>Loan Payment:</th>
            <td>$<?=commas($loanpayment)?></td></tr>
        <tr><th>Per Cap income:</th>
            <td>$<?=pci($users,$urace)?></td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><th>Savings Balance:</th>
            <td>$<?=commas($users[savings])?> (<?=$savrate?>%)</td></tr>
        <tr><th>Loan Balance:</th>
            <td>$<?=commas($users[loan])?> (<?=$loanrate?>%)</td></tr>
        </table>
    </td>
    <td style="vertical-align:top;width:33%">
        <table class="empstatus" style="width:100%">
        <tr><th colspan="2" class="era<?=$users[era]?>" style="text-align:center">Military</th></tr>
        <tr><th><?=$uera[armtrp]?>:</th>
            <td><?=commas($users[armtrp])?></td></tr>
        <tr><th><?=$uera[lndtrp]?>:</th>
            <td><?=commas($users[lndtrp])?></td></tr>
        <tr><th><?=$uera[flytrp]?>:</th>
            <td><?=commas($users[flytrp])?></td></tr>
        <tr><th><?=$uera[seatrp]?>:</th>
            <td><?=commas($users[seatrp])?></td></tr>
        <tr><th><?=$uera[wizards]?>:</th>
            <td><?=commas($users[wizards])?></td>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><th>Offensive Points:</th>
            <td><?=commas($offpts)?></td></tr>
        <tr><th>Defensive Points:</th>
            <td><?=commas($defpts)?></td></tr>
        </table>
    </td>
</tr>
</table>
<?
TheEnd("");
?>
