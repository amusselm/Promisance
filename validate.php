<?
include("header.php");
if ($do_validate)
{
	if ($num = sqleval("SELECT num FROM $playerdb WHERE valcode='$valcode' AND num=$users[num];"))
	{
		mysql_query("UPDATE $playerdb SET validated=1 WHERE num=$num;");
		mysql_query("UPDATE $playerdb SET disabled=0 WHERE num=$num AND disabled=1;");
	        TheEnd("User validated!");
	}
	else	TheEnd("Invalid validation code!");
}
else	TheEnd("");
?>
