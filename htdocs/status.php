<?php
require_once "localise.php";
require_once "version.php";
require_once "database.php";


// totals {{{
$res = sql_query("
	SELECT
		SUM(villages) AS villages,
		SUM(guilds) AS guilds,
		SUM(owners) AS owners,
		SUM(population) AS population
	FROM servers WHERE visible=True");
$row = sql_fetch_row($res);
$total_villages = $row['villages'];
$total_guilds = $row['guilds'];
$total_owners = $row['owners'];
$total_population = $row['population'];
$totals = "
			<tr><th colspan='6'>Totals</th></tr>
			<tr>
				<td>Server</td>
				<td>Status</td>
				<td>Alliances</td>
				<td>Players</td>
				<td>Villages</td>
				<td>Population</td>
			</tr>
			<tr>
				<td>-</td>
				<td>-</td>
				<td>$total_guilds</td>
				<td>$total_owners</td>
				<td>$total_villages</td>
				<td>$total_population</td>
			</tr>
";
// }}}

// rows {{{
$m = date("m"); // Month value
$d = date("d"); //today's date
$y = date("Y"); // Year value
$today = date('Y-m-d');
$yesterday = date('Y-m-d', mktime(0,0,0,$m,($d-1),$y));

$rows = array();
$links = array();
$lastcountry = "";
$res = sql_query("SELECT name,country,villages,status,owners,guilds,population FROM servers WHERE visible=True ORDER BY country, num");
while($row = sql_fetch_row($res)) {
	$name = $row['name'];
	$country = $row['country'];
	$villages = $row['villages'];
	$status = $row['status'];

	if(substr($status, 0, 10) == $today || substr($status, 0, 10) == $yesterday ||
			$status == "map.sql downloaded" || $status == "karte.sql downloaded") {
		$status = "<font color='green'>$status</font>";
	}
	else {
		$status = "<font color='red'>$status</font>";
	}

	$players = $row['owners'];
	$guilds = $row['guilds'];
	$population = $row['population'];
	
	if($country != $lastcountry) {
		$links[] = "<a href='#$country'>$country</a>";
		$rows[] = "
			<tr><th colspan='6'><a name='$country'>$country</a></th></tr>
			<tr>
				<td>Server</td>
				<td>Status</td>
				<td>Alliances</td>
				<td>Players</td>
				<td>Villages</td>
				<td>Population</td>
			</tr>
		";
		$lastcountry = $country;
	}
	$rows[] = "
		<tr>
			<td><a href='http://$name/'>$name</a></td>
			<td>$status</td>
			<td>$guilds</td>
			<td>$players</td>
			<td>$villages</td>
			<td>$population</td>
		</tr>
	";
}
// }}}

// html template {{{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
	<head>
		<title>TravMap <?=$version;?></title>
		<style>
BODY {
	background: #EEE;
	font-family: "Arial", sans-serif;
	font-size: 14px;
}
TH {
	background: #DDD;
}
TD {
	vertical-align: top;
	text-align: center;
	padding: 0px 10px 0px 10px;
}
		</style>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	</head>
	<body>
		<?php print implode($links, ", "); ?>
		<p>
		<table border="1" align="center">
			<?php print $totals; ?>
			<?php print implode($rows, "\n"); ?>
		</table>
	</body>
</html>
<?php
// }}}
?>
