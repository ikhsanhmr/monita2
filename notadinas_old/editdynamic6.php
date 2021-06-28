<html>
<head>
	<script type="text/javascript" src="../js/methods.js"></script>
</head>

<body>
<?php
	session_start();
	$nip = $_SESSION['nip'];
	if($nip=="") {
		exit;
	}

	$t = $_REQUEST["t"];
	require_once "../config/control.inc.php";	
	
	$pelaksana = "";
	$query = "SELECT * FROM bidang ORDER BY CONVERT(id, UNSIGNED)";
	if ($result = mysqli_query($query)) {
		$pelaksana = "<option value=''>Pilih Pelaksana</option>";
		while ($row = mysqli_fetch_array($result, MYSQL_BOTH)) {
			$pelaksana .= "<option value='$row[id]'>$row[namaunit]</option>";
		}
		mysqli_free_result($result);
	}
	$pelaksana = "<select name='pic$t' id='pic$t'>$pelaksana</select>";

	$pos = "";
	$query = "SELECT v.* FROM USER u INNER JOIN v_pos v ON u.nip = v.nip " . 
		($nip=="admin"? "": "WHERE u.nip = '$nip'") . " order by akses";
	if ($result = mysqli_query($query)) {
		$pos = "<option value=''>Pilih POS</option>";
		while ($row = mysqli_fetch_array($result, MYSQL_BOTH)) {
			$pos .= "<option value='$row[akses]'>$row[akses] - $row[nama]</option>";
		}
		mysqli_free_result($result);
	}
	$mysqli->close();($kon);	
	
	$rslt = "<div id='dpic$t'>";  /* class='alt'  */
	$rslt .= "$pelaksana&nbsp;<input type='button' value='-' onclick='hapus($t)'><br>";
	for($i=0; $i<3; $i++) {
		$rslt .= "<select name='pos$t$i' id='pos$t$i'>$pos</select>&nbsp;";
		$rslt .= "<input type='text' name='nilai$t$i' id='nilai$t$i' value='' onchange='nilai_usulan()'>";
		$rslt .= "<input type='text' name='sisa$t$i' id='sisa$t$i' value='' disabled><br>";
	}
	$rslt .= "<br></div>";	
	echo $rslt;
?>
</body>

</html>