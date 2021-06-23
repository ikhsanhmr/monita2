<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="../../css/screen.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../../js/methods.js"></script>
	<script type="text/javascript">
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
	
		session_start(); 
		require_once '../../config/koneksi.php';
		$nip=$_SESSION['nip'];
		$bidang=$_SESSION['bidang'];
		$kdunit=$_SESSION['kdunit'];
		$nama=$_SESSION['nama'];
		$adm=$_SESSION['adm'];
		$org=$_SESSION['org'];
		if($nip=="") {exit;}
		$con = (isset($_REQUEST["kon"])? $_REQUEST["kon"]: "");		

		//echo "$sql";
		$sqlskk = "SELECT DISTINCT noskk FROM notadinas_detail d left join notadinas n on d.nomornota = n.nomornota LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi WHERE d.progress in(7,9) and Year(tanggal) = ".date("Y")." " . ($org=="" ? "": ($org=="1" ? " AND skkoi = 'SKKI' " : "AND (nipuser = '$nip' or pelaksana = '$org') ")) . "ORDER BY noskk";
		$result_skk = mysql_query($sqlskk);

		$skk = "<select name='skk' id='skk' onChange='viewposskk(this.value, \"subpos\");checkskk(this.value);' required><option value=''></option>";
		while ($rowskk = mysql_fetch_array($result_skk, MYSQL_ASSOC)) {
			$skk .= "<option value='$rowskk[noskk]'>$rowskk[noskk]</option>";
		}
		$skk .= "</select>";	

		echo "
			<h2>INPUT RAB</h2>
			<form name='frab' id='frab' method='post' action='simpan.php'>
				<table border='1'>
					<tr>
						<td>Nomor SKKI/O <input type='hidden' name='edit' id='edit' value=''></td>
						<td>&nbsp;:&nbsp;</td>
						<td>$skk</td>
					</tr>
					<tr>
						<td>Nomor RAB </td>
						<td>&nbsp;:&nbsp;</td>
						<td><input type='text' name='norab' placeholder='Entry Nomor RAB' required></td>
					</tr>
					<tr>
						<td>Nilai </td>
						<td>&nbsp;:&nbsp;</td>
						<td><input type='text' name='nilai' placeholder='Entry Nilai' required></td>
					</tr>
					<tr>
						<td>Tanggal RAB </td>
						<td>&nbsp;:&nbsp;</td>
						<td><input type='date' name='tanggalrab' required></td>
					</tr>
					<tr>
						<td>Uraian Kegiatan</td>
						<td>&nbsp;:&nbsp;</td>
						<td><textarea name='uraian' rows='10' cols='70'> </textarea></td>
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
							<input type='submit' name='simpan' value='Simpan'>&nbsp;
							<input type='button' value='Batal' onclick='window.open(\"index.php\", \"_self\")'>
						</td>
					</tr>
				</table>
			</form>
		";
	?>
</body>
</html>