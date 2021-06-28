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
		if($nip=="") {exit;}
		
		$kon = (isset($_REQUEST["kon"])? $_REQUEST["kon"]: "");
		if($kon!="") {
			$sql = "select * from kontrak where nomorkontrak='$kon'";
			//echo "$sql";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			
			while ($row = mysqli_fetch_array($result)) {
				$skk = $row["nomorskkoi"];
				$pos = $row["pos"];
				$uraian = $row["uraian"];
				$vendor = $row["vendor"];
				$awal = $row["tglawal"];
				//$awal = substr($awal,5,2) . "/" . substr($awal,-2) . "/" . substr($awal,0,4);
				$akhir = $row["tglakhir"];
				//$akhir = substr($akhir,5,2) . "/" . substr($akhir,-2) . "/" . substr($akhir,0,4);
				$nilai = $row["nilaikontrak"];
				//echo "$skk - $pos - $uraian - $vendor - $awal - $akhir<br>";
			}
		
			mysqli_free_result($result);
			$mysqli->close();($link);	  			
		} else {
			$skk = $_REQUEST["skk"];
			$pos = $_REQUEST["pos"];		
		}
		
		echo "
			<h2>" . ($kon!=""? "EDIT ": "INPUT ") . "KONTRAK</h2>
			<form name='fkontrak' id='fkontrak' method='post' action='simpan.php'>
				<table border='1'>
					<tr>
						<td>Nomor SKKI/O</td>
						<td>&nbsp;:&nbsp;</td>
						<td>
							<input type='hidden' name='edit' id='edit' value='$kon'>
							<input size='52' readonly type='text' name='skk' id='skk' value='$skk'>
						</td>
					</tr>
					<tr>
						<td>POS</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' readonly type='text' name='pos' id='pos' value='$pos'></td>
					</tr>
					<tr>
						<td>Nilai Kontrak</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' readonly type='text' name='nilai' id='nilai' value='$nilai'></td>
					</tr>
					<tr>
						<td><div align='center'>Kontrak<br><input type='button' value='+' onClick='tambahKontrak()'></div></td>
						<td>&nbsp;:&nbsp;</td>
						<td>
							<div id='kontrak'>
								<table border='1'>
									<tr>
										<td>Nomor Kontrak</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input required size='49' type='text' name='nkontrak0' id='nkontrak0' value='" . ($kon==""? "": $kon) . "'" . ($kon==""? "": " readonly") . "></td>
									</tr>
									<tr>
										<td>Uraian Kegitatan</td>
										<td>&nbsp;:&nbsp;</td>
										<td><textarea required rows='3' cols='47' name='uraian0' id='uraian0'>" . ($kon==""? "": $uraian) . "</textarea></td>
									</tr>
									<tr>
										<td>Vendor</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input required size='49' type='text' name='vendor0' id='vendor0' value='" . ($kon==""? "": $vendor) . "'></td>
									</tr>
									<tr>
										<td>Tanggal Awal - Akhir (MM/DD/YYYY)</td>
										<td>&nbsp;:&nbsp;</td>
										<td>
											<input required size='22' type='" . ($nice? "date": "text") . "' name='awal0' id='awal0' " . ($nice? "": "onChange='dateCheck(\"awal0\")' ") . "value='$awal'> - 
											<input required size='22' type='" . ($nice? "date": "text") . "' name='akhir0' id='akhir0' " . ($nice? "": "onChange='dateCheck(\"akhir0\")' ") . "value='$akhir'>
										</td>
									</tr>
									<tr>
										<td>Nilai Kontrak</td>
										<td>&nbsp;:&nbsp;</td>
										<td><input required size='49' type='text' name='nilai0' id='nilai0' value='" . ($kon==""? "": $nilai) . "' onChange='totalkontrak()'></td>
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