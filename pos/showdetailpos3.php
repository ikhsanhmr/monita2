<?php
    session_start(); 
    require_once '../config/koneksi.php';
    $kdindukpos=base64_decode($_GET['pos']); 
    if(isset($_GET['del']))
    {
      $kdsubpos2=base64_decode($_GET['del']);
	  
      
      $sql="delete 
            from posinduk4
            where kdindukpos = '%kdsubpos'
            ";
     
      $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
            
    }
    else
    {
      //$kdindukpos=base64_decode($_GET['pos']);      
      $sql="select namasubpos 
            from posinduk3
            where kdsubpos='$kdindukpos'";
			
    
      $hasil = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	  $row = mysqli_fetch_array($hasil); 
 	  $namapos=$row['namasubpos'];    
	  
    }

?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h5>
  DAFTAR SUB POS <?=$subpos.' '.$namapos?>
</h5>
<a href="tambahsubpos3.php?pos=<?=$_GET['subpos']?>">(+) Tambah subpos</a><br />
  <?php
  /*
  $ses_userid=$_SESSION['p_userid'];
   $sqlsub="select subpos3 from `users-akses-pos` where subpos2='$subpos' and iduser='$ses_userid'";
  $rssub=db_select($sqlsub);
  //echo "asda".$rssub[0]['subpos3']."asd".$sqlsub;
  if ($rssub[0]['subpos3']=='' or $_SESSION['p_akses']=='0')
  {
  $sql="select kdsubpos, namasubpos
        from posanggota3
        where kdindukpos='$subpos'
        ";
  }     
  else
  {
	  $sql="select kdsubpos, namasubpos
        from posanggota3
        where kdindukpos='$subpos' and kdsubpos in (select subpos3 from `users-akses-pos` where subpos2='$subpos' and iduser='$ses_userid')
		";
  }
  $rs=db_select($sql);
 */
  $no=0;
	$sql="select kdindukpos,kdsubpos, namasubpos
        from posinduk4
        where kdindukpos='$kdindukpos'
        ";
	
  
  $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
  $rs = mysql_num_rows($hasil);
  if(count($rs)==0)
  {
    echo "Data tidak ditemukan.<br>";
  }
  else
  {
    echo '
    <table>
      <tr>
        <th>No</th>
        <th>Sub Pos</th>
        <th>Nama</th>
        <th>Aksi</th>
      </tr>';
        
while ($row = mysqli_fetch_array($hasil)) {
      $no++;
      echo '
      <tr>
        <td>'.$no.'</td>
        <td align="center">'.$row['kdsubpos'].'</td>   
        <td>'.$row['namasubpos'].'</td> 
        <td>       
        <a href="editsubpos3.php?subpos='.base64_encode($row['kdsubpos']).'">edit</a>&nbsp;&nbsp;&nbsp;
        <a href="?pos='.base64_encode(($row['kdindukpos'])).'&del='.base64_encode($row['kdsubpos']).'">hapus</a>      
        </td>                  
      </tr>';
    }  
  }
  
  ?>
</table>

<a href="showdetailpos2.php?pos=<?=base64_encode(substr($kdindukpos,0,4));?>">Kembali ke daftar pos</a>