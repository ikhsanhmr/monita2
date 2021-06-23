<?php
	session_start(); 
	require_once '../config/koneksi.php';
	
	$result = "";
	$skk = $_REQUEST["s"];
	
	// Select Jenis SKK
	$skki=mysql_query("select notadinas.skkoi from notadinas_detail left join notadinas on  notadinas_detail.nomornota=notadinas.nomornota where notadinas_detail.noskk='$skk'");
	$jskk=mysql_fetch_assoc($skki);
	$jskk=($jskk['skkoi']=='SKKI' ? true:false); // Check Jenis SKK
	
	if ($jskk) {
		$sql = "SELECT DISTINCT no_rab, nilai_rp FROM rab where nip='$_SESSION[nip]' AND status=0 ORDER BY no_rab";
		$result = mysql_query($sql);

		$skk = "<td>RAB</td><td>&nbsp;:&nbsp;</td><td><select name='nrab' id='nrab' required><option value=''></option>";
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$skk .= "<option value='$row[no_rab]'>$row[no_rab] (Rp. ".number_format($row['nilai_rp']).")</option>";
		}
		$skk .= "</select></td>";
	}else{
		$skk="";
	}
	
	mysql_free_result($skki);
	mysql_close($kon);	  			
	echo $skk;
?>