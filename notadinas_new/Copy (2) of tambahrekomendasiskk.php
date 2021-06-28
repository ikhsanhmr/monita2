<?
	session_start(); 
//  require_once '../config/koneksi.php';
	require_once "../config/control.inc.php";
  	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
 
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
$('#tgl_nota').datepick({dateFormat: 'yyyy-mm-dd'})
});

$(document).ready(function() {
	$("#tambahrekomendasi").validate({
		rules: {
			nonotadinas: "required",
			tgl_nota: "required",
			unit1: "required",
			perihal: "required",
			jenis: "required",
			nilairekom: "required"
		},
		messages: {
			nonotadinas: "No Nota Dinas harus diisi",	
			tgl_nota: "Tanggal Nota harus diisi",  
			unit1: "Bidang harus diisi",
			perihal: "Perihal harus diisi",	
			jenis: "Jenis harus dipilih",  
			nilairekom: "Nilai Rekomendasi harus diisi"                 		
		}
	});  
 }); 
</script>
</head>
<body>
<h2>Tambah Rekomendasi SKKO/I</h2>
<form name='tambahrekomendasi' method=POST id='tambahrekomendasi' action="prosesrekomendasi.php" autocomplete="off">
<input type="hidden" name="nip" value="<?=$nip?>">
  <table>
      <tr>
            <td>Nomor Nota Dinas</td>                
            <td><input name="nonotadinas" id="nonotadinas" maxlength="64" size="50" type="text"/></td>
        </tr>    
		    <tr>
                <td>Tgl Nota Dinas</td>                  
                <td><input name="tgl_nota" id="tgl_nota" type="text" size="50"></td>
            </tr>  
        <tr>
        <td>Jenis</td>                    
            <td><select name="jenis" id="jenis">
             <option value=''>Pilih Jenis</option>            
                    <option value='SKKO'>SKKO</option>
                    <option value='SKKI'>SKKI</option>
                </select></td>        
         </tr> 
        <tr>
            <td>Perihal</td>                    
            <td><input name="perihal" id="perihal" maxlength="64" size="50" type="text"/></td>
        </tr>
        <tr>
            <td>Nilai Usulan</td>                    
            <td>            
            <input name="nilairekom" id="nilairekom" maxlength="64" size="50" type="text" onchange="formatme(this)"/>
            </td>
        </tr>              	                                   
        <tr>
            <td>Pelaksana<br /><input type="button" name="badd" id=badd" value="+"></td>                
            <td>
				<?php                 
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
    	          </select>
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