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
	
	$nota = "";
	$query = "SELECT DISTINCT n.nomornota nn, perihal pp FROM notadinas n LEFT JOIN notadinas_detail d
			ON n.nomornota = d.nomornota
			WHERE n.progress = 7 AND COALESCE(d.progress,0) = 0 AND skkoi = 'SKKO'" . 
			($_SESSION['adm']==1? " and nip = '$nip'": "") .
			" ORDER BY nid";

	if ($result = mysqli_query($query)) {
		$nota = "<option value=''>Pilih Nota dinas</option>";
		while ($row = mysqli_fetch_array($result, MYSQL_BOTH)) {
			$nota .= "<option value='$row[nn]'>$row[pp]</option>";
		}
		mysqli_free_result($result);
	}
	$nota = "<select name='nota$t' id='nota$t' onchange='notacheck($t)'>$nota</select>";
	
	$pelaksana = "<select name='pic$t' id='pic$t' onchange='ndcheck($t)'><option value=''>Pilih Pelaksana</option></select>";

	$rslt = "<div id='dpic$t'>";  
	$rslt .= "$nota <div id='dp$t'>$pelaksana&nbsp;</div>";

	$rslt .= "<input type='text' name='pos$t' id='pos$t' value='' readonly>";
	$rslt .= "<input type='text' name='nilai$t' id='nilai$t' value='' readonly>";
//	$rslt .= "<input type='text' name='sisa$t' id='sisa$t' value='' readonly>";
	$rslt .= "<input type='button' value='-' onclick='hapusterbit($t)'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	$rslt .= "</div>";	
	echo $rslt;
?>
</body>

</html>