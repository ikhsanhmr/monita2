<?php
  session_start(); 
  require_once '../config/koneksi.php';
  $skk=base64_decode($_GET['s']);
  $nip=$_SESSION['nip'];
  //echo $skk;

	$query = "SELECT DISTINCT n.nomornota nn, perihal pp 
	FROM notadinas n LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota 
	WHERE n.progress = 7 AND (COALESCE(d.progress, 0) = 0 OR noskk = '$skk') AND skkoi = 'SKKO' ORDER BY nid";
	
	$nn = array();
	$pp = array();
	$i = -1;

	if ($result = mysqli_query($query)) {
		while ($row = mysqli_fetch_array($result)) {
			$i++;
			$nn[$i] = $row["nn"];
			$pp[$i] = $row["pp"];
		}
		mysqli_free_result($result);
	}
  
$sql = "
	SELECT s.*, n.nomornota nota, n.nid, pelaksana, pos1, nilai1, progress, sisa1, namaunit unit FROM " . 
	"skkoterbit s LEFT JOIN 
	(SELECT nd.*, namaunit FROM notadinas_detail nd LEFT JOIN bidang b ON nd.pelaksana = b.id) n 
	ON s.nomorskko = n.noskk where COALESCE(progress,0) != 0" . 
	//(($nip == "admin" || $nip== "6793235Z")? "": " and nip='$nip'") . 
	" AND nomorskko = '$skk' order by nomorskko, pos1, nomornota";

$j = -1;
$rslt = "";
$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());    
while ($row = mysqli_fetch_array($hasil)) {
	$j++;
	$nota = "<option value=''>Pilih Nota dinas</option>";
	for($i=0; $i<count($nn); $i++) {
		$nota .= "<option value='$nn[$i]' " . ($nn[$i]==$row["nota"]? "selected": "") . ">$pp[$i]</option>";
	}
	$nota = "<select name='nota$j' id='nota$j' onchange='notacheck($j)' disabled>$nota</select>"; 
	$pelaksana = "<select name='pic$j' id='pic$j' onchange='ndcheck($j)' disabled><option value=''>Pilih Pelaksana</option><option value='$row[nid]' selected>$row[unit]</option></select>";
	$rslt .= "<div id='dpic$j'>";  
	$rslt .= "$nota <div id='dp$j'>$pelaksana&nbsp;</div>";
	
	$rslt .= "<input type='text' name='pos$j' id='pos$j' value='$row[pos1]' readonly>";
	$rslt .= "<input type='text' name='nilai$j' id='nilai$j' value='$row[nilai1]' readonly>";
	$rslt .= "<input type='button' value='-' onclick='hapusterbit($j)'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	$rslt .= "</div><br>";	


	$noskko = $row["nomorskko"];
	$tgl_skko = $row["tanggalskko"];
	$uraian = $row["uraian"];
	$periode = $row["periode"];
	$jenis = $row["jenis"];
	$nocostcenter = $row["nomorcostcenter"];
	$nowbs = $row["nomorwbs"];
	$wbs = $row["nilaiwbs"];
	$tunai = $row["nilaitunai"];
	$nontunai = $row["nilainontunai"];
	$anggaran = $row["nilaianggaran"];
	$disburse = $row["nilaidisburse"];
}
mysqli_free_result ($hasil);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.datepick.css"> 
<script type="text/javascript" src="../js/jquery.datepick.js"></script>
<script type="text/javascript" src="../js/jquery.validate.pack.js"></script>
<script type="text/javascript" src="../js/ajaxcontent.js?_v=<?php echo rand(1, 100)?>"></script>
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
			noskko: "No SKKO harus diisi",	
			tgl_skko: "Tanggal SKKO harus diisi",  
			anggaran: "Nilai Anggaran SKKO harus diisi",
			disburse: "Nilai Disburse SKKO harus diisi",	
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
<h2>EDIT SKKO Terbit</h2>
<form name='tambahrekomendasi' method=POST id='tambahrekomendasi' action="proseseditskko.php" autocomplete="off" onsubmit="return ndvalidate(0)">
  <table>
      <tr>
            <td align="center">
				Nota Dinas - Pelaksana<br>
				Pos - Nilai - Sisa Pagu<br>
				<input type="button" name="btn" id="btn" onclick="tambahedit('<?php echo $noskko;?>')" value="+">
            </td>                
            <td>
				<div id="mydiv"></div>
				<?php echo $rslt; ?>
            </td>

		   <tr>
            <td>Nomor SKKO</td>                
            <td><input name="noskko" id="noskko" maxlength="64" size="50" type="text" value="<?php echo $noskko;?>"/>
            </td>
        </tr>   
        <tr>
            <td>Tgl Terbit SKKO</td>                  
            <td><input name="tgl_skko" id="tgl_skko" type="text" size="50" value="<?php echo $tgl_skko;?>" /></td>
            </tr>  
        <tr>
            <td>Uraian</td>                    
            <td><textarea name="uraian" id="uraian" rows="3" cols="50" /><?php echo $uraian;?></textarea>
            </td>
        </tr>
        <tr>
        <td>Periode</td>                    
            <td><select name="periode" id="periode">
             <option value=''>Pilih Periode</option>            
                    <option value='TRIWULAN' <?php echo ($periode=="TRIWULAN"? "selected": "");?> >TRIWULAN</option>
                    <option value='SEMESTER' <?php echo ($periode=="SEMESTER"? "selected": "");?> >SEMESTER</option>
                    <option value='TAHUNAN' <?php echo ($periode=="TAHUNAN"? "selected": "");?> >TAHUNAN</option>
                </select></td>        
         </tr> 
          <tr>
        <td>Jenis</td>                    
            <td><select name="jenis" id="jenis">
             <option value=''>Pilih Jenis</option>            
                    <option value='RUTIN' <?php echo ($jenis=="RUTIN"? "selected": "");?> >RUTIN</option>
                    <option value='NON RUTIN' <?php echo ($jenis=="NON RUTIN"? "selected": "");?> >NON RUTIN</option>
              </select></td>        
         </tr> 
    <tr>
            <td>No Cost Center</td>                    
            <td>            
                <div>
                  <input name="nocostcenter" id="nocostcenter" maxlength="64" size="50" type="text" onkeyup="lookup(this.value);" onblur="fill();" value="<?php echo $nocostcenter;?>" />
                </div>
          			<div class="suggestionsBox" id="suggestions" style="display: none;">
          				<div class="suggestionList" id="autoSuggestionsList">
          					&nbsp;
          				</div>
          			</div> 
        </tr>     
    <tr>
            <td>No WBS</td>                    
            <td>            
            <input name="nowbs" id="nowbs" maxlength="64" size="50" type="text" value="<?php echo $nowbs;?>" />
            </td>
        </tr> 
        <tr>
            <td>Nilai WBS</td>                    
            <td>            
            <input name="wbs" id="wbs" maxlength="64" size="50" type="text" value="<?php echo $wbs;?>" /> <!--onchange="formatme(this)"-->
            </td>
        </tr>                         
        <tr>
            <td>Nilai Tunai</td>                    
            <td>            
            <input name="tunai" id="tunai" maxlength="64" size="50" type="text" onchange="anggaranval();" value="<?php echo $tunai;?>" /> <!--onchange="formatme(this)"-->
            </td>
        </tr>
        <tr>
            <td>Nilai Non Tunai</td>                    
            <td>            
            <input name="nontunai" id="nontunai" maxlength="64" size="50" type="text" onchange="anggaranval();" value="<?php echo $nontunai;?>" /> <!--onchange="formatme(this)"-->
            </td>
        </tr>
        <tr>
            <td>Nilai Anggaran</td>                    
            <td>            
            <input name="anggaran" id="anggaran" maxlength="64" size="50" type="text" readonly value="<?php echo $anggaran;?>" /> <!--onchange="formatme(this)"-->
            </td>
        </tr>
        <tr>
            <td>Nilai Disburse</td>                    
            <td>            
            <input name="disburse" id="disburse" maxlength="64" size="50" type="text" readonly value="<?php echo $disburse;?>" /> <!--onchange="formatme(this)"-->
            </td>
        </tr>
<?php
/*
        </tr>
        <tr>
            <td>Pelaksana</td>                   
            <td><?php                 
                  $sql="SELECT * FROM BIDANG";
                  $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));   
                ?>
                <select name='unit1' id='unit1'/>
                    <option value=''>Pilih Pelaksana</option>
                    <?php
				while ($row = mysqli_fetch_array($hasil)) {
                echo "<option value='".$row['id']."-".$row['namaunit']."'>".$row['namaunit']."</option>";
                      }
                    ?>           
    	          </select></td>
        </tr>          
        <tr>
            <td>Pos Induk</td>
            <td><select name="posinduk" id="posinduk" onChange="akses(this.value)">
              <option value="">--Pilih Pos Induk--</option>
              <?php
//mengambil nama-nama propinsi yang ada di database
$posinduk = mysqli_query("SELECT * FROM POSINDUK where kdindukpos <= 54  ORDER BY kdindukpos");
while($row=mysqli_fetch_array($posinduk)){
      echo "<option value='".$row['kdindukpos']."'>".$row['kdindukpos']."-".$row['namaindukpos']."</option>";
}
?>
            </select></td>
    </tr>
        <tr>
            <td>Subpos 1</td>
            <td>
            <select name="posinduk2" id="posinduk2">
<option value="">--Pilih Subpos 1--</option>
</select>
            </td>
    </tr>
        <tr>
            <td>Subpos 2</td>
            <td>    <select name="posinduk3" id="posinduk3">
<option value="">--Pilih Subpos 2--</option>
</select>
            </td>
    </tr>
        <tr>
            <td>Subpos 3</td>
            <td>    <select name="posinduk4" id="posinduk4">
<option value="">--Pilih Subpos 3--</option>
</select>
            </td>
    </tr>
*/
?>               	                                   
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