<?php
  session_start(); 
  require_once '../config/koneksi.php';
  $skk=base64_decode($_GET['s']);
  $nip=$_SESSION['nip'];
  //echo $skk;

	$query = "SELECT DISTINCT n.nomornota nn, perihal pp 
	FROM notadinas n LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota 
	WHERE n.progress = 7 AND (COALESCE(d.progress, 0) = 0 OR noskk = '$skk') AND skkoi = 'SKKI' ORDER BY nid";
	
	$nn = array();
	$pp = array();
	$i = -1;

	if ($result = mysql_query($query)) {
		while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
			$i++;
			$nn[$i] = $row["nn"];
			$pp[$i] = $row["pp"];
		}
		mysql_free_result($result);
	}
  
$sql = "
	SELECT s.*, n.nomornota nota, pelaksana, pos1, nilai1, progress, sisa1, namaunit unit FROM " . 
	"skkiterbit s LEFT JOIN 
	(SELECT nd.*, namaunit FROM notadinas_detail nd LEFT JOIN bidang b ON nd.pelaksana = b.id) n 
	ON s.nomorskki = n.noskk where COALESCE(progress,0) != 0" . 
	(($nip == "admin" || $nip== "6793235Z")? "": " and nip='$nip'") .  
	" AND nomorskki = '$skk' order by nomorskki, pos1, nomornota";
//echo $sql;

$j = -1;
$rslt = "";
$hasil=mysql_query($sql) or die (mysql_error());    
while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
	$j++;
	$nota = "<option value=''>Pilih Nota dinas</option>";
	for($i=0; $i<count($nn); $i++) {
		$nota .= "<option value='$nn[$i]' " . ($nn[$i]==$row["nota"]? "selected": "") . ">$pp[$i]</option>";
	}
	$nota = "<select name='nota$j' id='nota$j' onchange='notacheck($j)' disabled>$nota</select>"; 
	$pelaksana = "<select name='pic$j' id='pic$j' onchange='ndcheck($j)' disabled><option value=''>Pilih Pelaksana</option><option value='$row[pelaksana]' selected>$row[unit]</option></select>";
//	$pelaksana = "<select onchange='ndcheck($j)' disabled><option value=''>Pilih Pelaksana</option><option value='$row[pelaksana]' selected>$row[unit]</option></select><input type='hidden' name='pic$j' id='pic$j' value='$row[pelaksana]'>";
	$rslt .= "<div id='dpic$j'>";  
	$rslt .= "$nota <div id='dp$j'>$pelaksana&nbsp;</div>";
	
	$rslt .= "<input type='text' name='pos$j' id='pos$j' value='$row[pos1]' readonly>";
	$rslt .= "<input type='text' name='nilai$j' id='nilai$j' value='$row[nilai1]' readonly>";
	$rslt .= "<input type='button' value='-' onclick='hapusterbit($j)'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$rslt .= "</div><br>";	

	$prk = $row["nomorprk"];
	$basket = $row["nomorscore"];
	$fungsi = $row["fungsi"];
	$fungsi_val = $row["fungsi_val"];
	$noskki = $row["nomorskki"];
	$tgl_skki = $row["tanggalskki"];
	$uraian = $row["uraian"];
	$nowbs = $row["nomorwbs"];
	$wbs = $row["nilaiwbs"];
	$tunai = $row["nilaitunai"];
	$nontunai = $row["nilainontunai"];
	$anggaran = $row["nilaianggaran"];
	$disburse = $row["nilaidisburse"];
	$jtm = $row["jtm"];
	$jtma = $row["nilaianggaranjtm"];
	$jtmd = $row["nilaidisbursejtm"];
	$gd = $row["gd"];
	$gda = $row["nilaianggarangd"];
	$gdd = $row["nilaidisbursegd"];
	$jtr = $row["jtr"];
	$jtra = $row["nilaianggaranjtr"];
	$jtrd = $row["nilaidisbursejtr"];
	$sl1 = $row["sl1"];
	$sl1a = $row["nilaianggaransl1"];
	$sl1d = $row["nilaidisbursesl1"];
	$sl3 = $row["sl3"];
	$sl3a = $row["nilaianggaransl3"];
	$sl3d = $row["nilaidisbursesl3"];
	$kp = $row["keypoint"];
	$kpa = $row["nilaianggarankp"];
	$kpd = $row["nilaidisbursekp"];
}
mysql_free_result ($hasil);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.datepick.css"> 
<script type="text/javascript" src="../js/jquery.datepick.js"></script>
<script type="text/javascript" src="../js/jquery.validate.pack.js"></script>
<script type="text/javascript" src="../js/ajaxcontent.js"></script>
<script type="text/javascript" src="../js/methods.js"></script>
    <style>
  	.suggestionsBox {
  		position: relative;
  		left: 0px;
  		margin: 10px 0px 0px 0px;
  		width: 200px;
  		background-color: #FBFDFD;
  		-moz-border-radius: 7px;
  		-webkit-border-radius: 7px;
  		border: 1px solid #e1f5f6;	
  	}
  	
  	.suggestionList {
  		margin: 0px;
  		padding: 0px;
  		z-index: 10;
  	}
  	
  	.suggestionList li {      		
  		margin: 0px 0px 3px 0px;
  		padding: 3px;
  		cursor: pointer;
  	}
  	
  	.suggestionList li:hover {
  		background-color: #FFFFFF;
  	}     
    </style>
<script type="text/javascript"> 

$(function() {
//$('#tgl_nota').datepick({dateFormat: 'yyyy-mm-dd'});
$('#tgl_skko').datepick({dateFormat: 'yyyy-mm-dd'})
});

$(document).ready(function() {
	$("#tambahrekomendasi").validate({
		rules: {
			noskko: "required",
			tgl_skko: "required",
			anggaran: "required",
			disburse: "required",
			uraian: "required",
			periode: "required",
			jenis: "required",
			posinduk: "required",
			tunai: "required",
			nontunai: "required",
			wbs: "required",
			unit1: "required"
		},
		messages: {
			noskko: "No skki harus diisi",	
			tgl_skko: "Tanggal skki harus diisi",  
			anggaran: "Nilai Anggaran skki harus diisi",
			disburse: "Nilai Disburse skki harus diisi",	
			uraian: "Uraian Harus diisi",  
			periode: "Periode harus dipilih",
			jenis: "Jenis harus dipilih",
			posinduk: "Pos harus diisi",	
			tunai: "Nilai Tunai harus diisi",  
			nontunai: "Nilai Non Tunai harus diisi",
			wbs: "Nilai WBS harus diisi",
			unit1: "Pelaksana harus diisi"               		
		}
	});  
	
		//====posinduk1
		$("#posinduk").change(function(){
		var posinduk = $("#posinduk").val();
		$.ajax({
		url: "ambilpos1.php",
		data: "posinduk="+posinduk,
		cache: false,
		success: function(msg){
		//jika data sukses diambil dari server kita tampilkan
		//di <select id=kota>
		$("#posinduk2").html(msg);
		}
		});
		});
		//====posinduk1
			//====posinduk2
		$("#posinduk2").change(function(){
		var posinduk2 = $("#posinduk2").val();
		$.ajax({
		url: "ambilpos2.php",
		data: "posinduk2="+posinduk2,
		cache: false,
		success: function(msg){
		//jika data sukses diambil dari server kita tampilkan
		//di <select id=kota>
		$("#posinduk3").html(msg);
		}
		});
		});
		//====posinduk2
		//====posinduk3
		$("#posinduk3").change(function(){
		var posinduk3 = $("#posinduk3").val();
		$.ajax({
		url: "ambilpos3.php",
		data: "posinduk3="+posinduk3,
		cache: false,
		success: function(msg){
		//jika data sukses diambil dari server kita tampilkan
		//di <select id=kota>
		$("#posinduk4").html(msg);
		}
		});
		});
		//====posinduk3


   
 }); 
 
function akses(wbscost) {
			document.getElementById("nowbs").disabled = (wbscost!=53? true: false);
			document.getElementById("nocostcenter").disabled = (wbscost==53? true: false);
}

function lookup(inputString) {
  		if(inputString.length == 0) {
  			// Hide the suggestion box.
  			$('#suggestions').hide();
  		} else {
  			$.post("rpc.php", {queryString: ""+inputString+""}, function(data){
  				if(data.length >0) {
  					$('#suggestions').show();
  					$('#autoSuggestionsList').html(data);
          
  				}
  			});
  		}
  	} // lookup
  	
function fill(thisValue) 
    {
  		$('#nocostcenter').val(thisValue);
  		setTimeout("$('#suggestions').hide();", 200);    
  	}  
</script>
</head>
<body>
<h2>EDIT skki Terbit</h2>
<form name='tambahrekomendasi' method="POST" id='tambahrekomendasi' action="proseseditskko.php" autocomplete="off" onsubmit="return ndvalidate(0)">
  <table>
    <tr>
            <td>No PRK</td>                    
            <td>
                  <input name="noprk" id="noprk" maxlength="64" size="50" type="text" value="<?php echo $prk;?>">
        </tr>
      <tr>
		<td>Basket</td>
		<td>
			<input name="basket" id="basket" maxlength="64" size="50" type="text" value="<?php echo  $basket;?>">
		</td>
      </tr>
      <tr>
		<td>Fungsi
			<select type='text' name="fungsi">
				<option value="<?php echo $fungsi;?>"><?php echo $fungsi;?></option>
				<option value="Peningkatan Keandalan">Peningkatan Keandalan</option>
				<option value="Efisiensi">Efisiensi</option>
				<option value="Pemasaran">Pemasaran</option>
				<option value="Lisdes">Lisdes</option>
				<option value="Sarana Penunjang Fungsi">Sarana Penunjang Fungsi</option>
				<option value="Sarana Umum">Sarana Umum</option>
				<option value="Sarana K2-K3L">Sarana K2-K3L</option>
				<option value="PLTD">PLTD</option>
			</select>
		</td>
		<td>
			<input name="fungsi_val" id="fungsi_val" maxlength="64" size="50" type="text" value="<?php echo $fungsi_val;?>">
		</td>
      </tr>
      <tr>
            <td align="center">
				Nota Dinas - Pelaksana<br>
				Pos - Nilai - Sisa Pagu<br>
				<input type="button" name="btn" id="btn" onclick="tambah()" value="+">
            </td>                
            <td>
				<div id="mydiv"></div>
				<?php echo $rslt;?>
            </td>

		   <tr>
            <td>Nomor SKKI</td>                
            <td><input name="noskko" id="noskko" maxlength="64" size="50" type="text" value="<?php echo $noskki;?>" readonly />
            </td>
        </tr>   
        <tr>
            <td>Uraian</td>                    
            <td><textarea name="uraian" id="uraian" rows="3" cols="50"><?php echo $uraian;?></textarea>
            </td>
        </tr>
        <tr>
            <td>Tgl Terbit SKKI</td>                  
            <td><input name="tgl_skko" id="tgl_skko" type="text" size="50" value="<?php echo $tgl_skki;?>"></td>
            </tr>  
        <tr>
            <td>
				Data Aset Manajemen
            </td>                    
            <td> <!-- <textarea name="aset" id="aset" rows="3" cols="50"></textarea> -->
				<table border="0">
					<tr>
						<th colspan='2'>Aset</th>
						<th>Anggaran</th>
						<th>Disburse</th>
					</tr>
					<tr>
						<td>JTM</td>
						<td><input type="text" name="jtm" id="jtm" value="<?php echo $jtm;?>"></td>
						<td><input type="text" name="jtma" id="jtma" value="<?php echo $jtma;?>"></td>
						<td><input type="text" name="jtmd" id="jtmd" value="<?php echo $jtmd;?>"></td>
					</tr>
					<tr>
						<td>GD</td>
						<td><input type="text" name="gd" id="gd" value="<?php echo $gd;?>"></td>
						<td><input type="text" name="gda" id="gda" value="<?php echo $gd;?>"></td>
						<td><input type="text" name="gdd" id="gdd" value="<?php echo $gd;?>"></td>
					</tr>
					<tr>
						<td>JTR</td>
						<td><input type="text" name="jtr" id="jtr" value="<?php echo $jtr;?>"></td>
						<td><input type="text" name="jtra" id="jtra" value="<?php echo $jtr;?>"></td>
						<td><input type="text" name="jtrd" id="jtrd" value="<?php echo $jtr;?>"></td>
					</tr>
					<tr>
						<td>SL1</td>
						<td><input type="text" name="sl1" id="sl1" value="<?php echo $sl1;?>"></td>
						<td><input type="text" name="sl1a" id="sl1a" value="<?php echo $sl1;?>"></td>
						<td><input type="text" name="sl1d" id="sl1d" value="<?php echo $sl1;?>"></td>
					</tr>
					<tr>
						<td>SL3</td>
						<td><input type="text" name="sl3" id="sl3" value="<?php echo $sl3;?>"></td>
						<td><input type="text" name="sl3a" id="sl3a" value="<?php echo $sl3;?>"></td>
						<td><input type="text" name="sl3d" id="sl3d" value="<?php echo $sl3;?>"></td>
					</tr>
					<tr>
						<td>Key Point</td>
						<td><input type="text" name="kp" id="kp" value="<?php echo $kp;?>"></td>
						<td><input type="text" name="kpa" id="kpa" value="<?php echo $kpa;?>"></td>
						<td><input type="text" name="kpd" id="kpd" value="<?php echo $kpd;?>"></td>
					</tr>
				</table>
            </td>
        </tr>
        <tr>
            <td>Nilai Tunai</td>                    
            <td>            
            <input value="<?php echo $tunai;?>" name="tunai" id="tunai" maxlength="64" size="50" type="text" onchange="anggaranval();" /> <!--onchange="formatme(this)"-->
            </td>
        </tr>
        <tr>
            <td>Nilai Non Tunai</td>                    
            <td>            
            <input value="<?php echo $nontunai;?>" name="nontunai" id="nontunai" maxlength="64" size="50" type="text" onchange="anggaranval();" /> <!--onchange="formatme(this)"-->
            </td>
        </tr>
        <tr>
            <td>Nilai Anggaran</td>                    
            <td>            
            <input value="<?php echo $anggaran;?>" name="anggaran" id="anggaran" maxlength="64" size="50" type="text" readonly /> <!--onchange="formatme(this)"-->
            </td>
        </tr>
        <tr>
            <td>Nilai Disburse</td>                    
            <td>            
            <input value="<?php echo $disburse;?>" name="disburse" id="disburse" maxlength="64" size="50" type="text" readonly /> <!--onchange="formatme(this)"-->
            </td>
        </tr>
		<tr>
            <td>No WBS</td>                    
            <td>            
            <input value="<?php echo $nowbs;?>" name="nowbs" id="nowbs" maxlength="64" size="50" type="text" />
            </td>
        </tr> 
        <tr>
            <td>Nilai WBS</td>                    
            <td>            
            <input value="<?php echo $wbs;?>" name="wbs" id="wbs" maxlength="64" size="50" type="text" /> <!--onchange="formatme(this)"-->
            </td>
        </tr>                         
             	                                   
      <tr>
            <td colspan="2">
                <input type="submit" value="Simpan">
                <input type="button" value="Batal" onclick="window.open('index.php', '_self')">
            </td>
        </tr>
    </table>
</form>
</body>
</html>