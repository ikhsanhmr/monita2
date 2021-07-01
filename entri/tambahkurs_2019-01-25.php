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
		$con = (isset($_REQUEST["kon"])? $_REQUEST["kon"]: "");
		
		$id = $_REQUEST['id'];
		
		if($id!="") {
			$sql = "select * from kurs_dollar where id = $id";
			mysqli_set_charset("UTF8");
			//echo "$sql";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			
			while ($row = mysqli_fetch_array($result)) {
				@$id     = $row["id"];
				@$tgl     = $row["tanggal"];
				@$nilai     = $row["nilaitengah"];
			}
		
			mysqli_free_result($result);
			//$mysqli->close();($link);	  			
		}

		echo "
			<h2>INPUT Nilai Kurs</h2>
			<form name='fkurs' id='fkurs' method='post' enctype='multipart/form-data' action='simpankurs.php'>
				<table border='1'>
					<tr>
						<td>Tanggal</td>
						<td>&nbsp;:&nbsp;</td>
						<td>
							<input required type='date' name='tanggal' id='tanggal' value='$tgl'>
							<input type='hidden' name='id' id='id' value='$id'>
						</td>
					</tr>
					<tr>
						<td>Nilai Kurs</td>
						<td>&nbsp;:&nbsp;</td>
						<td>
							<input type='text' name='nilai' id='nilai' value='$nilai'>
						</td>
					</tr>
					<tr>
						<td colspan='3' align='right'>
							<input type='submit' name='simpan' value='Simpan'>&nbsp;
							<input type='button' value='Batal' onclick='window.open(\"kurs.php\", \"_self\")'>
						</td>
					</tr>
				</table>
			</form>
		";
	?>
</body>
</html>