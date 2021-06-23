<?
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
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
$('#tgl_bayar').datepick({dateFormat: 'yyyy-mm-dd'})
});

$(document).ready(function() {
	$("#tambahskko").validate({
		rules: {
			noskko: "required",
			tgl_skko: "required",
			nilaianggaran: "required",
			nilaidisburse: "required",
			uraian: "required",
			periode: "required",
			jenis: "required",
			posinduk: "required",
			nilaitunai: "required",
			nilainontunai: "required",
			nilaiwbs: "required",
			unit1: "required"
		},
		messages: {
			noskko: "No SKKO harus diisi",	
			tgl_skko: "Tanggal SKKO harus diisi",  
			nilaianggaran: "Nilai Anggaran harus diisi",
			nilaidisburse: "Nilai Disburse harus diisi",	
			uraian: "Uraian Harus diisi",  
			periode: "Periode harus dipilih",
			jenis: "Jenis harus dipilih",
			posinduk: "Pos harus diisi",	
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
</script>
</head>
<body>
<h2>Tambah Pembayaran Kontrak</h2>
<form name='tambahskko' method=POST id='tambahskko' action="prosestambahskko.php" autocomplete="off">
  <table>
        <tr>
          <td>Nilai Bayar</td>
          <td><input name="nilaitunai" id="nilaitunai" maxlength="64" size="50" type="text" onchange="formatme(this)"/></td>
        </tr>
        <tr>
            <td>Tanggal Bayar</td>                    
            <td><input name="tgl_bayar" id="tgl_bayar" type="text" size="50"></td>
        </tr>
        <tr>
        <td>Sisa Bayar</td>                    
            <td><input name="tgl_skko2" id="tgl_skko2" type="text" size="50"></td>        
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