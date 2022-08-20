<?
function SpellSpy ()
{
	global $users, $uratio, $enemy, $erace, $eera, $eratio, $wizloss;
	if ($uratio > $eratio)
	{
		SpellSucceed("You have learned the following information:");
		printMainStats($enemy,$erace,$eera);
		addNews(201,$users,$enemy,1);
	}
	else
	{
		SpellFail();
		addNews(201,$users,$enemy,-$wizloss);
	}
}

function SpellBlast ()
{
	global $users, $uratio, $enemy, $eratio, $shmod, $time, $wizloss;
	if ($uratio > ($eratio * 1.15))
	{
		SpellSucceed("You have eliminated a portion of your opponent's forces!");
		$dam = 1 - ($shmod * .03);
		$enemy[armtrp] = round($enemy[armtrp] * $dam);
		$enemy[lndtrp] = round($enemy[lndtrp] * $dam);
		$enemy[flytrp] = round($enemy[flytrp] * $dam);
		$enemy[seatrp] = round($enemy[seatrp] * $dam);
		$enemy[wizards] = round($enemy[wizards] * $dam);
		if ($enemy[shield] > $time)
		{
			SpellShielded();
			addNews(202,$users,$enemy,0);
		}
		else	addNews(202,$users,$enemy,1);
		$users[offsucc]++;
	}
	else
	{
		SpellFail();
		addNews(202,$users,$enemy,-$wizloss);
		$enemy[defsucc]++;
	}
	saveUserDataNet($enemy,"networth armtrp lndtrp flytrp seatrp wizards");
}

function SpellShield ()
{
	global $users, $lratio, $time;
	if ($lratio >= 15)
	{
		if ($users[shield] > $time)
		{
			if ($users[shield] < $time + 3600*3)
			{
				SpellSucceed("Your shield has been renewed to 12 hours!");
				$users[shield] = $time + 3600*12;
			}
			else
			{
				SpellSucceed("You have extended your shield for another 3 hours!");
				$users[shield] += 3600*3;
			}
		}
		else
		{
			SpellSucceed("There is now a protective shield around your empire for 12 hours!");
			$users[shield] = $time + 3600*12;
		}
		saveUserData($users,"shield");
	}
	else	SpellFail();
}

function SpellStorm ()
{
	global $users, $uratio, $enemy, $eera, $eratio, $shmod, $time, $wizloss;
	if ($uratio > ($eratio * 1.21))
	{
		$foodloss = round($enemy[food] * .0912 * $shmod);
		$cashloss = round($enemy[cash] * .1265 * $shmod);
		$enemy[food] -= $foodloss;
		$enemy[cash] -= $cashloss;
		SpellSucceed("Your storms have blown away ".commas($foodloss)." $eera[food] and $".commas($cashloss)."!");
		if ($enemy[shield] > $time)
		{
			SpellShielded();
			addNews(204,$users,$enemy,0,$cashloss,$foodloss);
		}
		else	addNews(204,$users,$enemy,1,$cashloss,$foodloss);
		$users[offsucc]++;
	}
	else
	{
		SpellFail();
		addNews(204,$users,$enemy,-$wizloss);
		$enemy[defsucc]++;
	}
	saveUserDataNet($enemy,"networth food cash");
}

function SpellRunes ()
{
	global $users, $uratio, $enemy, $eera, $eratio, $shmod, $time, $wizloss;
	if ($uratio > ($eratio * 1.3))
	{
		$runeloss = round($enemy[runes] * .03 * $shmod);
		$enemy[runes] -= $runeloss;
		SpellSucceed("You have destroyed ".commas($runeloss)." of your enemy's $eera[runes]!");
		if ($enemy[shield] > $time)
		{
			SpellShielded();
			addNews(205,$users,$enemy,0,$runeloss);
		}
		else	addNews(205,$users,$enemy,1,$runeloss);
		$users[offsucc]++;
	}
	else
	{
		SpellFail();
		addNews(205,$users,$enemy,-$wizloss);
		$enemy[defsucc]++;
	}
	saveUserData($enemy,"runes");
}

function SpellStruct ()
{
	global $users, $uratio, $enemy, $eratio, $shmod, $time, $wizloss;
	if ($uratio > ($eratio * 1.7))
	{
		$shmod *= 0.03;
		SpellSucceed("Your spell has destroyed part of your enemy's empire!");
		if ($enemy[shops] >= 15 * $shmod) {	$destroyed += ceil($enemy[shops] * $shmod);	$enemy[shops] -= ceil($enemy[shops] * $shmod); }
		if ($enemy[homes] >= 15 * $shmod) {	$destroyed += ceil($enemy[homes] * $shmod);	$enemy[homes] -= ceil($enemy[homes] * $shmod); }
		if ($enemy[industry] >= 15 * $shmod) {	$destroyed += ceil($enemy[industry] * $shmod);	$enemy[industry] -= ceil($enemy[industry]* $shmod); }
		if ($enemy[barracks] >= 15 * $shmod) {	$destroyed += ceil($enemy[barracks] * $shmod);	$enemy[barracks] -= ceil($enemy[barracks] * $shmod); }
		if ($enemy[farms] >= 15 * $shmod) {	$destroyed += ceil($enemy[farms] * $shmod);	$enemy[farms] -= ceil($enemy[farms] * $shmod); }
		if ($enemy[labs] >= 15 * $shmod) {	$destroyed += ceil($enemy[labs] * $shmod);	$enemy[labs] -= ceil($enemy[labs] * $shmod); }
		if ($enemy[towers] >= 10 * $shmod) {	$destroyed += ceil($enemy[towers] * $shmod);	$enemy[towers] -= ceil($enemy[towers] * $shmod); }
		$enemy[freeland] += $destroyed;
		if ($enemy[shield] > $time)
		{
			SpellShielded();
			addNews(206,$users,$enemy,0);
		}
		else	addNews(206,$users,$enemy,1);
		$users[offsucc]++;
	}
	else
	{
		SpellFail();
		addNews(206,$users,$enemy,-$wizloss);
		$enemy[defsucc]++;
	}
	saveUserDataNet($enemy,"networth shops homes industry barracks farms labs towers freeland");
}

function cashandfood()
{
	global $users, $urace, $lratio;
	return $users[wizards] * $users[health]/100 * 65 * (1 + $users[labs] / $users[land]) * $urace[magic] / (calcSizeBonus($users[networth]) * calcSizeBonus($users[networth]));
}

function SpellFood ()
{
	global $users, $urace, $lratio, $config, $uera;
	if ($lratio >= 30)
	{
		$food = cashandfood()/$config[food];
		SpellSucceed(commas($food)." $uera[food] shows up in your empire's stockpiles!");
		$users[food] += $food;
	}
	else	SpellFail();
}

function SpellGold ()
{
	global $users, $urace, $lratio;
	if ($lratio >= 30)
	{
		$money = cashandfood();
		SpellSucceed("$".commas($money)." shows up in your empire's treasury!");
		$users[cash] += $money;
	}
	else	SpellFail();
}

function SpellGate ()
{
	global $users, $lratio, $time;
	if ($lratio >= 75)
	{
		if ($users[gate] > $time)
		{
			if ($users[gate] < $time + 3600*3)
			{
				SpellSucceed("Your time gate has been renewed to 12 hours!");
				$users[gate] = $time + 3600*12;
			}
			else
			{
				SpellSucceed("You have extended your time gate for another 3 hours!");
				$users[gate] += 3600*3;
			}
		}
		else
		{
			SpellSucceed("You have opened a time gate.  You can now attack anyone for 12 hours!");
			$users[gate] = $time + 3600*12;
		}
		saveUserData($users,"gate");
	}
	else	SpellFail();
}

function SpellUngate ()
{
	global $users, $lratio, $time;
	if ($lratio >= 80)
	{
		SpellSucceed("You have closed your open gate!");
		$users[gate] = $time;
		saveUserData($users,"gate");
	}
	else	SpellFail();
}

function wizDestroyBuildings ($type, $pcloss)
{
	global $buildloss, $enemy, $users;
	$pcloss /= 100;
	$loss = 0;
	if ($enemy[$type] > 0)
		$loss = mt_rand(1,ceil($enemy[$type] * $pcloss + 2));
	if ($loss > $enemy[$type])
		$loss = $enemy[$type];

	$enemy[$type] -= $loss;
	$buildloss += $loss;
}

function SpellFight ()
{
	global $users, $uera, $uratio, $enemy, $eera, $eratio, $lratio, $buildloss, $wizloss;
	if ($lratio >= 50)
	{
		SpellSucceed("Your $uera[wizards] battle it out with $enemy[empire]'s...");
		if ($uratio > $eratio * 2.2)
		{
			print "...and you are successful in defeating your opponent's $eera[wizards]!<br>\n";
			$uloss = mt_rand(0,round($users[wizards] * 0.09 + 1));
			$eloss = mt_rand(0,round($enemy[wizards] * 0.06 + 1));
			if ($uloss > $users[wizards])	$uloss = $users[wizards];
			if ($eloss > $enemy[wizards])	$eloss = $enemy[wizards];
			$buildloss = 0;
			wizDestroyBuildings(homes,7);
			wizDestroyBuildings(shops,7);
			wizDestroyBuildings(industry,7);
			wizDestroyBuildings(barracks,7);
			wizDestroyBuildings(labs,7);
			wizDestroyBuildings(farms,7);
			wizDestroyBuildings(towers,7);
			wizDestroyBuildings(freeland,10);
			$users[land] += $buildloss;
			$users[freeland] += $buildloss;
			$enemy[land] -= $buildloss;
			print "Your $uera[wizards] penetrated $enemy[empire]'s defense and captured $buildloss acres of land!.<br>\n";
			print "You also killed $eloss $eera[wizards], losing $uloss of your $uera[wizards] in the process!<br>\n";
			if ($enemy[land] == 0)
			{
				print '<span class="cgood">'."<b>$enemy[empire] (#$enemy[num])</b> has been destroyed!</span><br>\n";
				$users[kills]++;
				addNews(301,$users,$enemy,0);
			}
			addNews(211,$users,$enemy,$buildloss,$eloss,$uloss);
			$users[offsucc]++;
		}
		else
		{
			print "...and you fail to succeed against your enemy's $eera[wizards]!<br>\n";
			$uloss = mt_rand(0,round($users[wizards] * 0.10 + 1));
			$eloss = mt_rand(0,round($enemy[wizards] * 0.05 + 1));
			if ($uloss > $users[wizards])	$uloss = $users[wizards];
			if ($eloss > $enemy[wizards])	$eloss = $enemy[wizards];
			print "You lose $uloss $uera[wizards], but you manage to kill $eloss of your enemy's $eera[wizards]!<br>\n";
			addNews(211,$users,$enemy,0,$eloss,$uloss);
			$enemy[defsucc]++;
		}
		$users[wizards] -= $uloss;
		$enemy[wizards] -= $eloss;
	}
	else
	{
		SpellFail();
		addNews(211,$users,$enemy,-$wizloss);
	}
	saveUserDataNet($enemy,"networth homes shops industry barracks labs farms towers freeland land wizards");
	saveUserData($users,"freeland land");	// wizards get saved in takeTurns()
}

function SpellSteal ()
{
	global $users, $uratio, $enemy, $eratio, $shmod, $time, $wizloss;
	if ($uratio > ($eratio * 1.75))
	{
		$money = round($enemy[cash]/100000 * mt_rand(ceil(10000 * $shmod), ceil(15000 * $shmod)));
		$users[cash] += $money;
		$enemy[cash] -= $money;
		SpellSucceed("You have embezzled $".commas($money)." from your enemy's treasury!");
		if ($enemy[shield] > $time)
		{
			SpellShielded();
			addNews(212,$users,$enemy,0,$money);
		}
		else	addNews(212,$users,$enemy,1,$money);
		$users[offsucc]++;
	}
	else
	{
		SpellFail();
		addNews(212,$users,$enemy,-$wizloss);
		$enemy[defsucc]++;
	}
	saveUserDataNet($enemy,"networth cash");
}

function SpellAdvance ()
{
	global $users, $uera, $lratio, $spname;
	if (!$spname[12])
		TheEnd("You cannot advance any further!");
	if ($lratio >= 90)
	{
		SpellSucceed("You have advanced to the next age!");
		$users[era]++;
		saveUserData($users,"era");
		$uera = loadEra($users[era]);	// update era-specific stuff immediately
		setspnames();
	}
	else	SpellFail();
}

function CastSpell ($num)
{
	switch ($num)
	{
	case 1:	SpellSpy();	break;
	case 2:	SpellBlast();	break;
	case 3:	SpellShield();	break;
	case 4:	SpellStorm();	break;
	case 5:	SpellRunes();	break;
	case 6:	SpellStruct();	break;
	case 7:	SpellFood();	break;
	case 8:	SpellGold();	break;
	case 9:	SpellGate();	break;
	case 10:SpellUngate();	break;
	case 11:SpellFight();	break;
	case 12:SpellSteal();	break;
	case 13:SpellAdvance();	break;
	}
}
?>
