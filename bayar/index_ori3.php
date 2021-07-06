<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="../jquery.dataTables.min.css">
	<link href="../css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" >
	
	<script type="text/javascript">
		function viewk() {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var k = document.getElementById("skk").value;
			var o = document.getElementById("o").value;
			var c = document.getElementById("kon").value;
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}
			var url = encodeURI("index.php?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&k="+k+"&o="+o+"&c="+c+"&v=1");
			window.open(url, "_self"); 
		}
		
		function validateForm(me) {
			var dummy;
			var rtn = true;
			
			for(var i=0; i<document.getElementById(me).elements.length; i++) {
				if(document.getElementById(me).elements[i].id.substr(0,1)=="n") {
					dummy = document.getElementById(me).elements[i].id.substr(1, document.getElementById(me).elements[i].id.length);
					rtn = (valuecheck(dummy)==true? rtn: false);
				}
			}
			return rtn;
		}
		
		function valuecheck(me) {
			//alert(me + "\n" + document.getElementById("n"+me).value + "\n" + document.getElementById("s"+me).value + "\n" + (document.getElementById("n"+me).value>document.getElementById("s"+me).value));
			var dummy = true;
			document.getElementById("n"+me).style.borderColor = "grey";
			
			if(parseInt(document.getElementById("n"+me).value) > parseInt(document.getElementById("s"+me).value)) {
				document.getElementById("n"+me).style.borderColor = "red";
				dummy = false;
			}
			return dummy;
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
		$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
		$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
		$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
		$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
		$o = isset($_REQUEST["o"])? $_REQUEST["o"]: "";
		$c = isset($_REQUEST["c"])? $_REQUEST["c"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		
		$url = "index.php?p1=$p1&p2=$p2&b=$b0&p=$p0&k=$k0&o=$o&c=$c&v=1";
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		while ($row = mysqli_fetch_array($result)) {
			if($row["id"]<6) {
				$b .= ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5)?
					"<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
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
		$parm .= ($p1==""? "": " and SUBSTR(tglawal, 1, 7) >= '$p1'");
		$parm .= ($p2==""? "": " and SUBSTR(tglakhir, 1, 7) <= '$p2'");
		$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		$parm .= ($k0==""? "": " and skk = '$k0'");
		$parm .= ($o==""? "": " and skkoi = '$o'");
		$parm .= ($c==""? "": " and nomorkontrak = '$c'");
		
		echo "
			<h2>Realisasi Bayar Kontrak</h2>
			<table>
				<tr>
					<th>Periode (yyyy-mm)</th>
					<td>:</td>
					<td><input type='month' name='p1' id='p1' value='$p1'> - <input type='month' name='p2' id='p2' value='$p2'></td>
				</tr>
				<tr>
					<th>Jenis</th>
					<td>:</td>
					<td>
						<select name='o' id='o'>
							<option value=''></option>
							<option value='SKKO'" . ($o=="SKKO"? "selected": "") . ">SKKO</option>
							<option value='SKKI'" . ($o=="SKKI"? "selected": "") . ">SKKI</option>
						</select>
					</td>
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
					<td colspan='3' align='right'><input type='button' value='Ok' onclick='viewk()'></td>
				</tr>
			</table>		
		";
		
		if($v!="") {
			$sql = "
				SELECT 
					skkoi, n.nomornota nd, nip, nipuser, pelaksana, g.id nick, pos1, namapos, b.namaunit, skk, oi.uraian oiuraian, 
					nomorkontrak, k.pos kpos, k.uraian kuraian, vendor, tglawal, tglakhir, nilaikontrak, bayar,nodokumen,coalesce(ke,0) ke, SIGNED sgd
				FROM notadinas n
				LEFT JOIN bidang g ON n.nipuser = g.nick 
				LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota 
				LEFT JOIN bidang b ON d.pelaksana = b.id 
				LEFT JOIN (
					SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
				) p ON d.pos1 = p.pos
				LEFT JOIN (
					SELECT nomorskko skk, uraian FROM skkoterbit UNION
					SELECT nomorskki skk, uraian FROM skkiterbit
				) oi ON d.noskk = oi.skk
				LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos 
				LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar, count(*) ke FROM realisasibayar GROUP BY nokontrak) r ON k.nomorkontrak = r.nokontrak
				WHERE n.progress >= 7
				AND bayar IS NULL
					
					$parm
				ORDER BY nd, skk, nomorkontrak, pos1";
			//echo $sql;
			
			$no = 0;
			$parm = "";
			//$dummy = 0;
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			while ($row = mysqli_fetch_array($result)) {
				$no++;
				$dummy = $row["nilaikontrak"]-$row["bayar"];
				$dummy !="";
				$parm .= "
					<tr>
						<td>$no</td>
						<td>$row[namaunit]</td>
						<td>$row[pos1]</td>
						<td>$row[skk]</td>
						<td>$row[nomorkontrak]" . ($dummy>0? "<input type='hidden' name='k$no' id='k$no' value='$row[nomorkontrak]'>": "") . "</td>
						<td>$row[kuraian]</td>
						<td>$row[vendor]</td>
						<td>$row[tglawal]</td>
						<td>$row[tglakhir]</td>
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td align='right'>".number_format($dummy). ($dummy >0? "<input type='hidden' name='s$no' id='s$no' value='$dummy'>": "") . "</td>
						<td>$row[nodokumen]</td>
						<td>" . ($dummy >0? ($row["ke"] + 1): "") . "</td>
						<td>" . ($dummy >0? "<input type='date' name='d$no' id='d$no' value='" . date("Y-m-d") . "'>": "") . "</td>
						<td>" . ($dummy >0? "<input type='number' name='n$no' id='n$no' onblur='valuecheck($no)'>": "") . "</td>
						<td>" . ($dummy >0? "<input type='submit' value='Simpan'>": "") . "</td>
					</tr>";
					//min='0' max='$dummy' 
				$dummy = $row["skk"];
			}
			mysqli_free_result($result);
			
			echo "
				<form name='frm' id='frm' method='post' action='simpan.php' onsubmit='return validateForm(this.id)'>
				<input type='hidden' name='url' id='url' value='$url'>
					<table id='dataTables' class='display' cellspacing='0' width='100%'>
						<thead>
						<tr>
							<th rowspan='2'>No</th>
							<th rowspan='2'>Pelaksana</th>
							<th rowspan='2' rowspan='2'>Pos</th>
							<th rowspan='2'>No SKK</th>
							<th colspan='8'>Kontrak</th>
							<th colspan='4'>Pembayaran</th>
						</tr>
						<tr>
							<th>Nomor</th>
							<th>Uraian</th>
							<th>Vendor</th>
							<th>Tgl Awal</th>
							<th>Tgl Akhir</th>
							<th>Nilai</th>
							<th>Sisa</th>
							<th>NoDokumen</th>
							<th>Ke</th>
							<th>Tgl Bayar(MM/DD/YYYY)</th>
							<th>Nilai</th>
							<th>Proses</th>
						</tr>
						</thead>
						$parm
					</table>
				</form>";
		}
		$mysqli->close();($kon);
	?>
	
	</body>
	
	<script src="../js/jquery-1.12.0.min.js"></script>
	<script src="../js/jquery.dataTables.min.js"></script>
	<script>
	$(document).ready(function() {
		$('#dataTables').DataTable();
	} );
	</script>
</html>
