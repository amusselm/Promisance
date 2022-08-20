<?
include("funcs.php");
setcookie("usernamecookie","");
setcookie("passwordcookie","");
HTMLbegincompact("Signup");

function EndNow ($reason)
{
	print "$reason<br>\n";
	HTMLendcompact();
	exit;
}

if ($signupsclosed)
	EndNow("Sorry, the game is currently not accepting new accounts.  Please check back in a few days.");
$lockdb = 0;							// need to allow DB modifications for signups
if ($do_signup)
{
	if (($signup_username == "") || ($signup_password == ""))
		EndNow("You must specify both a username and a password!");
	if (!strstr($signup_name," "))
		EndNow("Sorry, you cannot signup without FULL and CORRECT information.<br>
			Everybody has a last name, and I doubt you're an exception.<br>
			Why do we ask?  Prevent cheating, that's all.");
	if (stristr($signup_name,"the "))
		EndNow("Nice try, but nobody has 'The' as part of their name.");
	if ((strstr($signup_email,",")) || (strstr($signup_email," ")) || (!strstr($signup_email,".")) || (!strstr($signup_email,"@")))
		EndNow("Please enter a valid E-mail address.");
	if ($signup_password != $signup_password_verify)
		EndNow("Your password does not match!");
	if ($signup_email != $signup_email_verify)
		EndNow("Your E-mail address does not match!");
	if (sqleval("SELECT COUNT(*) FROM $playerdb WHERE username='$signup_username';"))
		EndNow("Sorry, that username is already being used!");
	if (sqleval("SELECT COUNT(*) FROM $playerdb WHERE empire='$signup_empire';"))
		EndNow("Sorry, that empire name is already being used!");

	$multi = sqleval("SELECT COUNT(*) FROM $playerdb WHERE email='$signup_email' AND land>0 AND disabled!=2;");

	mysql_query("INSERT INTO $playerdb (num) VALUES (NULL);");	// add a new user entry (with defaults)
	$users = loadUser(mysql_insert_id());				// and load it

	$users[username] = $signup_username;
	$users[password] = md5($signup_password);
	$users[name] = $signup_name;
	$users[email] = $signup_email;
	$users[IP] = $REMOTE_ADDR;

	$users[signedup] = $time;
	if ($users[num] == 1)
		$users[disabled] = 2;
	else	$users[disabled] = 0;
	$users[valcode] = md5($users[username].mt_rand().$users[email]);// should be fairly unique
	$users[idle] = $time;

	$users[empire] = trim(HTMLSpecialChars($signup_empire));
	$users[race] = $signup_race;
	$users[era] = 1;
	$users[rank] = $users[num];

	$users[turns] = $config[initturns];

	$users[msgtime] = $time;
	$users[newstime] = $time;

	saveUserDataNet($users,"networth username password name email IP signedup disabled valcode idle empire race era rank turns msgtime newstime");

	if ($multi)
		mysql_query("UPDATE $playerdb SET ismulti=1,disabled=3 WHERE email='$signup_email' AND disabled!=2;");

	mail($users[email],"Promisance Signup for $config[servname] - $users[empire] (#$users[num])","
Thank you for signing up for $config[servname]!

If you did not sign up for an account with us, please let us know
and delete this message with our apologies.

---
You entered the following information when you signed up:
Username: $signup_username
Password: $signup_password
Validation Code: $users[valcode]

Once you have used $config[valturns] turns, you will be prompted to enter the above
validation code in order to continue playing.
Please do NOT delete this e-mail until you have validated!
---

Be sure to check out the latest creations from us at
$config[home] and tell your friends about our great
services and games!

Your e-mail address shall remain strictly confidential and will NOT
be given out to anyone.

Should you want to reply to this e-mail, please use $config[adminemail]
","From: Promisance Web Game <$config[valemail]>\nX-Mailer: Promisance Automatic Validation Script");
?>
Welcome to Promisance, <b><? print stripslashes("$users[empire]"); ?> (#<?=$users[num]?>)</b>!<br>
Please <a href="<?=$config[main]?>?action=login">Login</a> and manage your new empire!<br><br>
<?
	EndNow("");
}
?>
<h2><i>Promisance</i>: Signup Form</h2>
Welcome to Promisance, the first step to running your own empire is to signup!<br>
The administrators reserve the right to delete any accounts not abiding by the rules.<br><br>
<a href=promisance.php?action=guide>Promisance Playing Guide</a>
<table class="inputtable">
<caption style="font-size:large;font-weight:bold">Basic Rules:</caption>
<tr><th style="font-size:large">Multiple Accounts</th></tr>
<tr><td class="acenter">
    Multiple accounts <b>ARE NOT PERMITTED</b> in this game, including sharing acounts and baby-sitting.<br>
    If anyone is found using multiple accounts, all of the accounts in question will be disabled.<br>
    <b>If, however, you have a special situation (e.g. family member plays too), please send e-mail <a href="mailto:<?=$config[adminemail]?>">here</a> with an explanation.</b></td></tr>
<tr><th style="font-size:large">Server Abuse</th></tr>
<tr><td class="acenter">
    Foul language, hacking, porn, warez, etc. are not permitted on this server; offenders will be deleted and banned.</td></tr>
<tr><th style="font-size:large">Technical Support</th></tr>
<tr><td class="acenter">
    If you have any problems in this game and must send e-mail to an administrator, 
    you <b>MUST include all of the following</b> in your e-mail message:<br><br>
    1. The game in which you are having a problem (<?=$config[servname]?>).<br>
    2. The name and number of all empires involved (i.e. your empire).<br>
    3. The nature of the problem.<br><br>
    <b>Failure to include all 3</b> of the above will likely result in your message being <b>ignored and deleted</b>.</td></tr>
</table><br>
A <b>VALIDATION CODE</b> will be sent to your e-mail address.<br>
<h2>MAKE SURE IT IS CORRECT!</h2>
<h3>You will <i>NOT</i> be able to change it later!</h3>
<form method="post" action="<?=$config[main]?>?action=signup">
<table class="inputtable">
<tr><th colspan="2" style="font-size:large">Personal Information</th></tr>
<tr><th class="aright">Name:</th>
    <td><input type="text" name="signup_name" size="24"></td></tr>
<tr><th class="aright">E-Mail:</th>
    <td><input type="text" name="signup_email" size="24"></td></tr>
<tr><th class="aright">Verify E-Mail:</th>
    <td><input type="text" name="signup_email_verify" size="24"></td></tr>
<tr><td colspan="2" style="font-size:small;text-align:center">Your personal information will remain strictly confidential.</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><th colspan="2" style="font-size:large">Game Information</th></tr>
<tr><th class="aright">Username:</th>
    <td><input type="text" name="signup_username" size="8"></td></tr>
<tr><th class="aright">Password:</th>
    <td><input type="password" name="signup_password" size="8"></td></tr>
<tr><th class="aright">Verify Password:</th>
    <td><input type="password" name="signup_password_verify" size="8"></td></tr>
<tr><th class="aright">Empire Name:</th>
    <td><input type="text" name="signup_empire" size="24" maxlength="32"></td></tr>
<tr><th class="aright">Your Race: <a href=races.html>Help</a></th>
    <td><select name="signup_race" size="1">
<?
$races = mysql_query("select id,name from $racedb;");
while ($race = mysql_fetch_array($races))
{
?>
    <option value="<?=$race[id]?>"<?if ($race[id] == 1) print " selected";?>><?=$race[name]?></option>
<?
}
?>
    </select></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td><input type="submit" name="do_signup" value="Sign Me Up!"></td>
    <td class="aright"><input type="reset" value="Reset Form!"</td></tr>
</table>
</form>
<?
HTMLendcompact();
?>
