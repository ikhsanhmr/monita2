<?php
    session_start(); 
    require_once '../config/koneksi.php';

	$k = (isset($_REQUEST["k"])? $_REQUEST["k"]: "");
	if($k!="") {
		$sql = "
			SELECT uraian, vendor, tglawal, tglakhir, nilaikontrak, p.* 
			FROM kontrak k
			LEFT JOIN asetpdp p ON k.nomorkontrak = p.nomorkontrak
			WHERE trim(k.nomorkontrak) = '$k'";

		// echo $sql;
		// return;
		$result = mysql_query($sql);
		
		$dummy = 0;
		$ada = false;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) { 
			$dummy = ($row["pdpid"]==null? 0: 1);
			$ada = ($row["nilaikontrak"]==null? false: true);
			$nilai = $row["nilaikontrak"];
			$uraian = $row["uraian"];
			$awal = $row["tglawal"];
			$akhir = $row["tglakhir"];

			$jtm = $row["jtmaset"];
			$jtmr = $row["jtmrp"];
			$gd = $row["gdaset"];
			$gdr = $row["gdrp"];
			$jtr = $row["jtraset"];
			$jtrr = $row["jtrrp"];
			$sl1 = $row["sl1aset"];
			$sl1r = $row["sl1rp"];
			$sl3 = $row["sl3aset"];
			$sl3r = $row["sl3rp"];
			$kp = $row["keypointaset"];
			$kpr = $row["keypointrp"];
		}
		mysql_free_result($result);
		mysql_close($kon);	  
		
		echo "
			<table border='1'>
				<tr>
					<th>Nilai Kontrak</th>
					<td>:</td>
					<td colspan='2'>" . number_format($nilai) . "</td>
				</tr>
				<tr>
					<th>Uraian</th>
					<td>:</td>
					<td colspan='2'>$uraian</td>
				</tr>
				<tr>
					<th>Tgl Awal</th>
					<td>:</td>
					<td colspan='2'>$awal</td>
				</tr>
				<tr>
					<th>Tgl Akhir</th>
					<td>:</td>
					<td colspan='2'>$akhir</td>
				</tr>
				<tr>
					<th>Upload Dokumen</th>
					<td>:</td>
					<td colspan='2'><input type='file' name='uploadpdp' id='uploadpdp' accept='application/pdf' /></td>
				</tr>
			</table>" . ($ada? "
			<table>
				<tr>
					<th>Uraian</th>
					<th colspan='2'>Aset</th>
					<th colspan='2'>Nilai Aset</th>
				</tr>
				<tr>
					<td>JTM</td>
					<td><input type='text' name='jtm' id='jtm' value='$jtm'></td>
					<td>Kms</td>
					<td>Rp.</td>
					<td><input type='text' name='jtmr' id='jtmr' value='$jtmr'></td>
				</tr>
				<tr>
					<td>GD</td>
					<td><input type='text' name='gd' id='gd' value='$gd'></td>
					<td>Unit KVA</td>
					<td>Rp.</td>
					<td><input type='text' name='gdr' id='gdr' value='$gdr'></td>
				</tr>
				<tr>
					<td>JTR</td>
					<td><input type='text' name='jtr' id='jtr' value='$jtr'></td>
					<td>Kms</td>
					<td>Rp.</td>
					<td><input type='text' name='jtrr' id='jtrr' value='$jtrr'></td>
				</tr>
				<tr>
					<td>SL 1 Phasa</td>
					<td><input type='text' name='sl1' id='sl1' value='$sl1'></td>
					<td>Plgn</td>
					<td>Rp.</td>
					<td><input type='text' name='sl1r' id='sl1r' value='$sl1r'></td>
				</tr>
				<tr>
					<td>SL 3 Phasa</td>
					<td><input type='text' name='sl3' id='sl3' value='$sl3'></td>
					<td>Plgn</td>
					<td>Rp.</td>
					<td><input type='text' name='sl3r' id='sl3r' value='$sl3r'></td>
				</tr>
				<tr>
					<td>Key Point</td>
					<td><input type='text' name='kp' id='kp' value='$kp'></td>
					<td>Plgn</td>
					<td>Rp.</td>
					<td><input type='text' name='kpr' id='kpr' value='$kpr'></td>
				</tr>
				<tr>
					<td colspan='5' align='right'>
						<input type='hidden' name='edit' id='edit' value='$dummy'>
						<input type='button' value='Cancel' onclick=\"window.open('.', '_self')\">
						<input type='submit' value='Save'>
					</td>
				</tr>
			<table>": "");
	}
?>