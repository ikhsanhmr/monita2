<!DOCTYPE html>
<head>
	<link href="css/screen2.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script src="http://code.highcharts.com/highcharts-more.js"></script>
	<script type="text/javascript" src="js/methods.js"></script>
	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>User Management</title>
	
	<style>
		
		table.dashboard td{
			border-width: 0px;
			text-align: center;
			padding: 5px;
		}
		
		table.dashboard{
			background-color: #002aff;
			color: white;
		}
		
		.divtable{
			position: absolute;
			width: 91%;
			top: 210px;
		}
		
		.divtable table{
			margin: 0 auto;
		}
		
		.chartcontainer{
			min-width: 310px; 
			max-width: 400px; 
			height: 300px; 
			margin: 0 auto;
		}
	
	</style>
</head>

<body>
<link href="/css/screen.css" rel="stylesheet" type="text/css">
<h2>Dashboard</h2>
<?php
session_start();
if(!isset($_SESSION['cnip'])) {
	echo "<script>window.open('.', '_self')</script>";
	exit;
}

if($_SESSION['roleid']<=3) {
	require_once "config/control.inc.php";
	$link = mysql_connect($srv, $usr, $pwd);
	
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db);

	$sql = "SELECT COUNT(*) jumlah FROM notadinas WHERE " . ($_SESSION["roleid"]==1? "coalesce(progress,0) = 0": "nip = '$_SESSION[nip]' AND coalesce(progress,0) = 2");
	//echo "$sql<br>";
	$result = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$nd = $row["jumlah"];
	}
	mysql_free_result($result);
	
	$sql = "SELECT COUNT(*) jumlah FROM kontrak k INNER JOIN skkiterbit i ON k.nomorskkoi = i.nomorskki WHERE SIGNED IS NULL";
	//echo "$sql<br>";
	$result = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$kk = $row["jumlah"];
	}
	mysql_free_result($result);
	mysql_close($link);	
	
	echo "Halo $_SESSION[nama],<br><br>";
	echo "Terdapat : <br>";
	echo "- $nd Nota Dinas Baru<br>";
	echo "- $kk Kontrak baru / kontrak yang belum SIGNED<br>";
}

require_once "/config/koneksi.php";

$sql = "SELECT sum(nilaidisburse) as skkimurni, sum(nilaikontrak) as realisasi
		FROM (
			SELECT 	b.nomorskkoi, IFNULL(a.nilaidisburse, 0) AS nilaidisburse, SUM(IFNULL(b.nilaikontrak, 0)) AS nilaikontrak
			FROM 	kontrak b INNER JOIN
					skkiterbit a  ON a.nomorskki = b.nomorskkoi INNER JOIN
					rab c On b.no_rab = c.no_rab
			WHERE 	YEAR(b.tglawal) = ".date("Y")." AND b.pos IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10')
			Group By b.nomorskkoi, a.nilaidisburse
		) as data";
		
$result = mysql_query($sql);

$row1 = mysql_fetch_assoc($result);

$skaimurnirab = $row1['skkimurni'];
$realisasirab = $row1['realisasi'];
$rabvsmurni = 0;

if ($skaimurnirab > 0){
	$rabvsmurni = round(($realisasirab / $skaimurnirab) * 100, 2);
}

echo '
	<div class="page-header" style="text-align:center;">
		<h1>Realisasi Investasi Tahun '.date("Y").'</h1>
	</div>
	<div class="row" style="padding: 0 15px;">
		<div class="col-md-4">
			<div id="container1" class="chartcontainer"></div>
			<div class="divtable">
				<table class="display dashboard" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align: left;">Nilai SKKI</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="skaimurni">'.number_format($row1['skkimurni'], 0, ',', '.').'</td>
						</tr>
						<tr>
							<td style="text-align: left;">Nilai RAB</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="realisasi">'.number_format($row1['realisasi'], 0, ',', '.').'</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	';

mysql_free_result($result);

$sql = "SELECT sum(nilaidisburse) as skkimurni, sum(nilaikontrak) as realisasi
		FROM (
			SELECT 	b.nomorskkoi, IFNULL(a.nilaidisburse, 0) AS nilaidisburse, SUM(IFNULL(b.nilaikontrak, 0)) AS nilaikontrak
			FROM 	kontrak b INNER JOIN
					skkiterbit a  ON a.nomorskki = b.nomorskkoi
			WHERE 	YEAR(b.tglawal) = ".date("Y")." AND b.pos IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10') AND b.no_rab = ''
			Group By b.nomorskkoi, a.nilaidisburse
		) as data";
		
$result = mysql_query($sql);

$row2 = mysql_fetch_assoc($result);	

$skaimurnikontrak = $row2['skkimurni'];
$realisasikontrak = $row2['realisasi'];
$kontrakvsmurni = 0;

if ($skaimurnikontrak > 0){
	$kontrakvsmurni = round(($realisasikontrak / $skaimurnikontrak) * 100, 2);
}
	
echo '
		<div class="col-md-4">
			<div id="container2" class="chartcontainer"></div>
			<div class="divtable">
				<table class="display dashboard" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align: left;">Nilai SKKI</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="skaimurni">'.number_format($row2['skkimurni'], 0, ',', '.').'</td>
						</tr>
						<tr>
							<td style="text-align: left;">Nilai Kontrak</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="realisasi">'.number_format($row2['realisasi'], 0, ',', '.').'</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	';

mysql_free_result($result);
	
$sql = "SELECT	d.tahun, e.akipos AS nilaiaki, SUM( d.nilaibayar ) AS realisasi
		FROM (
				SELECT 	a.nomorskki, SUM( IFNULL( c.nilaibayar, 0 ) ) AS nilaibayar, YEAR( c.tglbayar ) AS tahun
				FROM	kontrak b INNER JOIN 
						skkiterbit a ON a.nomorskki = b.nomorskkoi INNER JOIN 
						realisasibayar c ON b.nomorkontrak = c.nokontrak 
				WHERE YEAR( c.tglbayar ) = ".date("Y")." AND b.pos IN ('62.1','62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10')
				GROUP BY a.nomorskki
			) AS d INNER JOIN 
			saldopos e ON d.tahun = e.tahun
		WHERE e.kdsubpos = 62
		GROUP BY d.tahun";

$result = mysql_query($sql);

$row3 = mysql_fetch_assoc($result);

$aki = $row3['nilaiaki'];
$bayar = $row3['realisasi'];
$bayarvsaki = 0;

if ($aki > 0){
	$bayarvsaki = round(($bayar / $aki) * 100, 2);
}

echo '
		<div class="col-md-4">
			<div id="container3" class="chartcontainer"></div>
			<div class="divtable">
				<table class="display dashboard" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align: left;">Nilai AKI</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="skaimurni">'.number_format($row3['nilaiaki'], 0, ',', '.').'</td>
						</tr>
						<tr>
							<td style="text-align: left;">Nilai Bayar</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="realisasi">'.number_format($row3['realisasi'], 0, ',', '.').'</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
';

mysql_free_result($result);
	
$sql = "SELECT unit.namaunit, IFNULL(e.rpaki,0) AS nilaiaki, SUM(IFNULL(d.nilaibayar, 0)) AS realisasi
		FROM bidang unit LEFT JOIN 
			saldoakibidang e ON unit.id = e.kdbidang LEFT JOIN
			(
				SELECT 	a.nomorskki, SUM( IFNULL( c.nilaibayar, 0 ) ) AS nilaibayar, YEAR( b.tglawal ) AS tahun, n.pelaksana
				FROM	kontrak b INNER JOIN 
						skkiterbit a ON a.nomorskki = b.nomorskkoi INNER JOIN 
						realisasibayar c ON b.nomorkontrak = c.nokontrak LEFT JOIN 
						notadinas_detail n ON a.nomorskki = n.noskk
				WHERE YEAR( b.tglawal ) = ".date("Y")." AND b.pos IN ('62.1','62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10')
				GROUP BY a.nomorskki, c.nilaibayar, n.pelaksana
			) AS d ON unit.id = d.pelaksana
		WHERE (e.tahun = 2018 or e.tahun IS NULL) AND unit.id <> 3
		GROUP BY unit.namaunit
		ORDER BY unit.namaunit";
		
$result = mysql_query($sql);

echo '
	<div class="page-header" style="text-align:center;">
		<h1>Realisasi Investasi Tahun '.date("Y").' per Unit</h1>
	</div>
	<div class="row" style="padding: 0 15px;">
';

$idcount = 4;

$dataakibidang = array();

while ($row = mysql_fetch_assoc($result)) {
    
	
	$akibidang = $row['nilaiaki'];
	$bayarbidang = $row['realisasi'];
	$bayarvsakibidang = 0;

	if ($akibidang > 0){
		$bayarvsakibidang = round(($bayarbidang / $akibidang) * 100, 2);
	}
	
	$dataakibidang[] = array(
		'id' => $idcount,
		'title' => 'Bayar (Murni + Lanjutan)',
		'subtitle' => 'Unit '.$row['namaunit'],
		'value' => $bayarvsakibidang
	);
	
	echo '
		<div class="col-md-4">
			<div id="container'.$idcount.'" class="chartcontainer"></div>
			<div class="divtable">
				<table class="display dashboard" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align: left;">Nilai AKI</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="skaimurni">'.number_format($row['nilaiaki'], 0, ',', '.').'</td>
						</tr>
						<tr>
							<td style="text-align: left;">Nilai Bayar</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="realisasi">'.number_format($row['realisasi'], 0, ',', '.').'</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	';
	
	$idcount++;
}

echo '</div>';
//echo json_encode($dataakibidang);

?>
	<script>
		$(document).ready(function() {
			var options = {
				chart: {
					type: 'gauge',
					plotBackgroundColor: null,
					plotBackgroundImage: null,
					plotBorderWidth: 0,
					plotShadow: false
				},
				
				pane: {
					startAngle: -90,
					endAngle: 90,
					background: null
				},
				
				plotOptions: {
					gauge: {
						dataLabels: {
							enabled: true,
							format: '{y} %'
					 },
						dial: {
							baseLength: '0%',
							baseWidth: 1,
							radius: '100%',
							rearLength: '0%',
							topWidth: 1
						}
					}
				},

				tooltip: {
					enabled: false
				},
				   
				// the value axis
				yAxis: {
					labels: {
						enabled: true,
						x: 35, y: -10
					},
					tickPositions: [0],
					minorTickLength: 0,
					min: 0,
					max: 100,
					plotBands: [{
						from: 0,
						to: 80,
						color: 'rgb(192, 0, 0)', // red
						thickness: '50%'
					}, {
						from: 80,
						to: 90,
						color: 'rgb(255, 192, 0)', // yellow
						thickness: '50%'
					}, {
						from: 90,
						to: 100,
						color: 'rgb(155, 187, 89)', // green
						thickness: '50%'
					}]
				},
				
				credits: {
					enabled: false
				}
			};
			
			var dataakibidang = <?php echo json_encode($dataakibidang); ?>;
			// var dataakibidang = [];
			
			// if(dataakibidang != ""){
				// dataakibidang = JSON.parse(dataakibidangjson);
			// }
			
			$('#container1').highcharts($.extend({}, options, {series: [{
					name: 'Realisasi',
					data: [<?php echo $rabvsmurni; ?>]
				}],
				
				title: {
					text: 'RAB Murni'
				}})
			);
			$('#container2').highcharts($.extend({}, options, {series: [{
					name: 'Realisasi',
					data: [<?php echo $kontrakvsmurni; ?>]
				}],
				
				title: {
					text: 'Kontrak Murni'
				}})
			);
			$('#container3').highcharts($.extend({}, options, {series: [{
					name: 'Realisasi',
					data: [<?php echo $bayarvsaki; ?>]
				}],
				title: {
					text: 'Bayar'
				},
				subtitle: {
					text: '(Murni + Lanjutan)'
				}})
			);
			
			if (dataakibidang.length > 0){
				dataakibidang.forEach(function(dataaki){
					$('#container' + dataaki.id).highcharts($.extend({}, options, {series: [{
							name: 'Realisasi',
							data: [dataaki.value]
						}],
						title: {
							text: dataaki.title
						},
						subtitle: {
							text: dataaki.subtitle
						}})
					);
				});
			}
			
			
		});
	</script>
</body>

</html> 