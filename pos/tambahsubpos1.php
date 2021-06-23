<?php
  session_start(); 
  require_once '../config/koneksi.php';
  $pos=base64_decode($_GET['pos']);
  $sql="select namaindukpos 
            from posinduk
            where kdindukpos='$pos'
            ";
  $hasil = mysql_query($sql);
  $row = mysql_fetch_array($hasil); 
  $namapos=$row['namaindukpos'];
  
  if(isset($_POST['btnsub']))
  {
    $pos=$_POST['pos'];
    $txtkodesub=$_POST['txtkodesub'];
    $txtnamasub=$_POST['txtnamasub'];

    $sql="select *
          from posinduk2
          where kdindukpos='$pos'
          and kdsubpos='$txtkodesub'";
    $hasil = mysql_query($sql);
	$ceknomorsub = mysql_num_rows($hasil);
	//$ceknomorsub=db_select_column($sql);
    
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
      $sql="insert into posinduk2(kdindukpos,kdsubpos,namasubpos)
            values('$pos','$txtkodesub','$txtnamasub')
            ";
      
      $hasil=mysql_query($sql);	
     
      echo '
      <script language="javascript">
        alert("Data berhasil disimpan!");
        document.location.href="showdetailpos1.php?pos='.base64_encode($pos).'";
      </script>
      ';
	
           
    }
  }  
    
?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h5>FORM PENAMBAHAN SUB POS</h5>
<form method="post" action="">
<input type="hidden" name="pos" value="<?=$pos?>">
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
      <td><input type="text" name="txtkodesub" value="<?=$pos.'.'?>" size="6" maxlength="5"></td>
    </tr>
    <tr>
      <td>Nama sub pos</td>
      <td><input type="text" name="txtnamasub" value="" size="40"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" value=" simpan " name="btnsub"></td>
    </tr>    
  </table>
</form>
