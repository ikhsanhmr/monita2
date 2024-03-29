<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/methods.js"></script>
	<script type="text/javascript">
		function validate() {
			var s = parseInt(document.getElementById("sisanya").value);
			var n = parseInt(document.getElementById("nilai").value);
			
			if(n>s) {
				alert("Nilai kontrak lebih besar dari sisa pagu.\nKontrak tidak bisa disimpan!");
				return false;
			}
		}
	</script>
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
	
		error_reporting(0);  session_start(); 
		require_once '../config/koneksi.php';
		$nip=$_SESSION['nip'];
		$bidang=$_SESSION['bidang'];
		$kdunit=$_SESSION['kdunit'];
		$nama=$_SESSION['nama'];
		$adm=$_SESSION['adm'];
		$org=$_SESSION['org'];
		if($nip=="") {exit;}
		$con = (isset($_REQUEST["kon"])? $_REQUEST["kon"]: "");
		

		//$sql = "SELECT DISTINCT noskk FROM notadinas_detail WHERE progress = 7 ORDER BY noskk";
		$sql = ($con==""? 
			//"select distinct noskk from notadinas n inner join notadinas_detail d ON n.nomornota = d.nomornota where d.progress = 7 " . ($adm>=1? "" : "AND (nipuser = '$nip' or pelaksana = '$org')") . " order by noskk":
			//"SELECT DISTINCT noskk FROM notadinas_detail WHERE progress = 7 " . ($org=="" || $rog=="1"? "": "and pelaksana = $org ") . "ORDER BY noskk":
			"SELECT DISTINCT noskk FROM notadinas_detail d left join notadinas n on d.nomornota = n.nomornota LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi WHERE d.progress = 7 AND k.nomorskkoi IS NULL " . ($org=="" || $rog=="1"? "": "AND (nipuser = '$nip' or pelaksana = '$org') ") . "ORDER BY noskk":
			"SELECT nomorskkoi noskk FROM kontrak WHERE nomorkontrak = '$con'"
		);
		//echo "$sql";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		$skk = "<select name='skk' id='skk' onChange='viewposskk(this.value, \"subpos\")' required><option value=''></option>";
		while ($row = mysqli_fetch_array($result)) {
			$skk .= "<option value='$row[noskk]'>$row[noskk]</option>";
		}
		$skk .= "</select>";
		
		mysqli_free_result($result);
		$mysqli->close();($kon);	  			

		echo "
			<h2>INPUT KONTRAK</h2>
			<form name='fkontrak' id='fkontrak' method='post' action='simpan.php' onsubmit='return validate()' ecntype='multipart/form-data'>
				<table border='1'>
					<tr>
						<td>Nomor SKKI/O <input type='hidden' name='edit' id='edit' value=''></td>
						<td>&nbsp;:&nbsp;</td>
						<td>$skk</td>
					</tr>
					<tr>
						<td>POS</td>
						<td>&nbsp;:&nbsp;</td>
						<td><div id='subpos'></div><div id='infopos'></div></td>
					</tr>
					<tr>
						<td>Nilai Kontrak</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' readonly type='text' name='nilai' id='nilai'></td>
					</tr>
					<tr>
						<td><div align='center'>Kontrak<br><input type='button' value='+' onClick='tambahKontrak()'></div></td>
						<td>&nbsp;:&nbsp;</td>
						<td>
							<div id='kontrak'>
								<table border='1'>
									<tr>
										<td colspan='3'><input type='checkbox' name='peti0' id='peti0'>Kontrak Petty Cash</td>
									</tr>
									<tr>
										<td>Nomor Kontrak</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input required size='49' type='text' name='nkontrak0' id='nkontrak0' required></td>
									</tr>
									<tr>
										<td>Uraian Kegitatan</td>
										<td>&nbsp;:&nbsp;</td>
										<td><textarea required rows='3' cols='47' name='uraian0' id='uraian0'></textarea></td>
									</tr>
									<tr>
										<td>Vendor</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input required size='49' type='text' name='vendor0' id='vendor0'></td>
									</tr>
									<tr>
										<td>Tanggal Awal - Akhir (MM/DD/YYYY)</td>
										<td>&nbsp;:&nbsp;</td>
										<td>
											<input required size='22' type='" . ($nice? "date": "text") . "' name='awal0' id='awal0' " . ($nice? "": "onChange='dateCheck(\"awal0\")' ") . "value=''> - 
											<input required size='22' type='" . ($nice? "date": "text") . "' name='akhir0' id='akhir0' " . ($nice? "": "onChange='dateCheck(\"akhir0\")' ") . "value=''>
										</td>
									</tr>
									<tr>
										<td>Nilai Kontrak</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input required size='49' type='text' name='nilai0' id='nilai0' onChange='totalkontrak()'></td>
									</tr>
									<tr>
										<td>Upload Kontrak</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input type='file' required name='uploadkontrak' id='uploadkontrak' accept='application/pdf' />></td>
									</tr>
									<tr>
										<td colspan='3'></td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='3' align='right'>
							<input type='submit' value='Simpan'>&nbsp;
							<input type='button' value='Batal' onclick='window.open(\"index.php\", \"_self\")'>
						</td>
					</tr>
				</table>
			</form>
		";
	?>
</body>
</html>