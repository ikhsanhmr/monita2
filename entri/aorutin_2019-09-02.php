<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function viewk(me) {
			var p1 = document.getElementById("p1").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var k = document.getElementById("skk").value;
			// var o = document.getElementById("o").value;
			//var c = document.getElementById("kon").value;
			
			
			var url = encodeURI((me==undefined? "aorutin.php": "aorutinxl.php") + "?p1="+p1+"&b="+b+"&p="+p+"&k="+k+"&v=1");
			window.open(url, "_self"); 
		}		
	</script>
	
	<?php
		error_reporting(0);  session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		
		require_once "../config/koneksi.php";
		$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
		$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
		$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
		$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
		// $o = isset($_REQUEST["o"])? $_REQUEST["o"]: "";
		$c = isset($_REQUEST["c"])? $_REQUEST["c"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		while ($row = mysqli_fetch_array($result)) {
			if($row["id"]<6) {
				$b .= ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5)?
					"<option value='$row[nick]' " . ($row["nick"]==$b0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[nick]' " . ($row["nick"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
			}
			$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
				"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
				($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
		}
		mysqli_free_result($result);
		$b .= "</select>";
		$p .= "</select>";
	?>
</head>


<body>
	<?php
		$parm = "";
		// $parm .= ($p1==""? "": " and YEAR(inputdt) = " . substr($p1,0,4));
		// $parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		$parm .= ($b0==""? "": " and (nipuser = '$b0' or b.nick = '$b0')");
		$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		$parm .= ($k0==""? "": " and skk = '$k0'");
		$parm .= ($o==""? "": " and skkoi = '$o'");
        // $parm .= ($c==""? "": " and nomorkontrak = '$c'");
        
        $t = "<select name='p1' id='p1'>";
		for($i=2015; $i<=date("Y"); $i++) {
			$t .= "<option value='$i'". ($i==$p1? " selected": "") .">$i</option>";
		}
		$t .= "</select>";
		
		echo "
			<h2>Data Kontrak Rutin Wilayah Sumatera Utara</h2>
			<table>
				<tr>
					<th>Periode (yyyy)</th>
					<td>:</td>
					<td>$t</td>
				</tr>
				<tr>
					<th>Bidang</th>
					<td>:</td>
					<td>$b</td>
				</tr>
				<tr>
					<th>Pelaksana</th>
					<td>:</td>
					<td>$p</td>
				</tr>
				<tr>
					<th>No SKK</th>
					<td>:</td>
					<td><input type='text' name='skk' id='skk' size='49' value='$k0'></td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' value='Ok' onclick='viewk()'>
						<input type='button' value='Excel' onclick='viewk(1)'>
					</td>
				</tr>
			</table>		
		";
		
		if($v!="") {

			$sql = "
					SELECT	id, nama, SUM(nilai_jan) AS nilai_jan, SUM(nilai_feb) AS nilai_feb, SUM(nilai_mar) AS nilai_mar, SUM(nilai_apr) AS nilai_apr, 
							SUM(nilai_mei) AS nilai_mei, SUM(nilai_jun) AS nilai_jun, SUM(nilai_jul) AS nilai_jul, SUM(nilai_ags) AS nilai_ags, 
							SUM(nilai_sep) AS nilai_sep, SUM(nilai_okt) AS nilai_okt, SUM(nilai_nov) AS nilai_nov, SUM(nilai_des) AS nilai_des, 
							SUM(nilai_utang) AS nilai_utang, SUM(bayar_jan) AS bayar_jan, SUM(bayar_feb) AS bayar_feb, SUM(bayar_mar) AS bayar_mar, 
							SUM(bayar_apr) AS bayar_apr, SUM(bayar_mei) AS bayar_mei, SUM(bayar_jun) AS bayar_jun, SUM(bayar_jul) AS bayar_jul, 
							SUM(bayar_ags) AS bayar_ags, SUM(bayar_sep) AS bayar_sep, SUM(bayar_okt) AS bayar_okt, SUM(bayar_nov) AS bayar_nov, 
							SUM(bayar_des) AS bayar_des, SUM(bayar_utang) AS bayar_utang
                    FROM 	(
								SELECT	a.*, MONTH(tgltagih), YEAR(tgltagih), 
										(CASE WHEN MONTH(tgltagih) = 1 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_jan,
										(CASE WHEN MONTH(tgltagih) = 2 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_feb, 
										(CASE WHEN MONTH(tgltagih) = 3 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_mar,
										(CASE WHEN MONTH(tgltagih) = 4 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_apr, 
										(CASE WHEN MONTH(tgltagih) = 5 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_mei,
										(CASE WHEN MONTH(tgltagih) = 6 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_jun, 
										(CASE WHEN MONTH(tgltagih) = 7 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_jul,
										(CASE WHEN MONTH(tgltagih) = 8 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_ags, 
										(CASE WHEN MONTH(tgltagih) = 9 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_sep,
										(CASE WHEN MONTH(tgltagih) = 10 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_okt, 
										(CASE WHEN MONTH(tgltagih) = 11 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_nov,
										(CASE WHEN MONTH(tgltagih) = 12 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_des,
										(CASE WHEN YEAR(tgltagih) = ".($p1 - 1)." THEN nilaikontrak ELSE 0 END) AS nilai_utang,
										(CASE WHEN MONTH(tgltagih) = 1 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_jan,
										(CASE WHEN MONTH(tgltagih) = 2 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_feb, 
										(CASE WHEN MONTH(tgltagih) = 3 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_mar,
										(CASE WHEN MONTH(tgltagih) = 4 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_apr, 
										(CASE WHEN MONTH(tgltagih) = 5 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_mei,
										(CASE WHEN MONTH(tgltagih) = 6 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_jun, 
										(CASE WHEN MONTH(tgltagih) = 7 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_jul,
										(CASE WHEN MONTH(tgltagih) = 8 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_ags, 
										(CASE WHEN MONTH(tgltagih) = 9 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_sep,
										(CASE WHEN MONTH(tgltagih) = 10 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_okt, 
										(CASE WHEN MONTH(tgltagih) = 11 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_nov,
										(CASE WHEN MONTH(tgltagih) = 12 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_des,
										(CASE WHEN YEAR(tgltagih) = ".($p1 - 1)." THEN nilaibayar ELSE 0 END) AS bayar_utang
								FROM	kontrak_type a INNER JOIN 
										kontrak b ON a.id = b.isrutin LEFT JOIN 
                                        realisasibayar c ON b.nomorkontrak = c.nokontrak LEFT JOIN
                                        notadinas_detail d ON b.nomorskkoi = d.noskk LEFT JOIN
                                        notadinas e ON d.nomornota = e.nomornota LEFT JOIN
                                        bidang f ON d.pelaksana = f.id
								WHERE	YEAR(b.inputdt) = $p1 and YEAR(tgltagih) IN (".($p1 - 1).", $p1) $parm
								GROUP BY a.id, a.nama, MONTH(tgltagih), YEAR(tgltagih)
								ORDER BY a.id, a.nama, YEAR(tgltagih), MONTH(tgltagih)
							) AS md
							GROUP BY id, nama";
			// echo $sql;
			// return;
			//echo $parm;
			
			$kontrak = 0;
			$bayar = 0;

			$no = 0;
			$parm = "";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			while ($row = mysqli_fetch_array($result)) {
				$no++;
				$kontrak += $row["kontrak"];
				$bayar += $row["bayar"];
				
				$parm .= "
					<tr>
						<td>$no</td>
						<td>$row[nama]</td>
						<td align='right'>".number_format($row["nilai_utang"])."</td>
						<td align='right'>".number_format($row["bayar_utang"])."</td>
						<td align='right'>".number_format($row["nilai_jan"])."</td>
						<td align='right'>".number_format($row["bayar_jan"])."</td>
						<td align='right'>".number_format($row["nilai_feb"])."</td>
						<td align='right'>".number_format($row["bayar_feb"])."</td>
						<td align='right'>".number_format($row["nilai_mar"])."</td>
						<td align='right'>".number_format($row["bayar_mar"])."</td>
						<td align='right'>".number_format($row["nilai_apr"])."</td>
						<td align='right'>".number_format($row["bayar_apr"])."</td>
						<td align='right'>".number_format($row["nilai_mei"])."</td>
						<td align='right'>".number_format($row["bayar_mei"])."</td>
						<td align='right'>".number_format($row["nilai_jun"])."</td>
						<td align='right'>".number_format($row["bayar_jun"])."</td>
						<td align='right'>".number_format($row["nilai_jul"])."</td>
						<td align='right'>".number_format($row["bayar_jul"])."</td>
						<td align='right'>".number_format($row["nilai_ags"])."</td>
						<td align='right'>".number_format($row["bayar_ags"])."</td>
						<td align='right'>".number_format($row["nilai_sep"])."</td>
						<td align='right'>".number_format($row["bayar_sep"])."</td>
						<td align='right'>".number_format($row["nilai_okt"])."</td>
						<td align='right'>".number_format($row["bayar_okt"])."</td>
						<td align='right'>".number_format($row["nilai_nov"])."</td>
						<td align='right'>".number_format($row["bayar_nov"])."</td>
						<td align='right'>".number_format($row["nilai_des"])."</td>
						<td align='right'>".number_format($row["bayar_des"])."</td>
					</tr>";
			}
			mysqli_free_result($result);
			
			echo "
				<table>
			";
			

				echo "
					<tr>
						<th rowspan='3'>No</th>
						<th rowspan='3'>Jenis Tagihan</th>
						<th colspan='28'>Bulan Tagih</th>
					</tr>
					<tr>
						<th colspan='2'>Utang</th>
						<th colspan='2'>Januari</th>
						<th colspan='2'>Februari</th>
						<th colspan='2'>Maret</th>
						<th colspan='2'>April</th>
						<th colspan='2'>Mei</th>
						<th colspan='2'>Juni</th>
						<th colspan='2'>Juli</th>
						<th colspan='2'>Agustus</th>
						<th colspan='2'>September</th>
						<th colspan='2'>Oktober</th>
						<th colspan='2'>November</th>
						<th colspan='2'>Desember</th>
					</tr>
					<tr>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
					</tr>
				";
			
			echo "
				$parm
			</table>";
		}
		$mysqli->close();($kon);
	?>
</html>
