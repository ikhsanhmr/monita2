<?
  session_start(); 
  require_once '../config/koneksi.php';
  $nonotadinas=base64_decode($_GET['notadinas']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/jquery.datepick.css"> 
<script type="text/javascript" src="../js/jquery.datepick.js"></script>
<script type="text/javascript" src="../js/jquery.validate.pack.js"></script>
 <script type="text/javascript" src="../js/methods.js"></script>
<script type="text/javascript"> 

$(function() {
//$('#tgl_nota').datepick({dateFormat: 'yyyy-mm-dd'});
$('#tgl_skki').datepick({dateFormat: 'yyyy-mm-dd'})
});

$(document).ready(function() {
	$("#tambahskki").validate({
		rules: {
			noskki: "required",
			tgl_skki: "required",
			nomorscore: "required",
			nomorprk: "required",
			nilaianggaran: "required",
			nilaidisburse: "required",
			uraian: "required",
			posinduk: "required",
			nomorwbs: "required",
			nilaitunai: "required",
			nilainontunai: "required",
			nilaiwbs: "required",
			unit1: "required"
		},
		messages: {
			noskki: "No SKKI harus diisi",	
			tgl_skki: "Tanggal SKKI harus diisi",  
			nomorscore: "Nomor Score harus diisi",
			nomorprk: "Nilai PRK harus diisi",	
			nilaianggaran: "Nilai Anggaran SKKI harus diisi",  
			nilaidisburse: "Nilai Disburse SKKI harus diisi",
			uraian: "Uraian Harus Diisi",
			posinduk: "Pos harus diisi",	
			nomorwbs: "Nomor WBS harus diisi",  
			nilaitunai: "Nilai Tunai harus diisi",
			nilainontunai: "Nilai Non Tunai harus diisi",
			nilaiwbs: "Nilai WBS harus diisi",
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
 }); 
</script>
</head>
<body>
<h2>Tambah SKKI Terbit</h2>
<form name='tambahskki' method=POST id='tambahskki' action="prosestambahskki.php" autocomplete="off">
  <table>
      <tr>
            <td>Nomor Nota Dinas</td>                
            <td><input name="nonotadinas" id="nonotadinas" maxlength="64" size="50" type="text" value="<? echo $nonotadinas;?>" readonly/></td>
        </tr>    
		   <tr>
            <td>Nomor SKKI</td>                
            <td><input name="noskki" id="noskki" maxlength="64" size="50" type="text"/>
            </td>
        </tr>   
        <tr>
            <td>Tgl Terbit SKKI</td>                  
            <td><input name="tgl_skki" id="tgl_skki" type="text" size="50"></td>
            </tr>  
        <tr>
            <td>Nomor Score</td>                    
            <td>            
            <input name="nomorscore" id="nomorscore" maxlength="64" size="50" type="text"/>
            </td>
        </tr>
        <tr>
            <td>Nomor PRK</td>                    
            <td>            
            <input name="nomorprk" id="nomorprk" maxlength="64" size="50" type="text"/>
            </td>
        </tr>
        <tr>
            <td>Nilai Anggaran</td>                    
            <td>            
            <input name="nilaianggaran" id="nilaianggaran" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>
        <tr>
            <td>Nilai Disburse</td>                    
            <td>            
            <input name="nilaidisburse" id="nilaidisburse" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>
        <tr>
            <td>Uraian</td>                    
            <td><textarea name="uraian" id="uraian" rows="3" cols="50"></textarea>
            </td>
        </tr>
        <tr>
            <td>Pos Induk</td>
            <td><select name="posinduk" id="posinduk" onChange="akses(this.value)">
              <option value="">--Pilih Pos Induk--</option>
              <?php
//mengambil nama-nama propinsi yang ada di database
$posinduk = mysqli_query("SELECT * FROM POSINDUK where kdindukpos > 54  ORDER BY kdindukpos");
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
            <td>Nomor WBS</td>                    
            <td>            
            <input name="nomorwbs" id="nomorwbs" maxlength="64" size="50" type="text"/>
            </td>
        </tr>
         <tr>
            <td>Nilai Tunai</td>                    
            <td>            
            <input name="nilaitunai" id="nilaitunai" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>
        <tr>
            <td>Nilai Non Tunai</td>                    
            <td>            
            <input name="nilainontunai" id="nilainontunai" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>
        <tr>
            <td>Nilai WBS</td>                    
            <td>            
            <input name="nilaiwbs" id="nilaiwbs" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>
        <tr>
            <td>Jumlah JTM</td>                    
            <td>            
            <input name="jumlahjtm" id="jumlahjtm" maxlength="64" size="50" type="text"/>
            </td>
        </tr>      
        <tr>
            <td>Nilai Anggaran JTM</td>                    
            <td>            
            <input name="anggaranjtm" id="anggaranjtm" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>                                       	                                   
       <tr>
            <td>Nilai Disburment JTM</td>                    
            <td>            
            <input name="disburmentjtm" id="disburmentjtm" maxlength="64" size="50" type="text" onchange="formatme(this)" />
            </td>
        </tr> 
       <tr>
            <td>Jumlah GD</td>                    
            <td>            
            <input name="jumlahgd" id="jumlahgd" maxlength="64" size="50" type="text" />
            </td>
        </tr>   
      <tr>
            <td>Nilai Anggaran GD</td>                    
            <td>            
            <input name="anggarangd" id="anggarangd" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>     
        <tr>
            <td>Nilai Disburment GD</td>                    
            <td>            
            <input name="disburmentgd" id="disburmentgd" maxlength="64" size="50" type="text" />
        </td>
        </tr>  
         <tr>
            <td>Jumlah JTR</td>                    
            <td>            
            <input name="jumlahjtr" id="jumlahjtr" maxlength="64" size="50" type="text"/>
            </td>
        </tr> 
      <tr>
            <td>Nilai Anggaran JTR</td>                    
            <td>            
            <input name="anggaranjtr" id="anggaranjtr" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>   
             <tr>
            <td>Nilai Disburment JTR</td>                    
            <td>            
            <input name="disburmentjtr" id="disburmentjtr" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr> 
       <tr>
            <td>Jumlah SL 1 Fasa</td>                    
            <td>            
            <input name="jumlah1fasa" id="jumlah1fasa" maxlength="64" size="50" type="text"/>
            </td>
        </tr>
        <tr>
            <td>Nilai SL 1 Fasa</td>                    
            <td>            
            <input name="anggaran1fasa" id="anggaran1fasa" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>  
        <tr>
            <td>Nilai Disburment SL 1 Fasa</td>                    
            <td>            
            <input name="disburment1fasa" id="disburment1fasa" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>    
           <tr>
            <td>Jumlah SL 3 Fasa</td>                    
            <td>            
            <input name="jumlah3fasa" id="jumlah3fasa" maxlength="64" size="50" type="text" />
            </td>
        </tr>       
             <tr>
            <td>Nilai SL 3 Fasa</td>                    
            <td>            
            <input name="anggaran3fasa" id="anggaran3fasa" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>  
      <tr>
      <td>Nilai Disburment SL 3 Fasa</td>                    
            <td>            
            <input name="disburment3fasa" id="disburment3fasa" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
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
            <td colspan="2">
                <input type="submit" value="Simpan">
                <input type="button" value="Batal" onclick=self.history.back()>
            </td>
        </tr>
    </table>
</form>
</body>
</html>