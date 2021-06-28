<?php
	session_start(); 
	$nip = $_SESSION['nip'];
	if($nip=="") {exit;}

	require_once '../config/koneksi.php';
	$nonotadinas=base64_decode($_GET['notadinas']);
	
	// pelaksana
	$query = "SELECT * FROM bidang ORDER BY CONVERT(id, UNSIGNED)";
	if ($result = mysqli_query($query)) {
		$i = -1;
		$plks = array();
		while ($row = mysqli_fetch_array($result, MYSQL_BOTH)) {
			$i++;
			$plks[$i] = array($row["id"], $row["namaunit"]);
		}
		mysqli_free_result($result);
	}
/*	
	for($i=0; $i<count($plks); $i++) {
		echo $plks[$i][0] . " " . $plks[$i][1] . "<br>";
	}
*/	
	// pos
	$query = "SELECT v.* FROM USER u INNER JOIN v_pos v ON u.nip = v.nip " . 
		($nip=="admin"? "": "WHERE u.nip = '$nip'") . " order by akses";
	if ($result = mysqli_query($query)) {
		$i = -1;
		$ipos = array();
		while ($row = mysqli_fetch_array($result, MYSQL_BOTH)) {
			$i++;
			$ipos[$i] = array($row["akses"], $row["nama"]);
		}
		mysqli_free_result($result);
	}
/*	
	for($i=0; $i<count($ipos); $i++) {
		echo $ipos[$i][0] . " " . $ipos[$i][1] . "<br>";
	}
*/	
	// saved values
	$dummy = "";
	$rslt = "";
/*	
	$sql="SELECT 
		n.nomornota nomornota, tanggal, perihal, nilaiusulan,
		pelaksana, pos1, nilai1, sisa1, rppos			
	FROM notadinas n LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
	left join (
		select * from saldopos
		union
		select * from saldopos2
		union
		select * from saldopos3
		union
		select * from saldopos4
	) s on year(tanggal) = tahun and pos1 = kdsubpos
	WHERE n.nomornota = '$nonotadinas' order by nid";
*/
	$sql = "SELECT YEAR(tanggal) tgl FROM notadinas WHERE nomornota = '$nonotadinas'";
	$result=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());
	while ($row = mysqli_fetch_array($result)) {
		$tgl = $row["tgl"];
	}
	mysqli_free_result($result);

	$sql = "
SELECT 
	n.nomornota nomornota, tanggal, perihal, nilaiusulan,
	pelaksana, pos1, nilai1, sisa1, rppos, posx, nilaix, (COALESCE(rppos,0)-COALESCE(nilaix,0)) sisax
FROM notadinas n LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
LEFT JOIN (
	SELECT * FROM saldopos
	UNION
	SELECT * FROM saldopos2
	UNION
	SELECT * FROM saldopos3
	UNION
	SELECT * FROM saldopos4
) s ON YEAR(tanggal) = tahun AND pos1 = kdsubpos
LEFT JOIN (
	SELECT pos1 posx, SUM(COALESCE(nilai1,0)) nilaix FROM notadinas n 
	LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
	WHERE YEAR(tanggal) = $tgl AND (COALESCE(n.progress,0) != 1 OR COALESCE(n.progress,0) != 5) AND n.nomornota != '$nonotadinas'
	GROUP BY pos1
) ndx
ON d.pos1 = ndx.posx
WHERE n.nomornota = '$nonotadinas' ORDER BY nid
	";
//	echo $sql;
	
	$t = -1;
	$i = -1;
	$result=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());
	while ($row = mysqli_fetch_array($result)) {
		if($dummy != $row["pelaksana"]) {
			$i = -1;
			$t++;
			$dummy = $row["pelaksana"];

			$rslt .= ($rslt==""? "": "</div><br>") . "<div id='dpic$t'>";
			$rslt .= "<select name='pic$t' id='pic$t'><option value=''>Pilih Pelaksana</option>";
			
			for($j=0; $j<count($plks); $j++) {
				$rslt .= "<option value='".$plks[$j][0]."'" . ($dummy==$plks[$j][0]? " selected": "") . ">" . 
					$plks[$j][1]."</option>";
			}
			$rslt .= "</select>&nbsp;";
			$rslt .= "<input type='button' value='-' onclick='hapus($t)'>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='button' value='++' onclick='tambahpos(\"$t\")'><br>";
		}
		
		$i++;
		$rslt .= "<div id='dpos$t.$i'>";
		$rslt .= "<select name='pos$t.$i' id='pos$t.$i' onchange='myvalue(\"$t.$i\"); poscheck(\"$t.$i\");'>";
		$rslt .= "<option value=''>Pilih Pos</option>";

		for($j=0; $j<count($ipos); $j++) {
			$rslt .= "<option value='".$ipos[$j][0]."'" . ($row["pos1"]==$ipos[$j][0]? " selected": "") . ">" . 
				$ipos[$j][0] . " - " . $ipos[$j][1]."</option>";
		}

		$rslt .= "</select>&nbsp;";
		$rslt .= "<input type='text' name='nilai$t.$i' id='nilai$t.$i' value='$row[nilai1]' onchange='nilai_usulan(\"$t.$i\")'>";
		$rslt .= "<input type='text' name='pagu$t.$i' id='pagu$t.$i' value='$row[rppos]' readonly>";
		$rslt .= "<input type='text' name='pakai$t.$i' id='pakai$t.$i' value='$row[nilaix]' readonly>";
		$rslt .= "<input type='text' name='sisa$t.$i' id='sisa$t.$i' value='$row[sisax]' readonly>";
		$rslt .= "<input name='btnm$t.$i' id='btnm$t.$i' type='button' value='--' onclick='kurangpos(\"$t.$i\")'><div id='infopagu$t.$i'></div><br>";
		$rslt .= "</div>";
	}
	mysqli_free_result($result);
	$rslt .= ($rslt==""? "": "</div>");
	$mydiv1 = $rslt;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<style>
	div.alt { background-color:yellow; box-shadow: 10px 10px 5px #888888; }
</style>
<script type="text/javascript" src="../js/ajaxcontent.js"></script>
<script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.datepick.css"> 
<script type="text/javascript" src="../js/jquery.datepick.js"></script>
<script type="text/javascript" src="../js/jquery.validate.pack.js"></script>
 <script type="text/javascript" src="../js/methods.js"></script>
<script type="text/javascript"> 
$(function() {
$('#tgl_nota').datepick({dateFormat: 'yyyy-mm-dd'});
});

$(document).ready(function() {
	$("#tambahrekomendasi").validate({
		rules: {
			nonotadinas: "required",
			tgl_nota: "required",
			perihal: "required",
			jenis: "required",
			nilairekom: "required"
		},
		messages: {
			nonotadinas: "No Nota Dinas harus diisi",	
			tgl_nota: "Tanggal Nota harus diisi",  
			perihal: "Perihal harus diisi",	
			jenis: "Jenis harus dipilih",  
			nilairekom: "Nilai Rekomendasi harus diisi"                 		
		}
	});  
 }); 
</script>
</head>
<?php
 $sql="select * 
        from notadinas 
        where nomornota='$nonotadinas'";
	$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());    
	while ($row = mysqli_fetch_array($hasil)) {
	$nomornota=$row['nomornota'];
    $tanggal=$row['tanggal'];
    $unit1=$row['unit'];  
    $perihal=$row['perihal'];
	$skkoi=$row['skkoi'];
    $pembuatskkoi=$row['pembuatskko'];
	$progress=$row['progress'];
    $noskkoi=$row['noskkoi'];
	$nilaiusulan=number_format($row['nilaiusulan']);  
	}
	mysqli_free_result($hasil);
?>
<body>

<h2>Edit Rekomendasi SKKO/I</h2>
<form name='tambahrekomendasi' method=POST id='tambahrekomendasi' action="proseseditrekomendasi.php" autocomplete="off" onsubmit="return ndvalidate(1)">
<input name="nomornotaawal" id="nomornotaawal" maxlength="64" size="50" type="hidden" value="<? echo $nonotadinas;?>"/>
<input type="hidden" name="nip" value="<?=$nip?>">
  <table>
      <tr>
            <td>Nomor Nota Dinas</td>                
            <td><input name="nonotadinas" id="nonotadinas" maxlength="64" size="50" type="text" value="<? echo $nomornota;?>"/ readonly></td>
        </tr>    
		    <tr>
                <td>Tgl Nota Dinas</td>                  
                <td><input name="tgl_nota" id="tgl_nota" type="text"  value="<? echo $tanggal;?>" size="50"></td>
            </tr>  
        <tr>
        <td>Jenis</td>                    
            <td><input name="jenis" id="jenis" maxlength="64" size="50" type="text"  value="<? echo $skkoi;?>" readonly/></td>        
         </tr> 

        <tr>
            <td>Perihal</td>                    
            <td><input name="perihal" id="perihal" maxlength="64" size="50" type="text"  value="<? echo $perihal;?>" /></td>
        </tr>
        <tr>
            <td>Nilai Usulan</td>                    
            <td>            
            <input name="nilairekom" id="nilairekom" maxlength="64" size="50" type="text" onchange="formatme(this)" value="<? echo $nilaiusulan;?>" readonly/>
            </td>
        </tr>
		 <tr>
            <td>
				Pelaksana - Pos - Nilai<br />
				<div align="center"><input type="button" name="btn" id="btn" onclick="tambah()" value="+"></div>
            </td>                
            <td>
				<div id="mydiv"><?=$mydiv1?></div>
    	    </td>
        </tr>          
      <tr>
            <td colspan="2">
                <input type="submit" value="Simpan">
                <input type="button" value="batal" onclick="window.open('index.php', '_self')">
            </td>
        </tr>
    </table>
</form>
</body>
</html>