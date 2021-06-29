<html>

<head>
	<script type="text/javascript" src="../js/methods.js"></script>
</head>

<body>
	<?php
	session_start();
	$nip = $_SESSION['nip'];
	if ($nip == "") {
		exit;
	}

	$t = $_REQUEST["t"];
	$skk = $_REQUEST["skk"];
	require_once "../config/control.inc.php";

	$nomornota = "";
	if (!empty($skk)) {
		$sql_notadinas = mysqli_query($mysqli, "select nomornota from notadinas_detail where noskk='$skk' limit 1");
		$get_notadinas = mysqli_fetch_assoc($sql_notadinas);

		$nomornota = $get_notadinas['nomornota'];
	}

	$nota = "";
	$query = "SELECT DISTINCT n.nomornota nn, perihal pp FROM notadinas n LEFT JOIN notadinas_detail d
			ON n.nomornota = d.nomornota
			WHERE n.progress = 7 AND COALESCE(d.progress,0) = 0 AND skkoi = 'SKKI'" .
		($_SESSION['adm'] == 1 ? " and nip = '$nip'" : "") . (!empty($nomornota) ? " and n.nomornota = '$nomornota'" : "") .
		" ORDER BY nid";

	$nota = "<option value=''>Pilih Nota dinas</option>";

	if ($result = mysqli_query($mysqli, $query)) {
		while ($row = mysqli_fetch_array($result)) {
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
	if ($t > 0) {
		$rslt .= "<input type='button' value='-' onclick='hapusterbit($t)'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}

	$rslt .= "</div><br />";
	echo $rslt;
	?>
</body>

</html>