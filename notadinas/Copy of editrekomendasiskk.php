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
	
	$ip = 0;
	$ipic = array();
	$ipos = array();
	$inil = array();
	
	$sql="SELECT d.* FROM notadinas n LEFT JOIN notadinas_detail d 
	ON n.nomornota = d.nomornota AND n.nomornota = '$nonotadinas'";
	$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());
	while ($row = mysqli_fetch_array($hasil)) {
		if($ipic[$ip]!=$row["pelaksana"]) {
			$ip += (isset($ipic[$ip])? 1: 0);
			$ipic[$ip] = $row["pelaksana"];
		}
	}
	mysqli_free_result($hasil);
	
	
	// pelaksana
	$query = "SELECT * FROM bidang ORDER BY CONVERT(id, UNSIGNED)";
	$result1 = mysqli_query($query);
	
	// pos
	$query = "SELECT v.* FROM USER u INNER JOIN v_pos v ON u.nip = v.nip " . 
		($nip=="admin"? "": "WHERE u.nip = '$nip'") . " order by akses";
	$result2 = mysqli_query($query);
	

	$mydiv = "";
	for($t=0; $t<$j; $t++) {
/*	
		$pelaksana = "";
		$query = "SELECT * FROM bidang ORDER BY CONVERT(id, UNSIGNED)";
		if ($result = mysqli_query($query)) {
			$pelaksana = "<option value=''>Pilih Pelaksana</option>";
			while ($row = mysqli_fetch_array($result)) {
//				$pelaksana .= "<option value='$row[id]'>$row[namaunit]</option>";
				$pelaksana .= "<option value='$row[id]' " . 
					($row["id"]==$ipic[$t]? " selected": "") . ">$row[namaunit]</option>";
			}
			mysqli_free_result($result);
		}
		$pelaksana = "<select name='pic$t' id='pic$t'>$pelaksana</select>";
*/
		
		$pelaksana = "";
		if($result1) {
			mysql_data_seek($result1, 0);
			$pelaksana = "<option value=''>Pilih Pelaksana</option>";
			while ($row = mysqli_fetch_array($result1)) {
//				$pelaksana .= "<option value='$row[id]'>$row[namaunit]</option>";
				$pelaksana .= "<option value='$row[id]' " . 
					($row["id"]==$ipic[$t]? " selected": "") . ">$row[namaunit]</option>";
			}
		}
		$pelaksana = "<select name='pic$t' id='pic$t'>$pelaksana</select>";

		if($result2) {
			for($x=0; $x<3; $x++) {
				mysql_data_seek($result1, 0);
			}
		}
		
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

		$rslt = "<div id='dpic$t'>";
		$rslt .= "$pelaksana&nbsp;<input type='button' value='-' onclick='hapus($t)'><br>";
		for($i=0; $i<3; $i++) {
			$rslt .= "<select name='pos$t$i' id='pos$t$i'>$pos</select>&nbsp;";
			$rslt .= "<input type='text' name='nilai$t$i' id='nilai$t$i' value='' onchange='nilai_usulan()'>";
			$rslt .= "<input type='text' name='sisa$t$i' id='sisa$t$i' value='' disabled><br>";
		}	
		
		$rslt .= "<br></div>";
		$mydiv .= $rslt;
	}
	mysqli_free_result($result1);

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
/*
function updateteks()
{
  var angka = clearnumberformat(document.tambahrekomendasi.rp_skk.value);
  document.tambahrekomendasi.rp_skk.value = setnumberformat(angka);
}

function updatedisburse()
{
  var angka = clearnumberformat(document.tambahrekomendasi.rpdisbursement.value);
  document.tambahrekomendasi.rpdisbursement.value = setnumberformat(angka);
}
 */
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

/*
  $("#rp_skk").keypress(function(e){
      if(e.which!=8 && e.which!=0 && (e.which<48 || e.which>57))
      {
        //display error message
        $("#errmsg_skk").css('color','red');
        $("#errmsg_skk").html("Harus angka").show();
        return false;
      }
      else
      {
         $("#errmsg_skk").hide();       
      }
  });  */
   
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
				<div id="mydiv"><?=$mydiv?></div>
    	    </td>
        </tr>          

      <tr>
            <td colspan="2">
                <input type="submit" value="Simpan">
                <input type="button" value="batal" onclick=self.history.back()>
            </td>
        </tr>
    </table>
</form>
</body>
</html>