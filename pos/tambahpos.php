<?php
  session_start(); 
  require_once '../config/koneksi.php';
  
  if(isset($_POST['btnsub']))
  {
    $kdindukpos=$_POST['kdindukpos'];
    $namaindukpos=$_POST['namaindukpos'];

    $sql="select *
          from posinduk
          where kdindukpos='$kdindukpos'";
    
  	$hasil = mysql_query($sql);
	$ceknomorsub = mysql_num_rows($hasil);
    if($ceknomorsub == 1)
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
      $sql="insert into posinduk(kdindukpos,namaindukpos)
            values('$kdindukpos','$namaindukpos')
            ";
      
      $hasil=mysql_query($sql);	

      echo '
      <script language="javascript">
        alert("Data berhasil disimpan!");
        document.location.href="index.php";
      </script>
      ';
           
    }
  }  
    
?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h5>FORM PENAMBAHAN POS</h5>
<form method="post" action="">
  <table>
    <tr>
      <td>Kode induk pos</td>
      <td><input type="text" name="kdindukpos" value="" maxlength="5"></td>
    </tr>
    <tr>
      <td>Nama induk pos</td>
      <td><input type="text" name="namaindukpos" value=""></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" value=" simpan " name="btnsub"></td>
    </tr>    
  </table>
</form>
