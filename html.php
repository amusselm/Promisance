<?
function getmicrotime ()
{
	list($usec, $sec) = explode(" ",microtime());
	return ((double)$usec + (double)$sec);
}

function defstyle()
{
	global $styles;
	return $styles[1];
}

function getstyle()
{
	global $styles, $users;
	if ($users[style])
		return $styles[$users[style]];
	else	return defstyle();
}

// Begins a full HTML page
function HTMLbeginfull ($title)
{
	global $starttime;
	$starttime = getmicrotime();
	Header("Pragma: no-cache");
	Header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Promisance - <?=$title?></title>
<link rel="stylesheet" type="text/css" href="<?=getstyle()?>">
</head>
<body>
<table style="width:100%">
<tr><td style="width:144px;vertical-align:top"><?include("menus.php");?></td><td class="acenter" style="vertical-align:top"><?include("ad.php");?>
<?
}

// Ends a full HTML page
function HTMLendfull ()
{
	global $starttime;
	$endtime = getmicrotime() - $starttime;
	echo "<!--$endtime-->\n<!--Mopra 1-->";
?>
</td></tr>
</table>
</body>
</html>
<?
}

// Begins a compact HTML page
function HTMLbegincompact ($title)
{
	global $starttime;
	$starttime = getmicrotime();
	Header("Pragma: no-cache");
	Header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Promisance - <?=$title?></title>
<link rel="stylesheet" type="text/css" href="<?=getstyle()?>">
</head>
<body>
<div class="acenter">
<?
}

// Ends a compact HTML page
function HTMLendcompact ()
{
	global $starttime;
	$endtime = getmicrotime() - $starttime;
	echo "<!--$endtime-->\n<!-- M344 2-->";
?>
</div>
</body>
</html>
<?
}
?>
