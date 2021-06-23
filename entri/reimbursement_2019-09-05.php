<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
	<link href="../css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" >
	<style type="text/css">
		#signButton{
			background: transparent;
			border: none;
			cursor: pointer;
		}
	</style>
	<script type="text/javascript">
		function viewk(x) {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var k = document.getElementById("skk").value;
			var kpos = document.getElementById("kdpos").value;
			//alert(kpos);
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}
			var url = encodeURI((x==undefined? "reimbursement.php": "reimexcel.php") + "?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&k="+k+"&kpos="+kpos+"&v=1");
			//alert(url);
			window.open(url, "_self");
		}

	</script>
	
	<?php
//		header("Content-type: application/vnd.ms-excell");
//		header("Content-Disposition: attachment; Filename=ao.xls");

		session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		
		$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
		$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
		$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
		$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
		$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
		$kdpos0 = isset($_REQUEST["kpos"])? $_REQUEST["kpos"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		require_once "../config/koneksi.php";
		$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if($row["id"]<6) {
				$b .= ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5)?
					"<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
			}
			$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
				"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
				($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
		}
		mysql_free_result($result);
		$b .= "</select>";
		$p .= "</select>";
		
		$sql = "
			SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			ORDER BY pos";
		$result = mysql_query($sql);
		
		$kdpos = "<select name='kdpos' id='kdpos'><option value=''></option>";
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$kdpos .= "<option value='$row[pos]' " . ($row["pos"]==$kdpos0? "selected": "") . ">$row[pos] - $row[namapos]</option>";
		}
		$kdpos .= "</select>";
		mysql_free_result($result);
	?>
</head>

<body>
	<?php
		$parm = "";
//		$parm .= ($p1==""? "": " and SUBSTR(tanggalskki, 1, 7) >= '$p1'");
//		$parm .= ($p2==""? "": " and SUBSTR(tanggalskki, 1, 7) <= '$p2'");
		$parm .= ($p1==""? "": " and YEAR(tglbayar) = " . substr($p1,0,4) . " AND MONTH(tglbayar) >= " . substr($p1,-2));
		$parm .= ($p2==""? "": " and YEAR(tglbayar) = " . substr($p2,0,4) . " AND MONTH(tglbayar) <= " . substr($p2,-2));

		$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		$parm .= ($k0==""? "": " and nomorskki = '$k0'");
		$parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");
		//echo "parm : $parm<br>";
		echo "
			<h2> Laporan Reimbursement</h2>
			<table>
				<tr>
					<th>Periode (yyyy-mm)</th>
					<td>:</td>
					<td><input type='month' name='p1' id='p1' value='$p1'> - <input type='month' name='p2' id='p2' value='$p2'></td>
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
					<th>POS</th>
					<td>:</td>
					<td>$kdpos</td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' value='View' onclick='viewk()'>
						<input type='button' value='Excel' onclick='viewk(1)'>
					</td>
				</tr>
			</table>";
		
		if($v!="") {
			$sql = "
				SELECT	nomorprk, nomorscore, nomorskki noskk, nilaianggaran anggaran, nilaidisburse disburse, 
						tanggalskki, b.namaunit, namapos, nomorkontrak, k.vendor, k.uraian uraiank, tglawal, tglakhir, 
						nilaikontrak, nilaibayar, tglbayar, pmn, COALESCE(nodokumen, nodokrep) as nodokumen,
						(Select nilaitengah From kurs_dollar kd where kd.tanggal <= r.tglbayar Order by tanggal DESC Limit 1) as nilaikurs
				FROM 	notadinas n LEFT JOIN 
						bidang g ON n.nipuser = g.nick LEFT JOIN 
						notadinas_detail d ON n.nomornota = d.nomornota LEFT JOIN 
						bidang b ON d.pelaksana = b.id LEFT JOIN 
						skkiterbit s ON d.noskk = s.nomorskki LEFT JOIN 
						(
							SELECT 	kdindukpos pos, namaindukpos namapos FROM posinduk UNION
							SELECT 	kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
							SELECT 	kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
							SELECT 	kdsubpos pos, namasubpos namapos FROM posinduk4
						) p ON d.pos1 = p.pos LEFT JOIN 
						kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos LEFT JOIN 
						realisasibayar r ON k.nomorkontrak = r.nokontrak
				WHERE d.progress >= 7 AND NOT nomorskki IS NULL 
				$parm
				ORDER BY nomorskki, LPAD(pelaksana, 2, '0'), k.pos, nomorkontrak";
			
			// print_r($_SESSION);
			// echo $sql;
			// return;
			//$hasil = "
			echo "
			<table border='1' id='dataTables'  cellspacing='0' width='100%'>
			<thead>
				<tr>
					<th rowspan='2' scope='col'>No</th>
					<th colspan='2' scope='col'>Program Rencana Kerja</th>
					<th rowspan='2' scope='col'>Nomor SKKI</th>
					<th rowspan='2' scope='col'>Nilai SKKI</th>
					<th rowspan='2' scope='col'>Tanggal Terbit</th>
					<th rowspan='2' scope='col'>PLN Unit/Pelaksana</th>
					<th rowspan='2' scope='col'>Jenis</th>
					<th colspan='6' scope='col'>Kontrak Pekerjaan</th>
					<th colspan='2' scope='col'>Pembayaran</th>
					<th colspan='3' scope='col'>Equivalent Currency</th>
					<th rowspan='2' scope='col'>PMN/NON PMN</th>
				</tr>
				<tr>
					<td align='center' style='background-color:rgb(127,255,127)'>Nomor PRK</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Fungsi/Basket</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nomor Kontrak</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nama Rekanan</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Uraian Pekerjaan</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Tgl Mulai</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Tgl Selesai</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nilai Kontrak</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nilai</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Tanggal</td>
					<td align='center' style='background-color:rgb(127,255,127)'>No. SAP</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Kusr SAP (Pada tanggal pembayaran)</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Equivalent Kurs (USD)</td>
				</tr>
				<tr>
					<td align='center'>1</td>
					<td align='center'>2</td>
					<td align='center'>3</td>
					<td align='center'>4</td>
					<td align='center'>5</td>
					<td align='center'>6</td>
					<td align='center'>7</td>
					<td align='center'>8</td>
					<td align='center'>9</td>
					<td align='center'>10</td>
					<td align='center'>11</td>
					<td align='center'>12</td>
					<td align='center'>13</td>
					<td align='center'>14</td>
					<td align='center'>15</td>
					<td align='center'>16</td>
					<td align='center'>17</td>
					<td align='center'>18</td>
					<td align='center'>19</td>
					<td align='center'>20</td>
				</tr>
			</thead>
			<tbody>";
			$result = mysql_query($sql);
			
			$no = 0;
			$totalkontrak = 0;
			$totalbayar = 0;
			$totalconvertion = 0;
			
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$no++;

				if (!empty($row["nilaikurs"])){
					$nilaisetelahkurs = round(($row["nilaibayar"]/$row["nilaikurs"]));
				}
				
				$totalkontrak += $row["nilaikontrak"];
				$totalbayar += $row["nilaibayar"];
				$totalconvertion += $nilaisetelahkurs;

				echo "
					<tr>
						<td>$no</td>
						<td>$row[nomorprk]</td>
						<td>$row[nomorscore]</td>
						<td>$row[noskk]</td>
						<td align='right'>".number_format($row["anggaran"])."</td>
						<td>$row[tanggalskki]</td>
						<td>$row[namaunit]</td>
						<td>$row[namapos]</td>
						<td>$row[nomorkontrak]</td>
						<td>$row[vendor]</td>
						<td>$row[uraiank]</td>
						<td>$row[tglawal]</td>
						<td>$row[tglakhir]</td>
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td align='right'>".number_format($row["nilaibayar"])."</td>
						<td>$row[tglbayar]</td>
						<td>$row[nodokumen]</td>
						<td align='right'>".number_format($row["nilaikurs"])."</td>
						<td align='right'>".number_format($nilaisetelahkurs)."</td>
						<td>$row[pmn]</td>
					</tr>
				";

				
			}
			mysql_free_result($result);
			
			
			
			//$hasil .= "</table>";
			echo "
				<tfoot>
				<tr>
					<td colspan='13'>Total</td>
					<td align='right'>" . number_format($totalkontrak) . "</td>
					<td align='right'>" . number_format($totalbayar) . "</td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'>" . number_format($totalconvertion) . "</td>
					<td></td>
				</tr>
				</tfoot>";
			  echo "</tbody>";	
			echo "</table>";

		}
		mysql_close($kon);
		
		//echo $hasil;
	?>
</body>
  <script type="text/javascript" src="../js/jquery-1.12.0.min.js"></script>
  <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
		$(document).ready(function() {
		$('#dataTables').DataTable(
		{
		  "ordering": false
		} 
		);
	} );
	</script>
</html>
