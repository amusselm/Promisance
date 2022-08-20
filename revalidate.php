<?
include("header.php");

mail($users[email],"QM Promisance Signup for $config[servname] - $users[empire] (#$users[num])","
Thank you for signing up for $config[servname]!

If you did not sign up for an account with us, please let us know
and delete this message with our apologies.

Your validation code is: $users[valcode]

Be sure to check out the latest creations from us at
http://qm.ath.cx/ and tell your friends about our great
services and games!
","From: QM Promisance Web Game <$config[valemail]>\nX-Mailer: QM Promisance Automatic Validation Script");
TheEnd("Your validation code has been resent!");
?>
