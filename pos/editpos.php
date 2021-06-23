<?php
  session_start(); 
  require_once '../config/koneksi.php';
  
  if(isset($_GET['pos']))
  {
    $kdindukpos=base64_decode($_GET['pos']);
    
    $sql="select * 
              from posinduk
              where kdindukpos='$kdindukpos'
              ";
	
	$hasil = mysql_query($sql);
	$row = mysql_fetch_array($hasil); 
 	$namaindukpos=$row['namaindukpos'];
  }
  
  
  if(isset($_POST['btnsub']))
  {
    $namaindukpos=$_POST['namaindukpos'];

    /**
     * cek apakah nomor subpos diganti,
     * jika diganti cek apakah nomor subpos yang baru sama dengan nomor subpos yang sudah dientri
     * jika sama munculkan pesan nomor subpos harus diganti
     *      
     */         

    $sql="select *
          from posinduk
          where kdindukpos='$kdindukpos'
          ";
    
	$hasil = mysql_query($sql);
	$ceknomorsub = mysql_num_rows($hasil);

    
    if($ceknomorpos==1)
    {
      echo '
      <script language="javascript">
        alert("Nomor pos sudah ada!");
        document.location.href="javascript:history.back(0)";
      </script>
      ';
    }
    else
    {
/*
	$sqlsub="select * from posanggota3 where kdindukpos in
(select kdsubpos from posanggota2 where kdindukpos in
(select kdsubpos from posanggota where kdindukpos='$kdindukpos')) ";
  $rssub=db_select($sqlsub);

      $sql="update posinduk
            set 
            kdindukpos='$kdindukpos',
            namaindukpos='$namaindukpos'
            where kdindukpos='$kdindukposlama'
            ";
 */
      $sql="update posinduk
            set 
            kdindukpos='$kdindukpos',
            namaindukpos='$namaindukpos'
            where kdindukpos='$kdindukpos'
            ";
      $hasil=mysql_query($sql);  
      
      echo '
      <script language="javascript">
        alert("Data berhasil diupdate!");
        document.location.href="index.php";
      </script>
      ';

           
    }
  }  
    
?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h5>FORM EDIT POS</h5>
<form method="post" action="">
<input type="hidden" name="kdindukposlama" value="<?=$kdindukpos?>">
  <table>
    <tr>
      <td>Kode induk pos</td>
      <td><?=$kdindukpos?></td>
			<input type="hidden" name="kdindukpos" value="<?=$kdindukpos?>" maxlength="5">
    </tr>
    <tr>
      <td>Nama induk pos</td>
      <td><input type="text" name="namaindukpos" value="<?=$namaindukpos?>"></td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="submit" value=" simpan " name="btnsub">
        <input type="button" value=" kembali " onclick="history.go(-1)">
      </td>
    </tr>    
  </table>
</form>
