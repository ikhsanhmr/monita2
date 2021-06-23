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

if($_SESSION['roleid']<=3 || $_SESSION['roleid'] == 20) {
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
	
	$sql = "SELECT COUNT(*) jumlah FROM kontrak k INNER JOIN skkiterbit i ON k.nomorskkoi = i.nomorskki WHERE SIGNED IS NULL and Year(inputdt) = ".date("Y");
	//echo "$sql<br>";
	$result = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$kk = $row["jumlah"];
	}
	mysql_free_result($result);

	$sql = "SELECT 	COUNT(k.nomorkontrak) jumlah 
			FROM 	( 
						SELECT 	t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype 
						FROM 	kontrak_approval t1 WHERE t1.id = (
																	SELECT 	t2.id 
																	FROM 	kontrak_approval t2 
																	WHERE 	TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak) 
																	ORDER BY t2.signdt DESC LIMIT 1
																) 
					) ka INNER JOIN kontrak k ON TRIM(k.nomorkontrak) = TRIM(ka.nmrkontrak) 
			Where " . ( $_SESSION['roleid'] > 1 ? "k.pos IN (Select akses From akses_pos Where nip = '".$_SESSION['cnip']."') AND" : "" ) . " (signlevel = 2 AND actiontype = 1)";
	//echo "$sql<br>";
	$result = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$ib = $row["jumlah"];
	}
	mysql_free_result($result);
	mysql_close($link);	
	
	echo "Halo $_SESSION[nama],<br><br>";
	echo "Terdapat : <br>";
	echo "- $nd Nota Dinas Baru<br>";
	echo "- $kk Kontrak baru / kontrak yang belum SIGNED<br>";
	echo "- $ib Inbox Bayar baru<br>";
} elseif ($_SESSION['roleid'] == 4 || $_SESSION['roleid'] == 5){

	require_once "config/control.inc.php";
	$link = mysql_connect($srv, $usr, $pwd);
	
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db);

	$sql = "SELECT 	COUNT(k.nomorkontrak) jumlah 
			FROM 	(
						SELECT	t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype
						FROM	kontrak_approval t1
						WHERE	t1.id = (	SELECT	t2.id
											FROM	kontrak_approval t2
											WHERE	TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak)
											ORDER BY t2.signdt DESC
											LIMIT 1
										)
					) ka INNER JOIN 
					kontrak k ON TRIM(ka.nmrkontrak) = TRIM(k.nomorkontrak) INNER JOIN
					notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 
			Where ".($_SESSION['cnip'] == '7602006A' ? "" : " pelaksana IN (Select akses From akses_bidang Where nip = '".$_SESSION['cnip']."') AND " )." (signlevel = 3 AND actiontype = 1)";
	//echo "$sql<br>";
	$result = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$ib = $row["jumlah"];
	}
	mysql_free_result($result);
	mysql_close($link);	
	
	echo "Halo $_SESSION[nama],<br><br>";
	echo "Terdapat : <br>";
	echo "- $ib Inbox Bayar baru<br>";
} elseif ($_SESSION['roleid'] == 13){

	require_once "config/control.inc.php";
	$link = mysql_connect($srv, $usr, $pwd);
	
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db);

	$sql = "Select 	Count(k.nomorkontrak) jumlah 
			FROM 	(
						SELECT	t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype
						FROM	kontrak_approval t1
						WHERE	t1.id = (	SELECT	t2.id
											FROM	kontrak_approval t2
											WHERE	TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak)
											ORDER BY t2.signdt DESC
											LIMIT 1
										)
					) ka INNER JOIN 
					kontrak k ON TRIM(ka.nmrkontrak) = TRIM(k.nomorkontrak) INNER JOIN
					notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 
			Where ".($_SESSION["cnip"] == "8610292Z" || $_SESSION["cnip"] == "94171330ZY" ? "pelaksana IN ('1','".$_SESSION['bidang']."') " : ($_SESSION["org"] < 6 ? " pelaksana < 6 AND d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."')" : " pelaksana = ".$_SESSION['bidang'] ))." AND (signlevel = 1 AND actiontype = 1)";
	//echo "$sql<br>";
	$result = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$ib = $row["jumlah"];
	}
	mysql_free_result($result);
	mysql_close($link);	
	
	echo "Halo $_SESSION[nama],<br><br>";
	echo "Terdapat : <br>";
	echo "- $ib Inbox Bayar baru<br>";
}

require_once "/config/koneksi.php";

$sql = "SELECT	IFNULL(SUM(nilaidisburse), 0) disburse, IFNULL(SUM(rab),0) rab, IFNULL(SUM(kontrak),0) kontrak
		FROM	( 
					SELECT	noskk, pelaksana 
					FROM	notadinas_detail 
					WHERE	progress >=7 AND NOT noskk IS NULL 
					GROUP BY noskk, pelaksana 
				) d INNER JOIN 
				skkiterbit s ON d.noskk = s.nomorskki LEFT JOIN 
				( 
					SELECT	skk noskk, SUM(nilai_rp) rab
					FROM	rab
					GROUP BY skk 
				) rb ON s.nomorskki = rb.noskk LEFT JOIN 
				( 
					SELECT	nomorskkoi noskk, SUM(nilaikontrak) kontrak
					FROM	kontrak
					WHERE	pos IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10','62.02','62.03','62.04','62.05','62.06','62.07','62.08','62.09')
					GROUP BY nomorskkoi 
				) kr ON s.nomorskki = kr.noskk
		WHERE	YEAR(tanggalskki) = ".date("Y")." AND posinduk IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10','62.02','62.03','62.04','62.05','62.06','62.07','62.08','62.09')";
		
$result = mysql_query($sql);

$row1 = mysql_fetch_assoc($result);

$skaimurnirab = $row1['disburse'];
$realisasirab = $row1['rab'];
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
			<div id="container1a" class="chartcontainer"></div>
			<div class="divtable">
				<table class="display dashboard" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align: left;">Nilai SKKI</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="skaimurni">'.number_format($skaimurnirab, 0, ',', '.').'</td>
						</tr>
						<tr>
							<td style="text-align: left;">Nilai RAB</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="realisasi">'.number_format($realisasirab, 0, ',', '.').'</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	';

mysql_free_result($result);


// $sql = "SELECT	IFNULL(SUM(nilaidisburse), 0) disburse, IFNULL(SUM(kontrak),0) kontrak  
// 		FROM	( 
// 					SELECT	noskk, pelaksana 
// 					FROM	notadinas_detail 
// 					WHERE	progress >=7 AND NOT noskk IS NULL 
// 					GROUP BY noskk, pelaksana 
// 				) d INNER JOIN 
// 				skkiterbit s ON d.noskk = s.nomorskki LEFT JOIN 
// 				( 
// 					SELECT	nomorskkoi noskk, SUM(nilaikontrak) kontrak
// 					FROM	kontrak
// 					WHERE	pos IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10')
// 					GROUP BY nomorskkoi 
// 				) kr ON s.nomorskki = kr.noskk
// 		WHERE	YEAR(tanggalskki) = ".date("Y")." AND posinduk IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10')";
		
// $result = mysql_query($sql);

// $row2 = mysql_fetch_assoc($result);	

$skaimurnikontrak = $row1['disburse'];
$realisasikontrak = $row1['kontrak'];
$kontrakvsmurni = 0;

if ($skaimurnikontrak > 0){
	$kontrakvsmurni = round(($realisasikontrak / $skaimurnikontrak) * 100, 2);
}
	
echo '
		<div class="col-md-4">
			<div id="container1b" class="chartcontainer"></div>
			<div class="divtable">
				<table class="display dashboard" cellspacing="0">
					<tbody>
						<tr>
							<td style="text-align: left;">Nilai SKKI</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="skaimurni">'.number_format($skaimurnikontrak, 0, ',', '.').'</td>
						</tr>
						<tr>
							<td style="text-align: left;">Nilai Kontrak</td>
							<td style="text-align: center;">:</td>
							<td style="text-align: right;" class="realisasi">'.number_format($realisasikontrak, 0, ',', '.').'</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	';

//mysql_free_result($result);

$sql = "SELECT	e.tahun, e.akipos AS nilaiaki, SUM( IFNULL( d.nilaibayar, 0 ) ) AS realisasi
		FROM	saldopos e  left join 
				(
					SELECT 	a.nomorskki, SUM( IFNULL( c.nilaibayar, 0 ) ) AS nilaibayar, YEAR( c.tglbayar ) AS tahun
					FROM	kontrak b INNER JOIN 
							skkiterbit a ON a.nomorskki = b.nomorskkoi INNER JOIN 
							realisasibayar c ON b.nomorkontrak = c.nokontrak 
					WHERE YEAR( a.tanggalskki ) = ".date("Y")." AND b.pos IN ('62.1','62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10','62.01','62.02','62.03','62.04','62.05','62.06','62.07','62.08','62.09')
					GROUP BY a.nomorskki
				) AS d ON e.tahun = d.tahun
		WHERE e.kdsubpos = 62 and e.tahun = ".date("Y")."
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
			<div id="container1c" class="chartcontainer"></div>
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

$sql = "SELECT	id, namaunit, SUM(COALESCE(nilaibayar,0)) realisasi, COALESCE(rpaki,0) nilaiaki 
		FROM	bidang f LEFT JOIN 
				saldoakibidang g ON f.id = g.kdbidang LEFT JOIN
				(
					SELECT 	*
					FROM	notadinas_detail e LEFT JOIN 
							(
								SELECT	b.nomorskki, SUM( IFNULL( c.nilaibayar, 0 ) ) AS nilaibayar
								FROM	kontrak a INNER JOIN 
										skkiterbit b ON a.nomorskkoi = b.nomorskki INNER JOIN 
										realisasibayar c ON a.nomorkontrak = c.nokontrak
								WHERE YEAR( tanggalskki ) = ".date("Y")." AND pos IN ('62.1','62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10','62.01','62.02','62.03','62.04','62.05','62.06','62.07','62.08','62.09')
								GROUP BY b.nomorskki
							) AS d ON e.noskk = d.nomorskki
				) AS h ON f.id = h.pelaksana
		WHERE (g.tahun = ".date("Y")." or g.tahun IS NULL) AND f.id <> 3
		GROUP BY f.id, f.namaunit		 
		ORDER BY LPAD(id, 2, '0')";
		
$result = mysql_query($sql);

$idcount = 2;

$dataakibidang = array();

while ($row = mysql_fetch_assoc($result)) {
    	
	$akibidang = $row['nilaiaki'];
	$bayarbidang = $row['realisasi'];
	$bayarvsakibidang = 0;

	if ($akibidang > 0){
		$bayarvsakibidang = round(($bayarbidang / $akibidang) * 100, 2);
	}
	
	// $sql = "SELECT	IFNULL(SUM(nilaidisburse), 0) disburse, IFNULL(SUM(kontrak),0) kontrak
	// 		FROM	( 
	// 					SELECT	noskk, pelaksana 
	// 					FROM	notadinas_detail 
	// 					WHERE	progress >=7 AND NOT noskk IS NULL 
	// 					GROUP BY noskk, pelaksana 
	// 				) d INNER JOIN 
	// 				skkiterbit s ON d.noskk = s.nomorskki LEFT JOIN 
	// 				( 
	// 					SELECT	skk noskk, SUM(nilai_rp) kontrak
	// 					FROM	rab
	// 					GROUP BY skk 
	// 				) kr ON s.nomorskki = kr.noskk
	// 		WHERE	YEAR(tanggalskki) = ".date("Y")." AND posinduk IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10') AND pelaksana = ".$row['id'];
			
	// $resultchild = mysql_query($sql);

	// $row1 = mysql_fetch_assoc($resultchild);

	// $skaimurnirab = $row1['disburse'];
	// $realisasirab = $row1['kontrak'];
	// $rabvsmurnibidang = 0;

	// if ($skaimurnirab > 0){
	// 	$rabvsmurnibidang = round(($realisasirab / $skaimurnirab) * 100, 2);
	// }
	
	//mysql_free_result($resultchild);
			
	$sql = "SELECT	IFNULL(SUM(nilaidisburse), 0) disburse, IFNULL(SUM(kontrak),0) kontrak, IFNULL(SUM(rab),0) rab  
			FROM	( 
						SELECT	noskk, pelaksana 
						FROM	notadinas_detail 
						WHERE	progress >=7 AND NOT noskk IS NULL 
						GROUP BY noskk, pelaksana 
					) d INNER JOIN 
					skkiterbit s ON d.noskk = s.nomorskki LEFT JOIN 
					( 
						SELECT	nomorskkoi noskk, SUM(nilaikontrak) kontrak
						FROM	kontrak
						WHERE	pos IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10','62.02','62.03','62.04','62.05','62.06','62.07','62.08','62.09')
						GROUP BY nomorskkoi 
					) kr ON s.nomorskki = kr.noskk LEFT JOIN 
					( 
						SELECT	skk noskk, SUM(nilai_rp) rab
						FROM	rab
						GROUP BY skk 
					) rab ON s.nomorskki = rab.noskk
			WHERE	YEAR(tanggalskki) = ".date("Y")." AND posinduk IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10','62.02','62.03','62.04','62.05','62.06','62.07','62.08','62.09') AND pelaksana = ".$row['id'];
			
	$resultchild = mysql_query($sql);
	
	$row2 = mysql_fetch_assoc($resultchild);	

	$skaimurni = $row2['disburse'];
	$realisasikontrak = $row2['kontrak'];
	$realisasirab = $row2['rab'];
	$kontrakvsmurnibidang = 0;
	$rabvsmurnibidang = 0;

	if ($skaimurni > 0){
		$kontrakvsmurnibidang = round(($realisasikontrak / $skaimurni) * 100, 2);
		$rabvsmurnibidang = round(($realisasirab / $skaimurni) * 100, 2);
	}
	
	mysql_free_result($resultchild);
		
	$dataakibidang[] = array(
		'id' => $idcount,
		'title' => 'Bayar (Murni + Lanjutan)',
		'subtitle' => 'Unit '.$row['namaunit'],
		'valuerab' => $rabvsmurnibidang,
		'valuekontrak' => $kontrakvsmurnibidang,
		'valueaki' => $bayarvsakibidang
	);
	
	echo '
		<div class="page-header" style="text-align:center;">
			<h1>Realisasi Investasi Tahun '.date("Y").' <br /> '.$row['namaunit'].'</h1>
		</div>
		<div class="row" style="padding: 0 15px;">
			<div class="col-md-4">
				<div id="container'.$idcount.'a" class="chartcontainer"></div>
				<div class="divtable">
					<table class="display dashboard" cellspacing="0">
						<tbody>
							<tr>
								<td style="text-align: left;">Nilai SKKI</td>
								<td style="text-align: center;">:</td>
								<td style="text-align: right;" class="skaimurni">'.number_format($skaimurni, 0, ',', '.').'</td>
							</tr>
							<tr>
								<td style="text-align: left;">Nilai RAB</td>
								<td style="text-align: center;">:</td>
								<td style="text-align: right;" class="realisasi">'.number_format($realisasirab, 0, ',', '.').'</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-4">
				<div id="container'.$idcount.'b" class="chartcontainer"></div>
				<div class="divtable">
					<table class="display dashboard" cellspacing="0">
						<tbody>
							<tr>
								<td style="text-align: left;">Nilai SKKI</td>
								<td style="text-align: center;">:</td>
								<td style="text-align: right;" class="skaimurni">'.number_format($skaimurni, 0, ',', '.').'</td>
							</tr>
							<tr>
								<td style="text-align: left;">Nilai Kontrak</td>
								<td style="text-align: center;">:</td>
								<td style="text-align: right;" class="realisasi">'.number_format($realisasikontrak, 0, ',', '.').'</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-4">
				<div id="container'.$idcount.'c" class="chartcontainer"></div>
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
		</div>
	';
	
	$idcount++;
}

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
			
			$('#container1a').highcharts($.extend({}, options, {series: [{
					name: 'Realisasi',
					data: [<?php echo $rabvsmurni; ?>]
				}],
				
				title: {
					text: 'RAB Murni'
				}})
			);
			$('#container1b').highcharts($.extend({}, options, {series: [{
					name: 'Realisasi',
					data: [<?php echo $kontrakvsmurni; ?>]
				}],
				
				title: {
					text: 'Kontrak Murni'
				}})
			);
			$('#container1c').highcharts($.extend({}, options, {series: [{
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
					$('#container' + dataaki.id + 'a').highcharts($.extend({}, options, {series: [{
							name: 'Realisasi',
							data: [dataaki.valuerab]
						}],
						
						title: {
							text: 'RAB Murni'
						}})
					);
					$('#container' + dataaki.id + 'b').highcharts($.extend({}, options, {series: [{
							name: 'Realisasi',
							data: [dataaki.valuekontrak]
						}],
						
						title: {
							text: 'Kontrak Murni'
						}})
					);
					$('#container' + dataaki.id + 'c').highcharts($.extend({}, options, {series: [{
							name: 'Realisasi',
							data: [dataaki.valueaki]
						}],
						title: {
							text: 'Bayar'
						},
						subtitle: {
							text: '(Murni + Lanjutan)'
						}})
					);
				});
			}
			
			
		});
	</script>
</body>

</html> 