<?
include("header.php");

function structures()
{
	global $uera;
?>
<table class="inputtable">
<tr><th colspan="2"><?=$uera[name]?> Building Descriptions</th></tr>
<tr><th><?=$uera[shops]?></th>
    <td><?=$uera[shops]?> allow your empire's economy to grow.  These buildings will help increase your Per Capita Income, as well as producing an amount of cash themselves.</td></tr>
<tr><th><?=$uera[homes]?></th>
    <td>While <?=$uera[peasants]?> will live on unused land, <?=$uera[homes]?> are specifically designed for housing. As a result, they allow you to house a great deal more <?=$uera[peasants]?> than otherwise.</td></tr>
<tr><th><?=$uera[barracks]?></th>
    <td><?=$uera[barracks]?> allow you to reduce your military expenses by more efficiently housing your units. They will also lower the price of all military units purchased from the black market.  Those first two benefits do not increase once you have 30%<?=$uera[barracks]?>.  <?=$uera[barracks]?> also increase the rate at which your black market refills.</td></tr>
<tr><th><?=$uera[industry]?></th>
    <td><?=$uera[industry]?> produce your military units; the percentage of resources allocated to each unit type produced is controlled through empire management.</td></tr>
<tr><th><?=$uera[labs]?></th>
    <td><?=$uera[labs]?> serve to train and house <?=$uera[wizards]?>, as well as produce <?=$uera[runes]?> with which your <?=$uera[wizards]?> may cast their spells.</td></tr>
<tr><th><?=$uera[farms]?></th>
    <td><?=$uera[farms]?> are vital for feeding your <?=$uera[peasants]?> and military; without food, your people will starve and desert your empire.</td></tr>
<tr><th><?=$uera[towers]?></th>
    <td><?=$uera[towers]?> are a strictly defensive building, worth 500 defense points each.</td></tr>
</table>
<?
}

function munits()
{
	global $eradb, $uera, $era, $config, $section;
	$myera = $uera;
	$eras = mysql_query("SELECT id,name FROM $eradb ORDER BY id;");
	while ($eera = mysql_fetch_array($eras))
	{
?>
<a href="<?=$config[main]?>?action=guide&amp;section=<?=$section?>&amp;era=<?=$eera[id]?>"><?=$eera[name]?></a>
<?
	}
	if ($era)
		$uera = loadEra($era);
?>
<table class="inputtable">
<tr><th colspan="5"><?=$uera[name]?> Military Descriptions</th></tr>
<tr><th>Unit</th>
    <th>Description</th>
    <th>Base Cost</th>
    <th>Off.</th>
    <th>Def.</th></tr>
<tr><th><?=$uera[armtrp]?></th>
    <td>The basic military unit. Not the strongest unit, but with a cheaper price tag these can be mobilized in large groups to cause plenty of damage to your enemy.</td>
    <td class="acenter">$<?=$config[armtrp]?></td>
    <td class="acenter"><?=$uera[o_armtrp]?></td>
    <td class="acenter"><?=$uera[d_armtrp]?></td></tr>
<tr><th><?=$uera[lndtrp]?></th>
    <td>A strong <?if ($uera[o_lndtrp] > $uera[d_lndtrp]) echo "offensive"; else echo "defensive";?> unit.  Can be used in attacks to gain land from your enemies.</td>
    <td class="acenter">$<?=$config[lndtrp]?></td>
    <td class="acenter"><?=$uera[o_lndtrp]?></td>
    <td class="acenter"><?=$uera[d_lndtrp]?></td></tr>
<tr><th><?=$uera[flytrp]?></th>
    <td>An aerial attack is sometimes the best way to go; these can also capture land in special attacks and have an edge in <?if ($uera[o_flytrp] > $uera[d_flytrp]) echo "offense"; else echo "defense";?>.
    <td class="acenter">$<?=$config[flytrp]?></td>
    <td class="acenter"><?=$uera[o_flytrp]?></td>
    <td class="acenter"><?=$uera[d_flytrp]?></td></tr>
<tr><th><?=$uera[seatrp]?></th>
    <td>These are used not only for military purposes, but also to ship foreign aid to other empires.  With both strong offensive and defensive capabilities, it is the most expensive unit, but also the most powerful.</td>
    <td class="acenter">$<?=$config[seatrp]?></td>
    <td class="acenter"><?=$uera[o_seatrp]?></td>
    <td class="acenter"><?=$uera[d_seatrp]?></td></tr>
</table>
<?
	$uera = $myera;
}

function military()
{
	global $eradb, $uera, $era;
	munits();
?>
<hr style="width:100%">
<table class="inputtable">
<tr><th>The Attack</th>
    <td>When you attack, the number of offensive points you attack with is compared to your oponents defense.  A 5% advantage is required to succeed.</td></tr>
<tr><th>Calculating Defense:</th>
    <td>First multiply the quantity of each type of defending troop by its defensive value (consult above table for these values).  If troops are shared in a clan, subtract off 10%.</td></tr>
<tr><td></td>
    <td>Then, clan shared forces are added to defense (no more than double your current defense).</td></tr>
<tr><td></td>
    <td>Lastly 500 points of defense are added for each tower.</td></tr>
<tr><th>Calculating Offense:</th>
    <td>Multiply the quantity of each type of troop by their offensive value (above).  Subtract 10% if you are sharing forces in a clan.</td></tr>
<tr><td colspan="2"><hr style="width:100%"></td></tr>
<tr><th>Attack Type</th>
    <th>Description</th></tr>
<tr><th>Standard Attack</th>
    <th>An attack with all four types of troops.  This gains land and some of the buildings on it.  A standard attack drains your health by 8%.</td></tr>
<tr><th>Surprise Attack</th>
    <td>A surprise attack sends all troop types, but grants a 25% offense bonus and doesn't allow clan members to aid in defense of your target.  The health penalty is 18% and you lose 50% more troops, so use it carefully.</td></tr>
<tr><th>Guerilla Strike</th>
    <td>This only uses <?=$uera[armtrp]?> for all calculations and losses for you and your enemy.</td></tr>
<tr><th>Stone Bombardment</th>
    <td>This only uses <?=$uera[lndtrp]?> for all calculations and losses for you and your enemy.</td></tr>
<tr><th>Aerial Assault</th>
    <td>This only uses <?=$uera[flytrp]?> for all calculations and losses for you and your enemy.</td></tr>
<tr><th>Hydro Assault</th>
    <td>This only uses <?=$uera[seatrp]?> for all calculations and losses for you and your enemy.</td></tr>
<tr><td colspan="2"><hr style="width:100%"></td></tr>
<tr><th>Land Gained (Standard, Surprise, and Guerilla)</th>
    <td>0-7% of developled (built on land) and 0-10% of undeveloped land.  Minimum 1 acre.</td></tr>
<tr><th>Land Gained (Aerial)</th>
    <td>This attack destroys up to 8.75% of any type of building, but only gains up to 6.3% of that land.  10% of free land is gained. Minimum 1 acre.</td></tr>
<tr><th>Land Gained (Hydro, Bombardment)</th>
    <td>These attacks destroy up to 9.1% of <?=$uera[towers]?> and <?=$uera[labs]?> (up to 7% for the others), but only gains up to 6.3% of land of all types. 10% of free land is gained. Minimum 1 acre.</td></tr>
</table>
<table class="inputtable" style="width:100%">
<tr><td colspan="3"><hr style="width:100%"></td></tr>
<tr><th></th>
    <th>Enemy losses</th>
    <th>Your losses</th></tr>
<tr><th><?=$uera[armtrp]?></th>
    <td class="acenter">0%-8.05%</td>
    <td class="acenter">0%-14.5%</td></tr>
<tr><th><?=$uera[lndtrp]?></th>
    <td class="acenter">0%-7.3%</td>
    <td class="acenter">0%-12.85%</td></tr>
<tr><th><?=$uera[flytrp]?></th>
    <td class="acenter">0%-6.75%</td>
    <td class="acenter">0%-7.88%</td></tr>
<tr><th><?=$uera[seatrp]?></th>
    <td class="acenter">0%-5.55%</td>
    <td class="acenter">0%-6.5%</td></tr>
<tr><th>Troop loss multiplier</th>
    <th colspan="2">Multipiers to the loss percentages</th></tr>
<tr><th>Your troops</th>
    <td colspan="2">1/((Offensive_Points/(Defensive_Points+1))/1.25)</td></tr>
<tr><th>Enemy</th>
    <td colspan="2">(Offensive_Points/(Defensive_Points+1))/1.5</td></tr>
</table>
<?
}

function status()
{
	global $uera;
?>
<table class="inputtable">
<tr><th>Field</th>
    <th>Description</th></tr>
<tr><th colspan="2">Status Table Definitions: Statistics</th></tr>
<tr><th>Turns Used</th>
    <td>This is the number of turns you have used since your empire was created.</td></tr>
<tr><th>Rank</th>
    <td>Your rank, determined by your networth, compares you to all other players in the game.</td></tr>
<tr><th>Networth</th>
    <td>Your networth is a calculated value based on the military, <?=$uera[wizards]?>, <?=$uera[peasants]?>, <?=$uera[food]?>, cash, and land you have, roughly indicating how much your empire is worth.</td></tr>
<tr><th>Population</th>
    <td>This is the number of <?=$uera[peasants]?> that live in your empire.  <?=$uera[peasants]?> are necessary for making money to finance your empire.</td></tr>
<tr><th colspan="2">Status Table Definitions: Agriculture</th></tr>
<tr><th>Est. Production</th>
    <td><?=$uera[farms]?> and unused land both help to produce <?=$uera[food]?> with which to sustain your empire.  This number indicates approximately how much they will produce each turn.</td></tr>
<tr><th>Est. Consumption</th>
    <td>Your military, <?=$uera[peasants]?>, and <?=$uera[wizards]?> all require <?=$uera[food]?> to survive.  This number shows your estimated consumption per turn.</td></tr>
<tr><th>Net</th>
    <td>This number indicates whether you are gaining or losing <?=$uera[food]?> overall per turn.  It is usually a good idea to keep an eye on this number, lest you run out of <?=$uera[food]?> and your people starve.</td></tr>
<tr><th colspan="2">Status Table Definitions: Relations</th></tr>
<tr><th>Member of Clan:</th>
    <td>If you are in a clan, its 'tag' is indicated here.  If you are independent, this will simply say 'None'.</td></tr>
<tr><th>Allies</th>
    <td>If you are in a clan, other clans which you are allied with will be listed here.</td></tr>
<tr><th>War</th>
    <td>If you are in a clan, clans you are at war with are listed here.</td></tr>
<tr><th>Offenses</th>
    <td>QM Promisance keeps track of how many times you have attacked other empires, as well as the percentage of successful attacks. Your attack count is shown here, with the success rating in parentheses.</td></tr>
<tr><th>Defenses</th>
    <td>Every time your empire is attacked, the Defenses counter is incremented. You can also see a rough percentage of how many attacks you have successfully resisted.</td></tr>
<tr><th>Kills</th>
    <td>This indicates the number of empires you have destroyed.</td></tr>
<tr><th colspan="2">Status Table Definitions: Land Division</th></tr>
<tr><th></th>
    <td>Each row here indicates how many structures of each type you have.</td></tr>
<tr><th colspan="2">Status Table Definitions: Finances</th></tr>
<tr><th>Est. Income</th>
    <td>Your income is determined by the number of <?=$uera[peasants]?> you have, your Per Capita Income, your tax rate, and your health.</td></tr>
<tr><th>Est. Expenses</th>
    <td>Your expenses consist of military upkeep and land taxes.  <?=$uera[barracks]?> help to lower your expenses.</td></tr>
<tr><th>Net</th>
    <td>This indicates your net income, whether you are gaining or losing money overall each turn.  It is highly recommended to keep an eye on this value.</td></tr>
<tr><th>Loan payment</th>
    <td>If you have borrowed any money from the World Bank, 0.5% of your loan is payed off each turn.  Your loan payment for the next turn you take is indicated here.</td></tr>
<tr><th>Per Cap income</th>
    <td>This is your per capita income, indicating how much money each of your <?=$uera[peasants]?> makes each turn.  You gain a percentage of this income based on your tax rate.</td></tr>
<tr><th>Savings Balance</th>
    <td>This indicates how much money you currently have in your savings account.  Your account's interest rate is indicated in parentheses.</td></tr>
<tr><th>Loan Balance</th>
    <td>Here is indicated the amount of money you currently owe to the bank.  The loan's interest rate is shown in parentheses.</td></tr>
<tr><th colspan="2">Status Table Definitions: Military</th></tr>
<tr><th></th>
    <td>The top rows indicate how many of each unit you currently have in your army.</td></tr>
<tr><th>Offense Points</th>
    <td>This number indicates your total calculated offensive power (see <a href="<?=$config[main]?>?action=guide&amp;section=military&amp;era=<?=$users[era]?>">Promisance Guide: Military</a> for more information).</td></tr>
<tr><th>Defense Points</th>
    <td>Your total calculated defensive power (see <a href="<?=$config[main]?>?action=guide&amp;section=military&amp;era=<?=$users[era]?>">Promisance Guide: Military</a>) is shown here.</td></tr>
</table>
<?
}

function scores()
{
	global $perminutes;
?>
<b>Rankings are updated every <?=$perminutes?> minute(s).</b><br>
<table class="inputtable">
<tr><th colspan="2">Scores Page: Colors</th></tr>
<tr><th><span class="mprotected">Protected/Vacation</span></th>
    <td>Empires in this color have either not used enough turns to leave protection, or they have gone on vacation.</td></tr>
<tr><th><span class="mdead">Dead</span></th>
    <td>Empires in this color have been destroyed.</td></tr>
<tr><th><span class="mally">Ally</span></th>
    <td>These empires are in the same clan as you.</td></tr>
<tr><th><span class="mdisabled">Disabled</span></th>
    <td>These empires have been disabled by the administration, either for running multiple accounts or for intentionally exploiting a bug in the game's code.</td></tr>
<tr><th><span class="madmin">Administrators</span></th>
    <td>These empires are responsible for keeping the game under control. They cannot be attacked and are not capable of attacking other empires or joining clans. If you're having problems in the game, these are the people to talk to.</td></tr>
<tr><th><span class="mself">You</span></th>
    <td>To make it easier to locate yourself in the score list, your empire appears in this color.</td></tr>
<tr><th colspan="2">Scores Page: Row Display</th></tr>
<tr><th>Rank</th>
    <td>This is the empire's rank (based on networth), compared to all other empires in the game.</td></tr>
<tr><th>Empire</th>
    <td>Here is listed the empire's name and number.</td></tr>
<tr><th>Land</th>
    <td>This column indicates the total amount of land an empire has.</td></tr>
<tr><th>Networth</th>
    <td>This is the empire's networth, a calculated estimate of the empire's overall value.</td></tr>
<tr><th>Clan</th>
    <td>If the empire is in a clan, its name will be listed here. Otherwise, 'None' will appear.</td></tr>
<tr><th>Race</th>
    <td>This is the race of the empire in question.</td></tr>
<tr><th>Era</th>
    <td>This is the time period the empire is in.</td></tr>
</table>
<?
}

function esearch()
{
?>
<table class="inputtable">
<tr><th colspan="2">Empire Search</th></tr>
<tr><th>In <a href="<?=$config[main]?>?action=guide&amp;section=eras">Era</a></th>
    <td>Limit the search to a certain era.</td></tr>
<tr><th>String Search</th>
    <td>If you want to find empires whose names contain a string, enter it here.  The string entered is case-insensitive.</td></tr>
<tr><th>Clan Tag Search</th>
    <td>To find all empires in a certain clan, use this option.</td></tr>
<tr><th>Era Search</th>
    <td>To find empires in a certain era, use this search.</td></tr>
<tr><th>Online Search</th>
    <td>Use this option to list all empires whose owners are currently online.</td></tr>
<tr><th colspan="2">Only 25 empires will be listed in any search query.</th></tr>
</table>
<?
}

function cash()
{
	global $uera;
?>
<b>Cashing</b><br>
<table class="inputtable">
<tr><td>While cashing, your <?=$uera[peasants]?> focus all of their time on making extra money, increasing your income by 25%.<br>
        Note that this does not affect your expenses, which are subtracted AFTER your income is calculated.</td></tr>
</table>
<?
}

function farm()
{
	global $uera;
?>
<b>Farming</b><br>
<table class="inputtable">
<tr><td>While farming, your <?=$uera[peasants]?> focus all of their time on making extra food, increasing your food production by 25%.</td></tr>
</table>
<?
}

function explore()
{
?>
<b>Exploring</b><br>
For every turn you spend exploring, you can gain a variable amount of land.<br>
This is an especially good way for smaller empires to gain land, as exploring becomes less effective as more land is gained.<br>
Eventually, you will no longer be able to gain any land by exploring, though this does not occur until you have well over 50,000 acres.<br>
<?
}

function news()
{
?>
<b>News Search</b><br>
<table class="inputtable">
<tr><td>Using the news search, you can easily find out about any major battles that have taken place recently.<br>
        You may search for news involving an empire as an attacker, defender, or both.<br>
        Empires can be searched for either by number or by clan.</td></tr>
</table>
<?
}

function bmarket()
{
	global $uera, $config;
?>
<h2>Black Market</h2><br>
<table class="inputtable">
<tr><th>Definition</th>
    <td>The black market is a private market that exists within your empire.</td></tr>
<tr><th>Refill Rate</th>
    <td>The market refills faster with more land and <?=$uera[barracks]?> (or <?=$uera[farms]?>, for buying <?=$uera[food]?>).</td></tr>
<tr><th>Prices</th>
    <td><?=$uera[barracks]?> account for <?=(1-$config[mktshops])*100?>% of the cost reduction. <?=$uera[shops]?> account for the other <?=$config[mktshops]*100?>% of the price reduction.<br>
        Minimum price is 70% the base cost, modified further by any racial bonuses.</td></tr>
<tr><th>Can Sell</th>
    <td>You can sell up to <?=$config[bmperc]/100?>% of any type of troop at once.  (Percentage sold is recorded and decremented by 1% * (1+shops/land) each time the turn script runs).</td></tr>
</table>
<?
}

function pmarket()
{
?>
<b>Public Market</b><br>
The public market allows trade between empires within one game.  Items on display here are being sold by other players.<br>
When you sell goods, the market keeps a 5% commission on all goods, deducted from the money received during sales.<br>
You can never purchase your own goods, though you can recall them from the market for a 20% loss.<br>
The least expensive items will be sold first, with a preference toward items which have been on the market longer.<br>
<?
}

function manage()
{
	global $uera, $config;
?>
<b>Empire Management</b><br>
<table class="inputtable">
<tr><th>Taxes</th>
    <td>This is what percentage of your (population) * (pci) goes to your governemnt (you).</td></tr>
<tr><th></th>
    <td>Immigration is multiplied by (4 / ((Tax_Rate + 15) / 20)) - ( 7 / 9).</td></tr>
<tr><th></th>
    <td>Emigration is multiplied by 1 / ((4 / ((Tax_Rate + 15) / 20)) - ( 7 / 9)).</td></tr>
<tr><th></th>
    <td>For every <b>two percentage points</b> over 10%, your maximum health is lowered 1 percent.</td></tr>
<tr><td colspan="2"><hr style="width:100%"></td></tr>
<tr><th>Polymorph</th>
    <td>This gives you the ability to, as a last resort, change your race.</td></tr>
<tr><th></th>
    <th>Polymorph requires:</th></trd>
<tr><th></th>
    <td class="acenter">3 or more <?=$uera[wizards]?> per acre of land</td></tr>
<tr><th></th>
    <td class="acenter"><?=$config[initturns]?> turns</td></tr>
<tr><th></th>
    <td class="acenter">75%+ health</td></tr>
<tr><th></th>
    <th>It has the following negative effects:</th></tr>
<tr><th></th>
    <td class="acenter">10%-15% loss of all units (military, civilian, arcane)</td></tr>
<tr><th></th>
    <td class="acenter">16%-54% loss of all buildings, weighted towards the middle</td></tr>
<tr><th></th>
    <td class="acenter">15%-45% initial loss of food, mana, and cash; this value <b>increases</b> for larger empires, and <b>decreases</b> for smaller empires</td></tr>
<tr><th></th>
    <td class="acenter"><?=$config[initturns]?> turns are lost (not taken)</td></tr>
<tr><th></th>
    <td class="acenter">50% health drop</td></tr>
<tr><td colspan="2"><hr style="width:100%"></td></tr>
<tr><th>Industry Settings</th>
    <td>The percentage of each type of unit your blacksmiths put effort into.</td></tr>
<tr><th></th>
    <td>Troops are produced according to the formula:</td></tr>
<tr><th></th>
    <td>(<?=$uera[industry]?>) * (Troop_type_percentage) * (Multipler)</td></tr>
<tr><th>Multipliers:</th>
    <td><table class="guide" style="width:100%"><tr><td><?=$uera[armtrp]?> = 1.2</td><td><?=$uera[lndtrp]?> = 0.6</td><td><?=$uera[flytrp]?> = 0.3</td><td><?=$uera[seatrp]?> = 0.2</td></tr></table></td></tr>
<tr><td colspan="2"><hr style="width:100%"></td></tr>
<tr><th>Change Password</th>
    <td>This changes your password.</td></tr>
<tr><th></th>
    <td>For security reasons, your actual password is never stored.<br>
        After changing your password (and when you first set it), one way encryption is used and the encrypted value is stored in our database.<br>
        Whenever you enter your password, it is encrypted and then checked against the encrypted code in our database.<br>
        This way, you can rest assured that nobody here <b>ever</b> sees your password.<br>
        Even if the server security were comprimised (hacked), your password could not fall into the wrong hands.</td></tr>
<tr><td colspan="2"><hr style="width:100%"></td></tr>
<tr><th>Vacation</th>
    <td>This allows you to put your empire in a dormant state.</td></tr>
<tr><th></th>
    <td>Upon setting vacation, you are immediately locked out of your account.</td></tr>
<tr><th></th>
    <td>It takes 12 hours for your country to become protected and completely dormant.<br>
        This 12 hour waiting period is to prevent empires from entering protection in order to avoid retaliatory attacks.</td></tr>
</table>
<?
}

function magic()
{
	global $uera, $config;
include("magicfun.php");
	global $spname;	
?>
<b>Magic</b><br>
<table class="inputtable">
<tr><th>Casting spells</th>
    <td>Every time you cast a spell you spend the amount of <?=$uera[runes]?> specified next to the spell name.</td></tr>
<tr><th>Restrictions</th>
    <td>Spells cannot be cast if your empire's health is below 20.  Offensive spells each cost 2 health to cast.</td></tr>
<tr><th>Success/Failure</th>
    <td>If you fail in casting a spell, a number of your wizards are killed.<br>
	Spell success and failure are based on the following <?=$uera[wizards]?> ratios:</td></tr>
<tr><th>Offensive spells:</th>
    <td>((Your_<?=$uera[wizards]?>)/((Your_Land + Enemy_Land)/2)))/((Enemy_<?=$uera[wizards]?>)/(Enemy_Land))</td></tr>
<tr><th>Utility spells:</th><td>(<?=$uera[wizards]?>)/(<?=$uera[labs]?>)</td></tr>
<tr><td colspan="2"><hr style="width:100%"></td></tr>
<tr><td colspan="2">
    <table class="inputtable" style="width:100%">
    <tr><th>Utility Spell:</th><th><?=$spname[3]?></th><th><?=$spname[6]?></th><th><?=$spname[10]?></th><th><?=$spname[11]?></th><th>Advance to Next Era</th></tr>
    <tr><th>Ratio:</th><td>15</td><td>30</td><td>75</td><td>80</td><td>90</td></tr>
    </table>
    <table class="inputtable" style="width:100%">
    <tr><th>Offensive Spell:</th><th><?=$spname[1]?></th><th><?=$spname[2]?></th><th><?=$spname[4]?></th><th><?=$spname[5]?></th><th><?=$spname[7]?></th><th><?=$spname[8]?></th><th><?=$spname[9]?></th></tr>
    <tr><th>Ratio:</th><td>1</td><td>1.15</td><td>1.21</td><td>1.3</td><td>1.7</td><td>1.75</td><td>2.2</td></tr>
    </table></td></tr>
<tr><td colspan="2"><hr style="width:100%"></td></tr>
<tr><th><?=$spname[1]?></th>
    <td>Returns strategic information on your enemy's empire.</td></tr>
<tr><th><?=$spname[2]?></th>
    <td>Destroys 3% of your enemy's military units.</td></tr>
<tr><th><?=$spname[3]?></th>
    <td>Reduces damage from spells by 2/3.</td></tr>
<tr><th><?=$spname[4]?></th>
    <td>Destroys 9.12% of your enemy's food and 12.65% of their cash.</td></tr>
<tr><th><?=$spname[5]?></th>
    <td>Destroys 3% of your enemy's <?=$uera[runes]?></td></tr>
<tr><th><?=$spname[6]?></th>
    <td>Destroys 3% of every of your enemy's buildings as long as they have at least 15 of that type.</td></tr>
<tr><th><?=$spname[7]?></th>
    <td>Creates (<?=$uera[wizards]?> * health * 70 * (1 + (<?=$uera[labs]?>/Land) * (Racial_Magic_Bonus)))/<?=$config[food]?> <?=$uera[food]?>.<br>
        This value is also multipled by an empire size modifier -- increasing ammounts for smaller empires, decreasing for larger.</td></tr>
<tr><th><?=$spname[8]?></th>
    <td>Creates (<?=$uera[wizards]?> * health * 70 * (1 + (<?=$uera[labs]?>/Land) * (Racial_Magic_Bonus))) dollars.<br>
        This value is also multipled by an empire size modifier -- increasing ammounts for smaller empires, decreasing for larger.</td></tr>
<tr><th><?=$spname[9]?></th>
    <td>Opens a gate into the other time periods.</td></tr>
<tr><th><?=$spname[10]?></th>
    <td>Closes an open time gate to other time periods.</td></tr>
<tr><th><?=$spname[11]?></th>
    <td>Does battle with your magic users.<br>
        If successful, you gain 7% of their occupied land, 10% of their undeveloped land, lose 9% of your <?=$uera[wizards]?>, and destroy 6% of your enemy's <?=$uera[wizards]?>.<br>
        If unsuccessful, you suffer a 10% <?=$uera[wizard]?> loss and your enemy suffers a 5% loss.</td></tr>
<tr><th><?=$spname[12]?></th>
    <td>Steals 10% - 15% of your enemy's cash and gives it to you.</td></tr>
<tr><th>Advance to Next Era</th>
    <td>Advances you from the past to present or present to future.</td></tr>
</table>
<?
}

function races()
{
	global $racedb, $uera, $config;
?>
<h2>Race Bonuses</h2>
<table class="inputtable">
<tr><th>Offense:</th>
    <td>Your offensive power while attacking other empires.</td></tr>
<tr><th>Defense:</th>
    <td>Your defensive power when being attacked by other empires.</td></tr>
<tr><th>Build:</th>
    <td>How quickly you can construct (and demolish) structures.</td></tr>
<tr><th>Costs:</th>
    <td>The amount of money you must pay for upkeep on your military units.</td></tr>
<tr><th>Magic:</th>
    <td>Your magical power, used when casting spells and when other empires cast spells on you.</td></tr>
<tr><th>Industry:</th>
    <td>Your ability to produce military units.</td></tr>
<tr><th>Income:</th>
    <td>Your Per Capita Income, how much your people make each turn.</td></tr>
<tr><th>Explore:</th>
    <td>How much land you gain per turn spent exploring.</td></tr>
<tr><th>Market:</th>
    <td>The prices of military units on the (private) black market.</td></tr>
<tr><th>Food:</th>
    <td>How much food your people consume each turn.</td></tr>
<tr><th>Runes:</th>
    <td>How much mana your wizards can produce each turn.</td></tr>
<tr><th>Farms:</th>
    <td>How much food your farms produce each turn.</td></tr>
</table>
<table border>
<tr><th>Race</th>
    <th>Offense</th>
    <th>Defense</th>
    <th>Build</th>
    <th>Costs</th>
    <th>Magic</th>
    <th>Industry</th>
    <th>Income</th>
    <th>Explore</th>
    <th>Market</th>
    <th>Food</th>
    <th>Runes</th>
    <th>Farms</th></yt>
<?
function printRace ($race)
{
?>
<tr><th><?=$race[name]?></th>
<?
	printAttrib($race,'offense',1);
	printAttrib($race,'defense',1);
	printAttrib($race,'bpt',1);
	printAttrib($race,'costs',2);
	printAttrib($race,'magic',1);
	printAttrib($race,'ind',1);
	printAttrib($race,'pci',1);
	printAttrib($race,'expl',1);
	printAttrib($race,'mkt',2);
	printAttrib($race,'food',2);
	printAttrib($race,'runes',1);
	printAttrib($race,'farms',1);
}

function printAttrib ($race, $attrib, $type)
{
	$val = 100 * ($race[$attrib] - 1);
	if ($val < 0)
		$sign = '-';
	if ($val > 0)
		$sign = '+';
	if ($val == 0)
		$color = 'cneutral';
	if ($type == 1)
	{
		if ($val < 0)
			$color = 'cbad';
		elseif ($val > 0)
			$color = 'cgood';
		else	$sign = '+';
	}
	if ($type == 2)
	{
		if ($val < 0)
			$color = 'cgood';
		elseif ($val > 0)
			$color = 'cbad';
		else	$sign = '-';
	}
?>
    <td class="<?=$color?>"><?=$sign?><?=abs($val)?>%</td><?
	if ($attrib == 'farms') print '</tr>';?>

<?
}
$races = mysql_query("SELECT * FROM $racedb;");
while ($race = mysql_fetch_array($races))
	printRace($race);
?>
</table>
<?
}

function intro()
{
	global $turnsper, $perminutes, $config;
?>
<h3>The Goal</h3>
<table class="inputtable">
<tr><td>As leader of a newly founded empire, your goal is to become supreme to all others. Using everything from diplomacy to war, you must strive to build an empire wealthier than all others (measured in networth).<br>
        Through this all, you will compete against hundreds to thousands of other players all vying to achieve the same goals.</td></tr>
</table>
<h3>Turn Based Games</h3>
<table class="inputtable">
<tr><th>Turns</th>
    <td>In <?=$config[servname]?> Promisance, you receive <b><?=$turnsper?> every <?=$perminutes?></b> minutes.<br>
        You cannot have more than <?=$config[maxturns]?> turns at once, so it is generally recommended you play your turns <i>about</i> once a day.<br>
        Available turns are listed in the <a href="<?=$config[main]?>?action=guide&amp;section=statusbar">status bar</a>.</td></tr>
<tr><th>Turn Use</th>
    <td>Attacking, casting spells and sending foreign aid take <b>two turns</b>.<br>
        Exploring, building, and cashing all use a <b>variable number</b> of turns.<br>
        You may continue to spend turns until you have run out and then you must wait for more to be given.</td></tr>
<tr><th>Stored Turns</th>
    <td>Up to <?=$config[maxstoredturns]?> turns above your maximum will be stored and then released at a rate of one additional turn per <?=$perminutes?> minutes.</td></tr>
<tr><th>Taxes</th>
    <td>Each turn you take will also give you tax revenues and your country will naturally grow or shrink, depending on your economic situation.</td></tr>
<tr><th>Protection</th>
    <td>For your first <?=$config[protection]?> turns you will be in protection status.  During this time, you may not attack, foreign aid or access the public market and others cannot attack or magic you, either.</td></tr>
</table>
<?
}

function messages()
{
	global $perminutes;
?>
<h3>Messages</h3>
<table class="inputtable">
<tr><th>What</th>
    <td>Messages are sent to other empires in the game (other people).</td></tr>
<tr><th>Why</th>
    <td>Sending messages to other empires is a good way to improve relations.</td></tr>
<tr><th>How</th>
    <td>Enter the recieving empire number (no # sign) in the space above the text box.<br>
    Enter your message in the text box.  Generally, it is advisable to sign your name.</td></tr>
<tr><th>Details</th>
    <td>You receive 1 additional credit every <?=$perminutes?> minute<?=plural($perminutes,"s","")?>, up to a total of 5 credits.<br>
        Sending a message, except when chosing "reply," uses 1 credit.</td></tr>
</table>
<?
}

function statusbar()
{
	global $uera;
?>
<h3>The Status Bar</h3>
<table class="inputtable">
<tr><th>Mailbox</th>
    <td>This link goes to messaging.  It changes to "<b>New Mail!</b>" when you have unread messages.</td></tr>
<tr><th>Turns</th>
    <td>The number of turns available for your use.  See the <a href="<?=$config[main]?>?action=guide&amp;section=intro">introduction</a> for information on turns.</td></tr>
<tr><th>Cash</th>
    <td>The amount of money you have, <b>not</b> including your bank account.</td></tr>
<tr><th>Land</th>
    <td>The amount of land your empire covers.</td></tr>
<tr><th><?=$uera[runes]?></th>
    <td>The amount of mana for your wizards to use.</td></tr>
<tr><th><?=$uera[food]?></th>
    <td>The amount of <?=$uera[food]?> you have.</td></tr>
<tr><th>Health</th>
    <td>Your people's (civilians, troops, and wizards) health.  Health affects your attack and defense strenght along with your income.</td></tr>
<tr><th>Networth</th>
    <td>Your empire's total value.</td></tr>
</table>
<?
}

function eras()
{
	global $uera;
?>
<h3>The Eras Differences</h3>
<table class="guide">
<tr><th>Era</th>
    <th>Industry</th>
    <th>Mana</th></tr>
<tr><th>Past</th>
    <td>-5%</td>
    <td>+20%</td></tr>
<tr><th>Present</th>
    <td>+0%</td>
    <td>+0%</td></tr>
<tr><th>Future</th>
    <td>+15%</td>
    <td>+0%</td></tr>
<tr><th>Units</th>
    <td colspan="2">See <a href="<?=$config[main]?>?action=guide&amp;section=munits">Military Units</a></td></tr>
</table>
<?
}

function main()
{
	global $uera;
?>
<h3>Main Page</h3>
<table class="guide">
<tr><th>Turns</th>
    <td>Number of turns you currently have that you can use.</td></tr>
<tr><th>Turns Stored</th>
    <td>The number of turns that are stored.  See the <a href="promisance.php?action=guide&section=intro">introduction to turn based games</a>.</td>
<tr><th>Rank</th>
    <td>Your rank compared to other players.</td></tr>
<tr><th>Peasants</th>
    <td>The number of <?=$uera[peasants]?> you have.</td></tr>
<tr><th>Land Acres</th>
    <td>The amount of land you have.</td></tr>
<tr><th>Money</th>
    <td>The amount of cash you have on hand, not counting money in the bank.</td></tr>
<tr><th><?=$uera[food]?></th>
    <td>The amount of food you have on hand.</td></tr>
<tr><th><?=$uera[runes]?></th>
    <td>The amount of mana you have on hand.</td></tr>
<tr><th>Networth</th>
    <td>The estimated value of your country taking all empire assets into account.</td></tr>
<tr><th>Era</th>
    <td>The time period you are in.  See also <a href="promisance.php?action=guide&section=eras">time periods</a>.</td></tr>
<tr><th>Race</th>
    <td>Your race.  See also <a href="promisance.php?action=guide&section=races">races</a>.</td></tr>
<tr><th>Health</th>
    <td>The health and happiness of your people and troops.</td></tr>
<tr><th>Tax Rate</th>
    <td>Your empire's tax rate.  This can be chagned in empire settings.</td></tr>
<tr><th><?=$uera[armtrp]?></th>
    <td>The number of <?=$uera[armtrp]?> you have.</td></tr>
<tr><th><?=$uera[lndtrp]?></th>
    <td>The number of <?=$uera[lndtrp]?> you have.</td></tr>
<tr><th><?=$uera[seatrp]?></th>
    <td>The number of <?=$uera[seatrp]?> you have.</td></tr>
<tr><th><?=$uera[flytrp]?></th>
    <td>The number of <?=$uera[airtrp]?> you have.</td></tr>
<tr><th><?=$uera[wizards]?></th>
    <td>The number of <?=$uera[wizards]?> you have.</td></tr>
</tr>
</table>
<?
}


?>
<h1><a href="<?=$config[main]?>?action=guide">Promisance Guide</a></h1>
<?

switch ($section)
{
	case messages:		messages();	break;
	case main:		main();		break;
	case statusbar:		statusbar();	break;
	case build:		structures();	break;
	case eras:		eras();		break;
	case military:		military();	break;
	case status:		status();	break;
	case scores:		scores();	break;
	case search:		esearch();	break;
	case farm:		farm();		break;
	case cash:		cash();		break;
	case land:		explore();	break;
	case news:		news();		break;
	case intro:		intro();	break;
	case munits:		munits();	break;
	case pvtmarketbuy:
	case pvtmarketsell:	bmarket();	break;
	case pubmarketbuy:
	case pubmarketsell:	pmarket();	break;
	case manage:		manage();	break;
	case magic:		magic();	break;
	case races:		races();	break;
	default:
?>
<table class="guide" style="width:90%">
<tr><th>General</th>
    <th>Information</th>
    <th>Use Turns</th>
    <th>Finances</th>
    <th>Relations</th>
    <th>Management</th></tr>
<tr><td><a href="<?=$config[main]?>?action=guide&amp;section=intro">Introduction</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=news">News Search</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=cash">Cashing</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=pubmarketbuy">Public Market</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=magic">Magic and Spells</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=manage">Managing Your Empire</a></td></tr>
<tr><td><a href="<?=$config[main]?>?action=guide&amp;section=races">Race Info</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=status">Empire Status</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=land">Exploration</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=pvtmarketbuy">Black Market</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=military">Attacking</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=messages">Messaging</a></td></tr>
<tr><td><a href="<?=$config[main]?>?action=guide&amp;section=munits">Military Units</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=scores">Scores List</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=build">Structures</a></td>
    <td></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=messages">Messaging</a></td>
    <td></td></tr>
<tr><td><a href="<?=$config[main]?>?action=guide&amp;section=statusbar">Status Bar</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=search">Empire Search</a></td>
    <td><a href="<?=$config[main]?>?action=guide&amp;section=farm">Farming</a></td>
    <td></td>
    <td></td>
    <td></td></tr>
<tr><td><a href="<?=$config[main]?>?action=guide&amp;section=eras">Time Periods</a></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td></tr>
</table>
<?
		break;
}
TheEnd("");
?>
