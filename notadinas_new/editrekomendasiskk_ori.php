<?php
	session_start(); 
	$nip = $_SESSION['nip'];
	if($nip=="") {exit;}
	
	require_once '../config/koneksi.php';
	$nonotadinas=base64_decode($_GET['notadinas']);
	$sql="SELECT COUNT(DISTINCT pelaksana) jumlah FROM notadinas n 
		LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota AND n.nomornota = '$nonotadinas'";
	$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());
	while ($row = mysqli_fetch_array($hasil)) {$j = $row["jumlah"];}
	mysqli_free_result($hasil);
	
	$i = -1;
	$k = 0;
	$pic_val = array();
	$pos_val= array();
	$nilai_val = array();
	$arr = array();
	
	$sql="SELECT d.* FROM notadinas n LEFT JOIN notadinas_detail d 
	ON n.nomornota = d.nomornota AND n.nomornota = '$nonotadinas'";
	$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());
	$baris = mysql_num_rows($hasil);
	while ($row = mysqli_fetch_array($hasil)) {
		$i++;
		$arr[$i] = array($row["pelaksana"], $row["pos1"], $row["nilai1"]);
		
		if($pic_val[$k]!=$row["pelaksana"]) {
			$k += (isset($pic_val[$k])? 1: 0);
			$pic_val[$k] = $row["pelaksana"];
		}
	}
	mysqli_free_result($hasil);
/*
	for($i=0; $i<$baris; $i++) {
		echo $arr[$i][0]." - ";
		echo $arr[$i][1]." - ";
		echo $arr[$i][2]."<br><br>";
	}
	
	for($i=0; $i<$j; $i++) {
		echo $pic_val[$i] . "<br>";
	}
*/	
	// pelaksana
	$query = "SELECT * FROM bidang ORDER BY CONVERT(id, UNSIGNED)";
	$result1 = mysqli_query($query);
	
	// pos
	$query = "SELECT v.* FROM USER u INNER JOIN v_pos v ON u.nip = v.nip " . 
		($nip=="admin"? "": "WHERE u.nip = '$nip'") . " order by akses";
	$result2 = mysqli_query($query);
	$posx = "<option value=''>Pilih POS</option>";
	while ($row = mysqli_fetch_array($result2, MYSQL_BOTH)) {
		$posx .= "<option value='$row[akses]'>$row[akses] - $row[nama]</option>";
	}
	

	$mydiv = "";
	$mydiv1 = "";
	$last = 0;	
	for($t=0; $t<$j; $t++) {
		$pelaksana = "";
		if($result1) {
			mysql_data_seek($result1, 0);
			$pelaksana = "<option value=''>Pilih Pelaksana</option>";
			while ($row = mysqli_fetch_array($result1, MYSQL_BOTH)) {
				$pelaksana .= "<option value='$row[id]' " . 
					($row["id"]==$pic_val[$t]? " selected": "") . ">$row[namaunit]</option>";
			}
		}
		$pelaksana = "<select name='pic$t' id='pic$t'>$pelaksana</select>";
		

		$rslt1 = "<div id='dpic$t'>";
		$rslt1 .= "$pelaksana&nbsp;<input type='button' value='-' onclick='hapus($t)'><br>";
		
		if($result2) {
			for($k=0; $k<3; $k++) {		// 3 combo dan text
				$nilai1 = "";
				$pagu1 = "";
				$pos1 = "";
				
				for($l=$last; $l<$baris; $l++) {
					if($pic_val[$t]==$arr[$l][0]) {
						$last++;
						
						$nilai1= (isset($arr[$l][2])? $arr[$l][2]: "");
						//echo $l . " - " . $arr[$l][2] . "<br>";
						
						mysql_data_seek($result2, 0);
						$pos1 = "<option value=''>Pilih POS</option>";
						while ($row = mysqli_fetch_array($result2, MYSQL_BOTH)) {
							$pos1 .= "<option value='$row[akses]' " . 
								($row["akses"]==$arr[$l][1]? " selected": "") . ">$row[akses] - $row[nama]</option>";
						}
						
						break;
					}
				}
				
				$rslt1 .= "<select name='pos$t$k' id='pos$t$k'>" . ($pos1==""? $posx: $pos1) . "</select>&nbsp";
				$rslt1 .= "<input type='text' name='nilai$t$k' id='nilai$t$k' value='$nilai1'" . 
				" onchange='nilai_usulan()'>";
				$rslt1 .= "<input type='text' name='sisa$t$k' id='sisa$t$k --- ' value='$t$k' disabled><br>";
			}
		}
		$rslt1 .= "<br></div>";
		$mydiv1 .= $rslt1;
	}
	mysqli_free_result($result1);
	mysqli_free_result($result2);

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
<form name='tambahrekomendasi' method=POST id='tambahrekomendasi' action="proseseditrekomendasi.php" autocomplete="off">
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
				Pelaksana - Pos - Nilai - Sisa Pagu<br />
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