<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function viewk(parm) {
			var k = document.getElementById("skk").value;
			var url = encodeURI("index.php?k="+k+"&v="+(parm==undefined? "1": ""));
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
		session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		
		require_once "../config/koneksi.php";
		$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	?>
</head>


<body>
	<?php
		echo "
			<h2>Revisi SKK Terbit</h2>
			<table>
				<tr>
					<th>No SKK</th>
					<td>:</td>
					<td><input type='text' name='skk' id='skk' size='50' value='$k0'></td>
				</tr>
				<tr>
					<td colspan='3' align='right'><input type='button' value='Ok' onclick='viewk()'></td>
				</tr>
			</table>		
		";
		
		if($v!="") {
			$sql = "
				SELECT 
					nomornota, pelaksana, pos1, nilai1,
					namaunit, 
					s.* 
				FROM notadinas_detail d
				LEFT JOIN bidang b ON d.pelaksana = b.id 
				INNER JOIN (
					SELECT 'SKKO' jenis, nomorskko noskk, nomorcostcenter nom, nomorwbs, 
						tanggalskko tglskk, uraian, nilaiwbs, nilaianggaran, nilaidisburse, nilaitunai, nilainontunai
					FROM skkoterbit UNION
					SELECT 'SKKI' jenis, nomorskki noskk, nomorprk nom, nomorwbs, 
						tanggalskki tglskk, uraian, nilaiwbs, nilaianggaran, nilaidisburse, nilaitunai, nilainontunai 
					FROM skkiterbit
				) s ON d.noskk = s.noskk
				WHERE d.noskk = '$k0'
				ORDER BY pos1, pelaksana";
			
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			if(mysql_num_rows($result)>0) {
				$sql = "select * from bidang";
				$hasil = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
				
				$no=0;
				$parm = "<tr><th>POS</th><th></th><th>Nilai</th></tr>";
				$jenis = "";
				while ($row = mysqli_fetch_array($result)) {
					$no++;
					$jenis = $row["jenis"];
					$nom = $row["nom"];
					$nowbs = $row["nomorwbs"];
					$tglskk = $row["tglskk"];
					$uraian = $row["uraian"];
					$ang = $row["nilaianggaran"];
					$dis = $row["nilaidisburse"];
					$wbs = $row["nilaiwbs"];
					$tunai = $row["nilaitunai"];
					$non = $row["nilainontunai"];
					$parm .= "
						<tr>
							<td><input readonly type='text' name='pos$no' id='pos$no' value='$row[pos1]'></td>
							<td>:</td>
							<td><input type='number' name='nilai$no' id='nilai$no' value='$row[nilai1]'></td>
						</tr>
						";
				}

				echo "
					<form name='rev' id='rev' method='post' action='simpan.php'>
					<table>
						<tr>
							<th>No SKK</th>
							<td>:</td>
							<td>
								<input readonly type='text' name='noskk' id='noskk' value='$k0' size='50'>
								<input type='hidden' name='jenis' id='jenis' value='$jenis'>
							</td>
						</tr>
						<tr>
							<th>Tgl Skk</th>
							<td>:</td>
							<td><input type='date' name='tglskk' id='tglskk' value='$tglskk'></td>
						</tr>
						<tr>
							<th>Uraian</th>
							<td>:</td>
							<td><textarea rows='3' cols='100' name='uraian' id='uraian'>$uraian</textarea></td>
						</tr>
						<tr>
							<th>" . ($jenis=="SKKO"? "Cost Center": "PRK") . "</th>
							<td>:</td>
							<td><input type='text' name='nom' id='nom' value='$nom' size='100'></td>
						</tr>
						<tr>
							<th>No WBS</th>
							<td>:</td>
							<td><input type='text' name='nowbs' id='nowbs' value='$nowbs' size='100'></td>
						</tr>
						<tr>
							<th>Nilai WBS</th>
							<td>:</td>
							<td><input type='number' name='wbs' id='wbs' value='$wbs' size='50'></td>
						</tr>
						<tr>
							<th>Anggaran</th>
							<td>:</td>
							<td><input type='number' name='anggaran' id='anggaran' value='$ang' size='50'></td>
						</tr>
						<tr>
							<th>Disburse</th>
							<td>:</td>
							<td><input type='number' name='disburse' id='disburse' value='$dis' size='50'></td>
						</tr>
						<tr>
							<th>Tunai</th>
							<td>:</td>
							<td><input type='number' name='tunai' id='tunai' value='$tunai' size='50'></td>
						</tr>
						<tr>
							<th>Non Tunai</th>
							<td>:</td>
							<td><input type='number' name='nontunai' id='nontunai' value='$non' size='50'></td>
						</tr>
						$parm
						<tr>
							<td colspan='3' align='right'>
								<input type='submit' value='Simpan'>
								<input type='button' value='Batal' onClick='viewk(0)'>
							</td>
						</tr>
					</table>
					</form>";
			}
			mysqli_free_result($result);
		}
		$mysqli->close();($kon);
	?>
</html>
