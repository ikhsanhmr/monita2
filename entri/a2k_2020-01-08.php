<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function viewk(me) {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var k = document.getElementById("skk").value;
			var c = document.getElementById("kon").value;
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}
			var url = encodeURI((me==undefined? "a2k.php": "a2kxl.php") + "?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&k="+k+"&c="+c+"&v=1");
			window.open(url, "_self"); 
		}		
	</script>
	
	<?php
		session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		
		require_once "../config/koneksi.php";
		$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
		$result = mysql_query($sql);
		
		$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
		$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
		$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
		$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
		$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
		$c = isset($_REQUEST["c"])? $_REQUEST["c"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

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
	?>
</head>


<body>
	<?php
		$parm = "";
		$parm .= ($p1==""? "": " and YEAR(tanggalskki) = " . substr($p1,0,4) . " AND MONTH(tanggalskki) >= " . substr($p1,-2));
		$parm .= ($p2==""? "": " and YEAR(tanggalskki) = " . substr($p2,0,4) . " AND MONTH(tanggalskki) <= " . substr($p2,-2));

		$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		$parm .= ($k0==""? "": " and skk = '$k0'");
		$parm .= ($c==""? "": " and nomorkontrak = '$c'");
		
		echo "
			<h2>Data Kontrak Untuk Upload A2K</h2>
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
					<th>No Kontrak</th>
					<td>:</td>
					<td><input type='text' name='kon' id='kon' size='49' value='$c'></td>
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
				SELECT 	nomorskki, nomorprk, nomorkontrak, nilaikontrak, vendor, k.uraian, tglawal, tglakhir, namaunit, 
						nick2, inputdt, kodeunit
				FROM	kontrak k INNER JOIN
						skkiterbit skk ON k.nomorskkoi = skk.nomorskki INNER JOIN
						notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 INNER JOIN 
						bidang b ON d.pelaksana = b.id
				Where	1=1
				$parm
				ORDER BY nomorskki, LPAD(pelaksana, 2, '0'), k.pos, nomorkontrak
			";
			// echo $sql;
			// return;
			//echo $parm;
			
			
			$no = 0;
			$parm = "";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$no++;
				
				$kdfungsi = substr($row['nomorprk'],-5);
				$noskki = substr($row['nomorskki'],0,3);
				$nokontrak = $row['nick2'].".".$kdfungsi.".".$noskki.".".$row['kodeunit'].".".$row['nomorkontrak'];
				$noski = substr($row['nomorprk'],0,12);
				$noproyek = substr($row['nomorprk'],-15);

				$parm .= "
					<tr>
						<td></td>
						<td>$nokontrak</td>
						<td>$noski</td>
						<td>$noproyek</td>
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td>$row[vendor]</td>
						<td>$row[uraian]</td>
						<td>$row[tglawal]</td>
						<td>$row[tglakhir]</td>
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td>$row[inputdt]</td>
					</tr>";
					//min='0' max='$dummy' 
			}
			mysql_free_result($result);
			
			echo "
				<table>
					<tr>
						<th>ID KONTRAK</th>
						<th>NO. KONTRAK</th>
						<th>NO. SKI</th>
						<th>NO. PROYEK</th>
						<th>NILAI KONTRAK YANG DIBIAYAI OLEH PROYEK (RP)</th>
						<th>NAMA VENDOR</th>
						<th>NAMA KONTRAK</th>
						<th>TGL. AWAL</th>
						<th>TGL. AKHIR</th>
						<th>NILAI KONTRAK (RP)</th>
						<th>TGL. ENTRY</th>
					</tr>
					$parm
				</table>";
		}
		mysql_close($kon);
	?>
</html>
