<?php
	session_start(); 
	require_once '../config/koneksi.php';
	
	$result = "";
	$skk = $_REQUEST["s"];
	
	// Select Jenis SKK
	$skki=mysqli_query("select notadinas.skkoi from notadinas_detail left join notadinas on  notadinas_detail.nomornota=notadinas.nomornota where notadinas_detail.noskk='$skk'");
	$jskk=mysqli_fetch_assoc($skki);
	$jskk=($jskk['skkoi']=='SKKI' ? true:false); // Check Jenis SKK
	
	if ($jskk) {
		// $sql = "SELECT DISTINCT no_rab, nilai_rp FROM rab where nip='$_SESSION[nip]' AND status=0 ORDER BY no_rab";

		$sql = "
			SELECT 	DISTINCT a.no_rab, nilai_rp, (a.nilai_rp - coalesce(jumlah, 0)) as sisa
			FROM	rab a Left Join 
					( 
						SELECT 	no_rab, SUM(nilaikontrak) as jumlah 
						FROM 	kontrak
						GROUP BY no_rab 
					) b ON a.no_rab = b.no_rab 
			where 	nip='$_SESSION[nip]' AND skk = '$skk' AND (a.nilai_rp - coalesce(jumlah, 0)) > 0
			ORDER BY no_rab
		";

		// echo $sql;
		// return;
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

		$skk = "<td>RAB</td><td>&nbsp;:&nbsp;</td><td><select name='nrab' id='nrab' required><option value=''></option>";
		while ($row = mysqli_fetch_array($result)) {
			// $skk .= "<option value='$row[no_rab]'>$row[no_rab] (Rp. ".number_format($row['nilai_rp']).")</option>";
			$skk .= "<option value='$row[no_rab]' data-sisa='$row[sisa]'>$row[no_rab] (Nilai: Rp. ".number_format($row['nilai_rp']).", Sisa: Rp. ".number_format($row['sisa']).")</option>";
		}
		$skk .= "</select></td>";
	}else{
		$skk="";
	}
	
	mysqli_free_result($skki);
	$mysqli->close();($kon);	  			
	echo $skk;
?>