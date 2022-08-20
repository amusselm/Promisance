<?
if ($action == "ad")
{
	if (!$lockdb)
	{
		$giveturns = ceil(60 / $perminutes) * $turnsper;
		mysql_query("UPDATE $playerdb SET free=$time+86400,turns=turns+$giveturns WHERE free<$time AND username='$cookie[usernamecookie]' AND password='$cookie[passwordcookie]';");
	}
	if ($ad_ismap)
		$ad_url .= "?$ad_img_x,$ad_img_y";
	Header("Location: $ad_url");
	exit;
}

$numads = 0;
// ADD YOUR ADS HERE
// Syntax: $ads[$numads++] = array('label'=>'Ad Caption','image'=>'Ad Image','url'=>'Ad URL','ismap'=>'1 if ad is imagemap, omit if not');
$ads[$numads++] = array('label'=>'slashdot.org','image'=>'http://images.qm.ath.cx/slashdot.gif','url'=>'http://slashdot.org/');

if ($numads > 1)
	$ad = mt_rand(0,$numads-1);
else	$ad = 0;
?>
<form method="post" action="<?=$config[main]?>?action=ad" target="_blank">
<input type="hidden" name="ad_id" value="<?=$ad?>">
<input type="hidden" name="ad_url" value="<?=$ads[$ad][url]?>">
<input type="hidden" name="ad_ismap" value="<?=$ads[$ad][ismap]?>">
<input type="image" name="ad_img" src="<?=$ads[$ad][image]?>" alt="<?=$ads[$ad][label]?>" style="width:468px;height:60px;border:0">
</form>
