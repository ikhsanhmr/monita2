<?php
	session_start();
	$nip = $_SESSION['nip'];
	if($nip=="") {
		exit;
	}

	$t = $_REQUEST["t"];
	$i = $_REQUEST["i"];
//	echo "t $t - i $i<br>";
	require_once "../config/control.inc.php";	
	
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
	$rslt = "";
	
//	for($i=0; $i<1 /*3*/ ; $i++) {
	$rslt .= "<div id='dpos$t.$i'>";
	$rslt .= "<select name='pos$t.$i' id='pos$t.$i' onchange='myvalue(\"$t.$i\"); poscheck(\"$t.$i\");'>$pos</select>&nbsp;";
	$rslt .= "<input type='text' name='nilai$t.$i' id='nilai$t.$i' value='' onchange='nilai_usulan(\"$t.$i\")'>";
	$rslt .= "<input type='text' name='sisa$t.$i' id='sisa$t.$i' value='' readonly>";
	$rslt .= "<input name='btnm$t.$i' id='btnm$t.$i' type='button' value='--' onclick='kurangpos(\"$t.$i\")'><br>";
	$rlst .= "</div><br><br>";
//	}
	echo $rslt;
?>