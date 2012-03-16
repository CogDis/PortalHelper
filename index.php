<html><head>
	<title>Portal Helper v0.4</title>
	<style type="text/css">
		input { width: 50px; }
	</style>
	<meta name="description" content="Plugin by dotblank, Calculator by narc0tiq" />
	<meta name="last-update" content="2012-03-13 01:42 UTC+0200" />
</head>
<body>

<form method="post">
	<div style="float: left; clear: left; top: 0px; left: 16px">
	<h2>Origin</h2>
	X: <input name="origx" value="<?PHP if(!empty($_REQUEST['origx'])) echo ($_REQUEST['origx'] + 0); ?>" />
	Z: <input name="origy" value="<?PHP if(!empty($_REQUEST['origy'])) echo ($_REQUEST['origy'] + 0); ?>" />
	</div>

	<div style="float: right; clear: right; top: 0px; right: 16px">
	<h2>Destination</h2>
	X: <input name="destx" value="<?PHP if(!empty($_REQUEST['destx'])) echo ($_REQUEST['destx'] + 0); ?>" />
	Z: <input name="desty" value="<?PHP if(!empty($_REQUEST['desty'])) echo ($_REQUEST['desty'] + 0); ?>" />
	Y: <input name="destz" value="<?PHP if(!empty($_REQUEST['destz'])) echo ($_REQUEST['destz'] + 0); ?>" />
	</div>

	<div style="margin-top: 125px; clear: both; position: relative; left: 20%; width: 60%; text-align: center">
	<h2>Done?</h2>
	<input type="submit" name="submit" style="width: 100px" accesskey="s" />
	</div>
</form>

<?PHP

define('ICONS_URL', 'icons2');

$tier_multipliers = array(4096, 256, 16, 1);
$mat_multipliers = array(
	0 => 'wool (id 35:0)',
	1 => 'wool:orange (id 35:1)',
	2 => 'wool:magenta (id 35:2)',
	3 => 'wool:lightblue (id 35:3)',
	4 => 'wool:yellow (id 35:4)',
	5 => 'wool:lightgreen (id 35:5)',
	6 => 'wool:pink (id 35:6)',
	7 => 'wool:gray (id 35:7)',
	8 => 'wool:lightgray (id 35:8)',
	9 => 'wool:cyan (id 35:9)',
	10 => 'wool:purple (id 35:10)',
	11 => 'wool:blue (id 35:11)',
	12 => 'wool:brown (id 35:12)',
	13 => 'wool:green (id 35:13)',
	14 => 'wool:red (id 35:14)',
	15 => 'wool:black (id 35:15)'

);
$mat_icon_urls = array(
	 0 => ICONS_URL.'/white.png',
	 1 => ICONS_URL.'/orange.png',
	 2 => ICONS_URL.'/magenta.png',
	 3 => ICONS_URL.'/lightblue.png',
	 4 => ICONS_URL.'/yellow.png',
	 5 => ICONS_URL.'/lightgreen.png',
	 6 => ICONS_URL.'/pink.png',
	 7 => ICONS_URL.'/gray.png',
	 8 => ICONS_URL.'/lightgray.png',
	 9 => ICONS_URL.'/cyan.png',
	10 => ICONS_URL.'/purple.png',
	11 => ICONS_URL.'/blue.png',
	12 => ICONS_URL.'/brown.png',
	13 => ICONS_URL.'/green.png',
	14 => ICONS_URL.'/red.png',
	15 => ICONS_URL.'/black.png',

	62 => ICONS_URL.'/bookshelf.png',
	63 => ICONS_URL.'/dirt.png',
	64 => ICONS_URL.'/sponge.png'
);

if(!empty($_REQUEST['submit']))
{
	$origx = $_REQUEST['origx'] + 0;
	$origy = $_REQUEST['origy'] + 0;
	$origz = $_REQUEST['origz'] + 0;

	$destx = $_REQUEST['destx'] + 0;
	$desty = $_REQUEST['desty'] + 0;
	$destz = $_REQUEST['destz'] + 0;

	$xdif = $destx - $origx;
	$ydif = $desty - $origy;
	$zdif =    255 - $destz;

	$xtiers = calc_xydistance($origx, $destx);
	$ytiers = calc_xydistance($origy, $desty);
	$ztiers = calc_xydistance($destz,    255);

	// calc_xydistance always returns four digits, so trim the first two from
	//the right tier... (they're always 0 in the current implementation)
	unset($ztiers[0]);
	unset($ztiers[1]);

	$ztiers = array_values($ztiers);

	// On display, we'll add the sponge to the third tier (we don't want to
	//confuse the text display, so we won't do it here)

	$left_tier  = $xtiers;
	$mid_tier   = $ytiers;
	$right_tier = $ztiers;
	$inverter  = false;

	$pri_facing = 'west';
	$alt_facing = 'south';

	if($xdif >= 0)
	{
		if($ydif >= 0)
		{
			echo '<h3>Build facing east. The middle column is the X column. Needs inverter.</h3>';
			echo '<h3>Alternate: Build facing north. The middle column is the Z column. Needs inverter.</h3>';

			$pri_facing = 'east';
			$alt_facing = 'north';

			$left_tier = $ytiers;
			$mid_tier  = $xtiers;
			$inverter  = true;
		}
		else
		{
			echo '<h3>Build facing north. The middle column is the Z column. No inverter.</h3>';
			echo '<h3>Alternate: Build facing west. The middle column is the X column. No inverter.</h3>';

			$pri_facing = 'north';
			$alt_facing = 'west';
		}
	}
	else
	{
		if($ydif >= 0)
		{
			echo '<h3>Build facing south. The middle column is the Z column. No inverter.</h3>';
			echo '<h3>Alternate: Build facing east. The middle column is the X column. No inverter.</h3>';

			$pri_facing = 'south';
			$alt_facing = 'east';
		}
		else
		{
			echo '<h3>Build facing west. The middle column is the X column. Needs inverter.</h3>';
			echo '<h3>Alternate: Build facing south. The middle column is the X column. Needs inverter.</h3>';

			$pri_facing = 'west';
			$alt_facing = 'south';

			$left_tier = $ytiers;
			$mid_tier  = $xtiers;
			$inverter  = true;
		}
	}

	echo '<p><b>Note:</b> "Facing" is the cardinal direction you\'re facing when looking at the pressure plate and the sponge is to your right.</p>';


	echo '<div style="float:right; clear:left; padding: 8px"><h2>Alternate</h2><p>(facing '.$alt_facing.')'
		.return_preview_table($mid_tier, $left_tier, $right_tier, $inverter)
		.'</div>';

	echo '<div style="float:right; clear:left; padding: 8px"><h2>Preview</h2><p>(facing '.$pri_facing.')'
		.return_preview_table($left_tier, $mid_tier, $right_tier, $inverter)
		.'</div>';


	echo '<h3>X column (diff: '.$xdif.')</h3><ul>';
	echo '<li>any block (ignored by system)</li>';
	foreach($xtiers as $tier => $value)
	{
		echo '<li>'.$mat_multipliers[$value].' ('.$value.' x '.$tier_multipliers[$tier].')</li>';
	}
	echo '</ul>';

	echo '<h3>Z column (diff: '.$ydif.')</h3><ul>';
	echo '<li>any block (ignored by system)</li>';
	foreach($ytiers as $tier => $value)
	{
		echo '<li>'.$mat_multipliers[$value].' ('.$value.' x '.$tier_multipliers[$tier].')</li>';
	}
	echo '</ul>';

	echo '<h3>Y column (value: '.$zdif.')</h3><ul>';
	echo '<li>sponge (id 19)</li>';
	foreach($ztiers as $tier => $value)
	{
		echo '<li>'.$mat_multipliers[$value].' ('.$value.' x '.$tier_multipliers[$tier].')</li>';
	}
	echo '<li>rotation block (see below)</li>';
	if(!empty($inverter))
	{
		echo '<li>sponge (id 19) (this is the inverter)</li>';
	}
	echo '</ul>';

	echo '<h2>Materials legend</h2><ul>';
	foreach($mat_multipliers as $mul => $mat)
	{
		echo '<li>'.$mul.'x == '.$mat.'</li>';
	}
	echo '</ul>'."\n\n";

	echo '<p><b>Protip:</b> You can use material names instead of numbers as
parameters to /give. The names are the ones given in the legend, above. For
instance, /give sponge will work just as well as /give 19, and is easier to remember!
</p>

<p><b>Rotation block:</b> This optional block allows you to change the facing-direction of
players exiting your portal. Replace bookshelf with coloured wool, according to this:</p>
<ul>
	<li> 0-7 will rotate their view by 45 degrees clockwise from their starting
orientation (for instance, a magenta block will have them facing 90 degrees to
the right of the way they were going).</li>
	<li> 8-15 will have them come out facing a fixed direction: 8=south
9=south-west 10=west 11=north-west 12=north 13=north-east 14=east 15=south-east</li>
</ul>';
}
?>
</body></html>
<?PHP
// Rotation block
//
// Must be located under the left column. Uses same coords system.

function return_preview_table($left_tier, $mid_tier, $right_tier, $inverter = false)
{
	global $mat_icon_urls;

	// Any block can go as the first in the left and mid tiers
	array_unshift($left_tier, 63);
	array_unshift($mid_tier, 63);

	// The sponge is always the first thing in the right-hand tier:
	array_unshift($right_tier, 64);

	// The rotation block goes after the digits of the right tier:
	array_push($right_tier, 62);

	// If we have an inverter, it goes in the right tier also:
	if(!empty($inverter))
		array_push($right_tier, 64);


	$ret = '<table border="0px" cellspacing="0px" cellpadding="2px" style="background-color: #ccc">';
	$ret .= '<tr><td colspan="3" style="text-align:center"><img src="'.ICONS_URL.'/pplate.png'.'" /></td></tr>';

	for($i = 0; $i < 5; $i++)
	{
		$ret .= '<tr>
			<td><img src="'.$mat_icon_urls[$left_tier[$i]].'" /></td>
			<td><img src="'.$mat_icon_urls[$mid_tier[$i]].'" /></td>'."\n\t";
		if($i != 4 or !empty($inverter))
			$ret .= "\t".'<td><img src="'.$mat_icon_urls[$right_tier[$i]].'" /></td>'."\n\t";
		$ret .= '</tr>';
	}
	$ret .= '</table>';

	return $ret;
}

function calc_xydistance($orig, $dest)
{
	$diff = abs($dest - $orig);

	// Compensate for probable GPS negative numbers bug.
	if((($dest < 0) and ($orig > 0)) or (($dest > 0) and ($orig < 0)))
		$diff += 1;

	global $tier_multipliers;

	$tiers = array();
	$remainder = $diff;
	foreach($tier_multipliers as $k => $unused)
	{
		$tiers[$k] = floor($remainder / $tier_multipliers[$k]);
		$remainder = $remainder % $tier_multipliers[$k];
	}

	return $tiers;
}
