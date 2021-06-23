<?php
  session_start(); 
  require_once '../config/koneksi.php';
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
			bidang: "required",
			perihal: "required",
			jenis: "required",
			nilairekom: "required"
		},
		messages: {
			nonotadinas: "No Nota Dinas harus diisi",	
			tgl_nota: "Tanggal Nota harus diisi",  
			bidang: "Bidang harus diisi",
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
 $nonotadinas=base64_decode($_GET['notadinas']);
 $sql="select * 
        from notadinas 
        where nomornota='$nonotadinas'";
	$hasil=mysql_query($sql) or die (mysql_error());    
	while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
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
	
?>
<body>

<h2>Edit Rekomendasi SKKO/I</h2>
<form name='editrekomendasi' method=POST id='editrekomendasi' action="proseseditrekomendasi.php" autocomplete="off">
<input name="nomornotaawal" id="nomornotaawal" maxlength="64" size="50" type="hidden" value="<?php echo $nonotadinas;?>"/>
  <table>
      <tr>
            <td>Nomor Nota Dinas</td>                
            <td><input name="nonotadinas" id="nonotadinas" maxlength="64" size="50" type="text" value="<?php echo $nomornota;?>"/></td>
        </tr>    
		    <tr>
                <td>Tgl Nota Dinas</td>                  
                <td><input name="tgl_nota" id="tgl_nota" type="text"  value="<?php echo $tanggal;?>" size="50"></td>
            </tr>  
        <tr>
            <td>Bidang/ Area</td>                   
            <td><?php                 
                  $sql="SELECT * FROM BIDANG";
                  $hasil=mysql_query($sql);   
                ?>
                <select name='unit1' id='unit1'/>
                    <option value=''>Pilih Bidang/ Area</option>
                    <?php
				while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
                        echo "<option value='".$row['id']."-".$row['namaunit']."'".($row['namaunit']==$unit?"selected":"") . ">".$row['namaunit']."</option>";
                      }
                    ?>           
    	          </select></td>
        </tr>          
        <tr>
            <td>Perihal</td>                    
            <td><input name="perihal" id="perihal" maxlength="64" size="50" type="text"  value="<?php echo $perihal;?>" /></td>
        </tr>
        <tr>
        <td>Jenis</td>                    
            <td><select name="jenis" id="jenis">
             <option value=''>Pilih Jenis</option>            
                    <option value='SKKO' <?php if($skkoi=='SKKO') echo "selected"?>>SKKO</option>
                    <option value='SKKI' <?php if($skkoi=='SKKI') echo "selected"?>>SKKI</option>
                </select></td>        
         </tr> 
        <tr>
            <td>Nilai Usulan</td>                    
            <td>            
            <input name="nilairekom" id="nilairekom" maxlength="64" size="50" type="text" onchange="formatme(this)" value="<?php echo $nilaiusulan;?>"/>
            </td>
        </tr>
        <tr>
            <td>Pembuat SKKO</td>                    
            <td><input name="pembuat" id="pembuat" maxlength="64" size="50" type="text" readonly value="<?php echo $pembuatskkoi;?>"/></td>
        </tr>                              
        <tr>
            <td>Progress</td>                   
            <td><input name="progress" id="progress" maxlength="64" size="50" type="text" readonly value="<?php echo $progress;?>"/></td>
        </tr>    
        <tr>
            <td>No SKKO</td>                  
            <td><input name="noskk" id="noskk" maxlength="64" size="50" type="text" readonly value="<?php echo $noskkoi;?>"/></td>
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