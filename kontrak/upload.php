<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/methods.js"></script>
</head>

<body>
	<?php
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) { 
			$bname = 'Internet Explorer'; 
			$ub = "MSIE"; 
		} 
		elseif(preg_match('/Firefox/i',$u_agent)) { 
			$bname = 'Mozilla Firefox'; 
			$ub = "Firefox"; 
		} 
		elseif(preg_match('/Chrome/i',$u_agent)) { 
			$bname = 'Google Chrome'; 
			$ub = "Chrome"; 
		} 
		elseif(preg_match('/Safari/i',$u_agent)) { 
			$bname = 'Apple Safari'; 
			$ub = "Safari"; 
		} 
		elseif(preg_match('/Opera/i',$u_agent)) { 
			$bname = 'Opera'; 
			$ub = "Opera"; 
		} 
		elseif(preg_match('/Netscape/i',$u_agent)) { 
			$bname = 'Netscape'; 
			$ub = "Netscape"; 
		} 
		$nice = (($ub=="Chrome" || $ub=="Opera" || $ub=="Chrome")? true: false);
	
		session_start(); 
		require_once '../config/koneksi.php';
		$nip=$_SESSION['nip'];
		$bidang=$_SESSION['bidang'];
		$kdunit=$_SESSION['kdunit'];
		$nama=$_SESSION['nama'];
		$adm=$_SESSION['adm'];
		$org=$_SESSION['org'];
		if($nip=="") {exit;}

		$sql = "SELECT * FROM kontrak_type ORDER BY nama ASC";
		//echo "$sql";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		$kontrak_type = "";
		while ($row = mysqli_fetch_array($result)) {
			$kontrak_type .= "<tr><td>".ltrim($row[id], '0')."</td><td>$row[nama]</td></tr>";
		}
		
		mysqli_free_result($result);
		 			

		echo "
			<h2>Upload Kontrak</h2>
			<form method='post' enctype='multipart/form-data' action='simpan_upload.php'>
				<table border='1'>
					<tr>
						<td>Upload Kontrak </td>
						<td>&nbsp;:&nbsp;</td>
						<td><input type='file' name='upload' id='upload'></td>
					</tr>
					<tr>
						<td colspan='2'></td>
						<td><a href='../templatekontrak.xlsx'>Download Format</a></td>
					</tr>
					<tr>
						<td colspan='3' align='right'>
							<input type='submit' value='Upload'>&nbsp;
							<input type='button' value='Cancel' onclick='window.open(\"index.php\", \"_self\")'>
						</td>
					</tr>
				</table>
			</form>
			<h2>Perhatian</h2>
			<ul>
				<li>Sebelum meng-upload data kontrak pastikan untuk men-download template terlebih dahulu.</li>

				<li>Untuk kolom <span style=\"font-weight: bold;\">Nomor Kontrak</span>, sistem akan menambahkan label <span style=\"font-weight: bold;\">-h</span> di akhir kontrak. Contoh: Nomor kontrak <span style=\"font-weight: bold;\">123/HKM.00.01/WSU/2019</span> menjadi <span style=\"font-weight: bold;\">123/HKM.00.01/WSU/2019-h</span>.</li>

				<li>Untuk kolom <span style=\"font-weight: bold;\">Tgl Bayar</span> hanya khusus diisi jika kontraknya ingin langsung masuk ke <span style=\"font-weight: bold;\">Realisasi Bayar</span>. Kosongkan jika tidak ingin masuk ke <span style=\"font-weight: bold;\">Realisasi Bayar</span>.</li>

				<li>Untuk kolom <span style=\"font-weight: bold;\">Nomor RAB</span> hanya khusus untuk kontrak <span style=\"font-weight: bold;\">Investasi</span> yang memiliki RAB dan boleh kosong.</li>

				<li>Untuk kolom <span style=\"font-weight: bold;\">Jenis Rutin</span> hanya khusus untuk kontrak <span style=\"font-weight: bold;\">Operasi</span> dan jika kosong maka secara otomatis akan menjadi tagihan <span style=\"font-weight: bold;\">Non Rutin Lain - lain</span>.</li>

				<li>
					Kolom <span style=\"font-weight: bold;\">Jenis Rutin</span> cukup mengisi kodenya saja. untuk kode jenis rutin adalah sebagai berikut:

					<table>
						<tr>
							<th>KODE</th>
							<th>JENIS RUTIN</th>
						</tr>
						$kontrak_type
					</table>
				</li>
			</ul>
		";
	?>
</body>
</html>