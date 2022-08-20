<?
include("header.php");

function sendUnits ($type)
{
	global $users, $uera, $enemy, $eera, $send, $cansend;
	fixInputNum($send[$type]);
	$amount = $send[$type];
	if ($amount == 0)
		return;
	elseif ($amount < 0)
		TheEnd("Cannot send a negative amount of $uera[$type]!");
	elseif ($amount > $users[$type])
		TheEnd("You don't have that many $uera[$type]!");
	elseif ($amount > $cansend[$type])
		TheEnd("Cannot send more than 20% of your $uera[$type]!");
	else
	{
		$users[$type] -= $amount;
		$enemy[$type] += $amount;
	}
}

function printRow ($type)
{
	global $users, $uera, $cansend, $convoy;
?>
<tr><td><?if ($type == cash) echo "Money"; else echo $uera[$type]?></td>
    <td class="aright"><?=commas($users[$type])?></td>
    <td class="aright"><?=commas($cansend[$type])?></td>
    <td class="aright"><input type="text" name="send[<?=$type?>]" size="8" value="<?if ($type == seatrp) echo $convoy; else echo 0;?>"></td></tr>
<?
}

if ($users[turnsused] <= $config[protection])
	TheEnd("Cannot send aid while under protection!");
if ($users[aidcred] == 0)
	TheEnd("Cannot send any more foreign aid!");

$convoy = 2 * floor($users[networth] / 10000);

for ($i = 0; $i < 4; $i++)
	$cansend[$trplst[$i]] = round($users[$trplst[$i]] * .20);
$cansend[cash] = round($users[cash] * .20);
$cansend[runes] = round($users[runes] * .20);
$cansend[food] = round($users[food] * .20);

if ($do_sendaid)
{
	if ($users[turns] < 2)
		TheEnd("Not enough turns!");
	if (!$dest)
		TheEnd("You must specify an empire!");
	if ($dest == $users[num])
		TheEnd("Cannot send aid to yourself!");
	if ($users[seatrp] < $convoy)
		TheEnd("You don't have enough $uera[seatrp]!");
	fixInputNum($send[seatrp]);
	if ($send[seatrp] < $convoy)
		TheEnd("You must send at least $convoy $uera[seatrp]!");

	$enemy = loadUser($dest);
	$eera = loadEra($enemy[era]);

	if ($enemy[num] != $dest)
		TheEnd("No such empire!");
	if (($enemy[era] != $users[era]) && ($enemy[gate] <= $time) && ($users[gate] <= $time))
		TheEnd("Need to open a time portal first!");
	if ($enemy[disabled] >= 2)
		TheEnd("Cannot send aid to disabled empires!");
	if ($enemy[turnsused] <= $config[protection])
		TheEnd("Cannot send aid to someone under protection!");
	if ($enemy[vacation] > $config[vacationdelay])
		TheEnd("Cannot send aid to someone on vacation!");
	if ($enemy[networth] > $users[networth] * 3)
		TheEnd("That empire is far too large to require your aid!");

	$uclan = loadClan($users[clan]);
	if ($enemy[clan])
	{
		if (($uclan[war1] == $enemy[clan]) || ($uclan[war2] == $enemy[clan])|| ($uclan[war3] == $enemy[clan]))
			TheEnd("Your Generals laugh at the idea of sending aid to your enemies.");
		if (($users[clan] == $enemy[clan]) || ($uclan[ally1] == $enemy[clan]) || ($uclan[ally2] == $enemy[clan]) || ($uclan[ally3] == $enemy[clan]))
			$users[aidcred]++;	// unlimited aid to allies
	}

	for ($i = 0; $i < 4; $i++)
		sendUnits($trplst[$i]);
	sendUnits(cash);
	sendUnits(runes);
	sendUnits(food);

	addNews(102,$users,$enemy,$send[seatrp],$send[armtrp],$send[lndtrp],$send[flytrp],$send[cash],$send[runes],$send[food]);
	$users[aidcred]--;
	saveUserDataNet($users,"networth armtrp lndtrp flytrp seatrp cash runes food aidcred");
	saveUserDataNet($enemy,"networth armtrp lndtrp flytrp seatrp cash runes food");
	takeTurns(2,aid);
?>
<?=commas($send[seatrp])?> <?=$uera[seatrp]?> have departed with shipment to <b><?=$enemy[empire]?> (#<?=$enemy[num]?>)</b>
<hr>
<?
	$convoy = 2 * floor($users[networth] / 10000);
	for ($i = 0; $i < 4; $i++)
		$cansend[$trplst[$i]] = round($users[$trplst[$i]] * .20);
	$cansend[cash] = round($users[cash] * .20);
	$cansend[runes] = round($users[runes] * .20);
	$cansend[food] = round($users[food] * .20);
}
?>
Sending aid requires 2 turns and at least <?=$convoy?> <?=$uera[seatrp]?><br>
We can send up to <?=$users[aidcred]?> shipment<?=plural($users[aidcred],"s","")?>.<br>
We can send an additional shipment every hour.<br><br>
<form method="post" action="<?=$config[main]?>?action=aid">
<table class="inputtable">
<tr><td colspan="3" class="aright">Send aid to which empire?</td>
    <td><input type="text" name="dest" size="6"></td></tr>
<tr><th class="aleft">Unit</th>
    <th class="aright">Owned</th>
    <th class="aright">Can Send</th>
    <th class="aright">Send</th></tr>
<?
for ($i = 0; $i < 4; $i++)
	printRow($trplst[$i]);
printRow(cash);
printRow(runes);
printRow(food);
?>
<tr><td colspan="4" class="acenter"><input type="submit" name="do_sendaid" value="Send Assistance"></td></tr>
</table>
</form>
<?
TheEnd("");
?>
