<?
include("funcs.php");

$num = sqleval("SELECT COUNT(*) FROM $playerdb where land!=0;");

if (!$style)	$style = 0;
if ($style < 0)	$style = 0;
if ($style > 4)	$style = 4;
if (!$digits)	$digits = strlen($num);

function_exists(ImageCreateFromPNG) or die("Error: you must have php_gd v1.8 or greater installed!");

$num = str_pad($num,$digits,0,STR_PAD_LEFT);
$src = @ImageCreateFromPNG("counter.png") or die("Unable to open source image!");
$dest = ImageCreate(16*$digits,16);
ImageColorTransparent($dest,ImageColorAllocate($dest,255,0,0));

for ($i = 0; $i < $digits; $i++)
	ImageCopy($dest,$src,16*$i,0,16*$num[$i],16*$style,16,16);

Header("Pragma: no-cache");
Header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
$imgtypes = ImageTypes();
Header("Content-type: image/png");
ImagePNG($dest);
ImageDestroy($dest);
?>
