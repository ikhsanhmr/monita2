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
		while ($row = mysqli_fetch_array($result)) {
			$pelaksana .= "<option value='$row[id]'>$row[namaunit]</option>";
		}
		mysqli_free_result($result);
	}
	$pelaksana = "<select name='pic$t' id='pic$t' onchange='unitcheck(this.id)'>$pelaksana</select>";

	$pos = "";
	$query = "SELECT v.* FROM USER u INNER JOIN v_pos v ON u.nip = v.nip " . 
		($nip=="admin"? "": "WHERE u.nip = '$nip'") . " order by akses";
	if ($result = mysqli_query($query)) {
		$pos = "<option value=''>Pilih POS</option>";
		while ($row = mysqli_fetch_array($result)) {
			$pos .= "<option value='$row[akses]'>$row[akses] - $row[nama]</option>";
		}
		mysqli_free_result($result);
	}
	$mysqli->close();($kon);	
	
	$rslt = ($t==0? "": "<br>");
	$rslt .= "<div id='dpic$t'>";  /* class='alt'  */
	$rslt .= "$pelaksana&nbsp;";
//	for($i=0; $i<1 /*3*/ ; $i++) {
		$i = 0;
		$rslt .= "
		<input type='button' value='-' onclick='hapus($t)'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='button' value='++' onclick='tambahpos(\"$t\")'><br>";
		
		$rslt .= "<div id='dpos$t.$i'>";
		$rslt .= "<select name='pos$t.$i' id='pos$t.$i' onchange='myvalue(\"$t.$i\"); poscheck(\"$t.$i\");'>$pos</select>&nbsp;";
		$rslt .= "<input type='text' name='nilai$t.$i' id='nilai$t.$i' value='' onchange='nilai_usulan(\"$t.$i\")'>";
		$rslt .= "<input type='text' name='sisa$t.$i' id='sisa$t.$i' value='' readonly>";
		$rslt .= "<input name='btnm$t.$i' id='btnm$t.$i' type='button' value='--' onclick='kurangpos(\"$t.$i\")'><br>";
		$rlst .= "</div>";
//	}
//	$rslt .= "<br></div>";	
	$rslt .= "</div>";	
	echo $rslt;
?>
</body>

</html>