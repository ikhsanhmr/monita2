<?php
  session_start(); 
  require_once '../config/koneksi.php';
  
  
  if(isset($_GET['subpos']))
  {
    $subpos=base64_decode($_GET['subpos']);
    $pos=substr($subpos,0,4);
    
    $sql="select namasubpos 
              from posinduk2
              where kdsubpos='$pos'";              
    $hasil = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	$row = mysqli_fetch_array($hasil); 
 	$namapos=$row['namasubpos']; 
    
	          
    $sqlnamasubpos="";
    $sql="select namasubpos 
              from posinduk3
              where kdindukpos='$pos'
              and kdsubpos='$subpos'"; 
	$hasil = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	$row = mysqli_fetch_array($hasil); 
 	$namasubpos=$row['namasubpos']; 
	 
  }
  
  
  if(isset($_POST['btnsub']))
  {
    $pos=$_POST['pos'];
    //$txtkodesub=$_POST['txtkodesub'];
    $txtnamasub=$_POST['txtnamasub'];
    $nosubposlama=$_POST['nosubposlama'];

    /**
     * cek apakah nomor subpos diganti,
     * jika diganti cek apakah nomor subpos yang baru sama dengan nomor subpos yang sudah dientri
     * jika sama munculkan pesan nomor subpos harus diganti
     *      
     */         

    $sql="select *
          from posinduk3
          where kdindukpos='$pos'
          and namasubpos='$txtnamasub'";
    
    $hasil = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	$ceknomorsub = mysql_num_rows($hasil);
    
    if($ceknomorsub==1)
    {
      echo '
      <script language="javascript">
        alert("Nomor sub pos sudah ada!");
        document.location.href="javascript:history.back(0)";
      </script>
      ';
    }
    else
    {
      $sql="update posinduk3
            set 
            kdsubpos='$subpos',
            namasubpos='$txtnamasub'
            where kdindukpos='$pos'
            and kdsubpos='$nosubposlama'
            ";
      
      $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
      
      echo '
      <script language="javascript">
        alert("Data berhasil diupdate!");
        document.location.href="showdetailpos2.php?pos='.base64_encode($pos).'";
      </script>
      ';

           
    }
  }  
    
?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h5>FORM EDIT SUB POS</h5>
<form method="post" action="">
<input type="hidden" name="pos" value="<?=$pos?>">
<input type="hidden" name="nosubposlama" value="<?=$subpos?>">
<input type="hidden" name="txtnamasub" value="<?=$namasubpos?>">
  <table>
    <tr>
      <td>Pos</td>
      <td><?=$pos?></td>
    </tr>
    <tr>
      <td>Nama pos</td>
      <td><?=$namapos?></td>
    </tr>
    <tr>
      <td>Nomor sub pos</td>
      <td><?=$subpos?></td>
    </tr>
    <tr>
      <td>Nama sub pos</td>
      <td><input type="text" name="txtnamasub" value="<?=$namasubpos?>" size="32" maxlength="64"></td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="submit" value=" simpan " name="btnsub">
        <input type="button" value=" kembali " onclick="history.go(-1)">        
      </td>
    </tr>    
  </table>
</form>
