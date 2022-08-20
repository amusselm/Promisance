<?
include("header.php");
$size = calcSizeBonus($users[networth]);
$loanrate = $config[loanbase] + $size;
$savrate = $config[savebase] - $size;
$maxloan = $users[networth] * 5;
$maxsave = $users[networth] * 10;

if ($do_borrow)
{
	if ($lastweek)
		TheEnd("Cannot take out loans during the last week of the game!");
	fixInputNum($borrow);
	if ($borrow < 0)
		TheEnd("Cannot take out a negative loan.");
 	if ($borrow + $users[loan] > $maxloan)
		TheEnd("Cannot take out a loan for that much");
	$users[cash] += $borrow;
	$users[loan] += $borrow;
	saveUserDataNet($users,"networth cash loan");
?>
You have taken out a loan for $<?=commas($borrow)?>. 0.5% of the loan will be paid off each turn.<hr>
<?
}
if ($do_repay)
{
	fixInputNum($repay);
	if ($repay > $users[cash])
		TheEnd("You don't have that much money!");
	if ($repay > $users[loan])
		TheEnd("You don't owe that much!");
	$users[cash] -= $repay;
	$users[loan] -= $repay;
	saveUserDataNet($users,"networth cash loan");
?>
Thank you for your $<?=commas($repay)?> payment! It will be credited to your account immediately.<hr>
<?
}
if ($do_deposit)
{
	fixInputNum($deposit);
	if ($deposit > $users[cash])
		TheEnd("You don't have that much money!");
	if ($deposit < 0)
		TheEnd("You cannot deposit a negative amount of money!");
	if ($deposit + $users[savings] > $maxsave)
		TheEnd("Cannot have that much in savings!");
	$users[cash] -= $deposit;
	$users[savings] += $deposit;
	saveUserDataNet($users,"networth cash savings");
?>
You have deposited $<?=commas($deposit)?> into your savings account.<hr>
<?
}
if ($do_withdraw)
{
	fixInputNum($withdraw);
	if ($withdraw > $users[savings])
		TheEnd("You don't have that much in your savings account!");
	$users[cash] += $withdraw;
	$users[savings] -= $withdraw;
	saveUserDataNet($users,"networth cash savings");
?>
You have withdrawn $<?=commas($withdraw)?> from your savings.<hr>
<?
}
?>
<h2>Welcome to the Promisance World Bank</h2>
<table>
    <tr><td>
    <table class="empstatus" style="width:100%">
        <tr class="inputtable"><th colspan="2" style="text-align:center">Savings</th></tr>
        <tr><th>Interest APR:</th>   <td><?=$savrate?>%</td></tr>
        <tr><th>Maximum Balance:</th><td>$<?=commas($maxsave)?></td></tr>
        <tr><th>Current Balance:</th><td>$<?=commas($users[savings])?></td></tr>
    </table></td>
    <td style="width:10%"></td>
    <td>
    <table class="empstatus" style="width:100%">
        <tr class="inputtable"><th colspan="2" style="text-align:center">Loan</th></tr>
        <tr><th>Interest APR:</th>   <td><?=$loanrate?>%</td></tr>
        <tr><th>Maximum Balance:</th><td>$<?=commas($maxloan)?></td></tr>
        <tr><th>Current Balance:</th><td>$<?=commas($users[loan])?></td></tr>
    </table></td></tr>
</table><br>
Interest is calculated per turn (52 turns = 1 APR year)<br>
<?
if ($users[turnsused] < $config[protection])
{
?>
<b>(Savings is NOT calculated during protection)</b><br>
<?
}
?>
<br>
<form method="post" action="<?=$config[main]?>?action=bank">
<table class="inputtable">
<?
if ($lastweek)
{
?>
<tr><td colspan="3">Loans unavailable during last week.</td></tr>
<?
}
else
{
?>
<tr><th>Take out a Loan</th>
    <td>$<input type="text" name="borrow" size="9" value="0"></td>
    <td><input type="submit" name="do_borrow" value="Borrow"></td></tr>
<?
}
?>
<tr><th>Pay Toward Loan</th>
    <td>$<input type="text" name="repay" size="9" value="0"></td>
    <td><input type="submit" name="do_repay" value="Repay"></td></tr>
<tr><th>Deposit into Savings</th>
    <td>$<input type="text" name="deposit" size="9" value="0"></td>
    <td><input type="submit" name="do_deposit" value="Deposit"></td></tr>
<tr><th>Withdraw from Savings</th>
    <td>$<input type="text" name="withdraw" size="9" value="0"></td>
    <td><input type="submit" name="do_withdraw" value="Withdraw"></td></tr>
</table>
</form>
<?
TheEnd("");
?>
