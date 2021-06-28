<?php
  session_start(); 
  require_once '../config/koneksi.php';
  
  if(isset($_POST['btnsub']))
  {
    $pos=$_POST['pos'];
    $txtkodesub=$_POST['txtkodesub'];
    $txtnamasub=$_POST['txtnamasub'];

    $sql="select *
          from posinduk4
          where kdindukpos='$pos'
          and kdsubpos='$txtkodesub'";
    
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
      $sql="insert into posinduk4(kdindukpos,kdsubpos,namasubpos)
            values('$pos','$txtkodesub','$txtnamasub')
            ";
      
      $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));	
      
      echo '
      <script language="javascript">
        alert("Data berhasil disimpan!");
        document.location.href="showdetailpos3.php?pos='.base64_encode($pos).'";
      </script>
      ';

           
    }
  }
  else
  {
    $pos=base64_decode($_GET['pos']);
	$sql="select namasubpos 
              from posinduk3
              where kdsubpos='$pos'
              ";  
	$hasil = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
  	$row = mysqli_fetch_array($hasil); 
  	$namapos=$row['namasubpos'];
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
      <td><input type="text" name="txtkodesub" value="<?=$pos.'.'?>" size="8" maxlength="10"></td>
    </tr>
    <tr>
      <td>Nama sub pos</td>
      <td><input type="text" name="txtnamasub" value="" size="50"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" value=" simpan " name="btnsub"></td>
    </tr>    
  </table>
</form>
<a href="showdetailpos2.php?subpos=<?=base64_encode($pos)?>">Kembali</a>
