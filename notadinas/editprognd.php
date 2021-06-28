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
		
		$nd = $_REQUEST['nd'];
		
		if($nd!="") {
			$sql = "SELECT 	n.nomornota as nomornota, n.tanggal, n.perihal, n.skkoi as skkoi, n.nilaiusulan, 
					n.progress as progress, n.nip, n.assigndt, n.nipuser, p.pid, p.info, p.keterangan, u.nama, 
					u.kdunit as unitanggaran, u.bidang, u.adm
			FROM 	notadinas n LEFT JOIN 
					progress p ON COALESCE(n.progress, 0) = p.pid LEFT JOIN 
					user u on n.nip=u.nip 
			WHERE nomornota = '$nd'";
			mysql_set_charset("UTF8");
			//echo "$sql";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			
			while ($row = mysqli_fetch_array($result)) {
				@$nomornota     = $row["nomornota"];
				@$uraian     = $row["perihal"];
				@$info     = $row["info"];
				@$progress     = $row["progress"];
				@$nilai     = $row["nilaitengah"];
			}
		
			mysqli_free_result($result);
			//$mysqli->close();($link);

			$sql = "SELECT * FROM progress ORDER BY pid";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			$selection = "<select name='progress' id='progress'>";

			while ($row = mysqli_fetch_array($result)) {
				
				$selection .= "<option value='$row[pid]' " . ($progress==$row["pid"]? "selected": "") . ">$row[info]</option>";
			}
			mysqli_free_result($result);
			$selection .= "</select>";

			echo "
				<h2>Ubah Progress Notadinas</h2>
				<form name='fkurs' id='fkurs' method='post' enctype='multipart/form-data' action='simpaneditprognd.php'>
					<table border='1'>
						<tr>
							<th>Nomor Notadinas</th>
							<th>Perihal</th>
							<th>Progress</th>
							<th>Revisi Progress</th>
						</tr>
						<tr>
							<td>
								$nomornota
								<input type='hidden' name='nomornota' value='$nomornota'>
							</td>
							<td>$uraian</td>
							<td>$info</td>
							<td>
								$selection
							</td>
						</tr>
						<tr>
							<td colspan='4' align='center'>
								<input type='submit' name='simpan' value='Simpan'>&nbsp;
								<input type='button' value='Batal' onclick='window.open(\"editprogressnotadinas.php\", \"_self\")'>
							</td>
						</tr>
					</table>
				</form>
			";	  			
		}
	?>
</body>
</html>