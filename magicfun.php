<?
include("spells.php");

// Stock functions for spell success/failure, so we don't have to edit them all individually
function SpellSucceed ($msg)
{
?>
You cast the spell and it is <span class="cgood">successful!</span><br>
<?=$msg?><br>
<?
}

function SpellFail ()
{
	global $users, $uera, $wizloss;
?>
You cast the spell and it <span class="cbad">fizzles!</span><br>
<?=$wizloss?> <?=$uera[wizards]?> are killed in an explosion!<br>
<?
	$users[wizards] -= $wizloss;
}

function SpellShielded ()
{
?>
<span class="cwarn">...though the spell seems to have been partially blocked.</span><br>
<?
}

function printMRow ($num)
{
	global $spname, $sptype, $spcost, $uera;
?>
        <option value="<?=$num?>"><?=$spname[$num]?> (<?=commas($spcost[$num])?> <?=$uera[runes]?>) <?if ($sptype[$num] == d) echo "(Self)"?></option>
<?
}

function setspcost()
{	
	global $users, $urace, $sptype, $spcost;
	// this chunk sets the costs of spells
	// array was created so that it's easier to reference
	$manabase = ($users[land] * .1) + 100 + ($users[labs] * .2) * $urace[magic] * calcSizeBonus($users[networth]);
	// sptype is "o" for offense, "d" for defense
	$spcost[1]  = ceil($manabase *  1.00);	$sptype[1] = "o";	// Crystal Ball

	$spcost[2]  = ceil($manabase *  2.50);	$sptype[2] = "o";	// Fireball
	$spcost[3]  = ceil($manabase *  4.90);	$sptype[3] = "d";	// Spell Shield

	$spcost[4]  = ceil($manabase *  7.25);	$sptype[4] = "o";	// Storms
	$spcost[5]  = ceil($manabase *  9.50);	$sptype[5] = "o";	// Lightning Strike
	$spcost[6]  = ceil($manabase * 18.00);	$sptype[6] = "o";	// Wrath of Demons

	$spcost[7]  = ceil($manabase * 17.00);	$sptype[7] = "d";	// Cornocopia
	$spcost[8]  = ceil($manabase * 17.50);	$sptype[8] = "d";	// Tree of Gold

	$spcost[9]  = ceil($manabase * 20.00);	$sptype[9] = "d";	// Open Time Gate
	$spcost[10] = ceil($manabase * 14.50);	$sptype[10] = "d";	// Close Time Gate

	$spcost[11] = ceil($manabase * 22.75);	$sptype[11] = "o";	// Magic User Fight
	$spcost[12] = ceil($manabase * 25.75);	$sptype[12] = "o";	// Embezzlement

	$spcost[13] = ceil($manabase * 47.50);	$sptype[13] = "d";	// Advance
}

function setspnames()
{
	global $spname, $users;
	if ($users[era] == 1)
	{
		$spname[1] = "Crystal Ball";		$spname[2] = "Fireball";	$spname[3] = "Spell Shield";
		$spname[6] = "Wrath of Demons";
		$spname[13] = "Advance to Present";
	}
	elseif ($users[era] == 2)
	{
		$spname[1] = "Mind Observation";	$spname[2] = "Psionic Storm";	$spname[3] = "Psionic Shield";
		$spname[6] = "Rage of Angels";
		$spname[13] = "Advance to Future";
	}
	elseif ($users[era] == 3)
	{
		$spname[1] = "High Orbit Blanket";	$spname[2] = "Energy Storm";	$spname[3] = "Energy Shield";
		$spname[6] = "Nanotech Warriors";
		$spname[13] = "";
	}
	$spname[4] = "Storms";
	$spname[5] = "Lightning Strike";
	$spname[7] = "Cornucopia";
	$spname[8] = "Tree of Gold";
	$spname[9] = "Open Time Gate";
	$spname[10] = "Close Time Gate";
	$spname[11] = "Magic User Fight";
	$spname[12] = "Embezzlement";

	if ($users[turnsused] < 500 * $users[era])
		$spname[13] = "";
}
setspcost();
setspnames();
if ($do_spell)
{
	if ($spell_num == 0)
		TheEnd("You must specify a spell!");
	if ($users[turns] < 2)
		TheEnd("Not enough turns!");
	if ($users[runes] < $spcost["$spell_num"])
		TheEnd("Not enough $uera[runes]!");
	if ($users[health] < 20)
		TheEnd("Due to their waning health, your wizards cannot cast any spells.");

	if ($sptype["$spell_num"] == o)			// offense spell?
	{
		if (!$target)
			TheEnd("You must specify a target!");
		if ($target == $users[num])
			TheEnd("You may not attack yourself!");
		$enemy = loadUser($target);
		if ($enemy[num] != $target)
			TheEnd("No such user!");
		$erace = loadRace($enemy[race]);
		$eera = loadEra($enemy[era]);

		if ($enemy[land] == 0)
			TheEnd("That empire is dead!");
		if (($users[clan] > 0) && ($users[clan] == $enemy[clan]))
			TheEnd("Cannot attack empires in your clan!");
		if ($enemy[disabled] >= 2)
			TheEnd("Cannot use magic on disabled empires!");
		if ($enemy[turnsused] <= $config[protection])
			TheEnd("Cannot use magic on empires under protection!");
		if ($enemy[vacation] > $config[vacationdelay])
			TheEnd("That empire is on vacation!");
		if (($enemy[era] != $users[era]) && ($users[gate] <= $time) && ($enemy[gate] <= $time))
			TheEnd("Need to open a Time Gate first!");

		$uclan = loadClan($users[clan]);
		$warflag = 0;
		$netmult = 10;
		if ($enemy[clan])
		{
			if ($users[clan] == $enemy[clan])
				TheEnd("Cannot attack empires in your clan!");
			if (($uclan[ally1] == $enemy[clan]) || ($uclan[ally2] == $enemy[clan])|| ($uclan[ally3] == $enemy[clan]))
				TheEnd("Your Generals quietly ignore your orders to attack an Ally.");
			if (($uclan[war1] == $enemy[clan]) || ($uclan[war2] == $enemy[clan])|| ($uclan[war3] == $enemy[clan]))
			{
				$warflag = 1;
				$netmult = 30;
			}
		}
		if ($spell_num != 1)
		{
			if ($enemy[networth] > $users[networth] * $netmult)
				TheEnd("Your $uera[wizards] flatly refuse to target such a strong opponent!");
			if ($users[networth] > $enemy[networth] * $netmult)
				TheEnd("Your $uera[wizards] politely refuse your orders to target a defenseless empire!");

			if ($warflag == 0)
			{
				if ($enemy[attacks] > 20)
					TheEnd("Too many recent attacks on that empire. Try again in one hour.");
				$revolt = 1;
				if ($users[networth] > $enemy[networth] * 2.5)
				{				// Shame is less powerful than fear
?><span class="cwarn">Your <?=$uera[wizards]?> are shamed by your attack on such a weak opponent. Many desert!</span><br>
<?					$revolt = 1 - $users[networth] / $enemy[networth] / 125;
				}
				elseif ($enemy[networth] > $users[networth] * 2.5)
				{
?><span class="cwarn">Your <?=$uera[wizards]?> tremble at your attack on such a strong opponent. Many desert!</span><br>
<?					$revolt = 1 - $users[networth] / $enemy[networth] / 100;
				}
				if ($revolt < .9)
					$revolt = .9;
				$users[wizards] = ceil($users[wizards] * $revolt);
			}
			if ($warflag == 0)
				$enemy[attacks]++;
			$users[attacks] -= 2;
			if ($users[attacks] < 0) $users[attacks] = 0;
			$users[health] -= 4;
			$users[offtotal]++;
			$enemy[deftotal]++;
		}
								// for offense, wizards/avgland
		$uratio = $users[wizards] / (($users[land] + $enemy[land]) / 2) * $urace[magic];
		$eratio = $enemy[wizards] / $enemy[land] * 1.05 * $erace[magic];
	}
	if ($users[labs])					// for defense, wizards/towers
		$lratio = $users[wizards] / $users[labs] * $urace[magic];
	else	$lratio = 0;

	// lose 1%-5% of your wizards if spell fails
	$wizloss = mt_rand(ceil($users[wizards] * .01),ceil($users[wizards] * .05 + 1));
	if ($wizloss > $users[wizards])
		$wizloss = $users[wizards];

	if ((1 <= $spell_num) && ($spell_num <= 13))
	{
		if ($enemy[shield] > $time)
			$shmod = 1/3;
		else	$shmod = 1;
		CastSpell($spell_num);
	}
	$users[runes] -= $spcost["$spell_num"];
	saveUserData($users,"attacks offsucc offtotal kills");
	if ($enemy[num])
		saveUserData($enemy,"attacks defsucc deftotal");
	takeTurns(2,magic);
}
?>
