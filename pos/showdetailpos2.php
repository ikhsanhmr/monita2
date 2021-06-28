<?php
session_start();
require_once '../config/koneksi.php';
$kdindukpos = base64_decode($_GET['pos']);
if (isset($_GET['del'])) {
  $kdsubpos2 = base64_decode($_GET['del']);


  $sql = "delete 
            from posinduk4
            where kdindukpos like '$kdsubpos2%'
            ";

  $hasil = mysqli_query($mysqli, $sql) or die('Unable to execute query. ' . mysqli_error($mysqli));

  $sql = "delete 
            from posinduk3
            where kdsubpos='$kdsubpos2'
            and kdindukpos='$kdindukpos'
            ";
  $hasil = mysqli_query($mysqli, $sql) or die('Unable to execute query. ' . mysqli_error($mysqli));
} else {
  //$kdindukpos=base64_decode($_GET['pos']);      
  $sql = "select namasubpos 
            from posinduk2
            where kdsubpos='$kdindukpos'";

  $hasil = mysqli_query($mysqli, $sql) or die('Unable to execute query. ' . mysqli_error($mysqli));
  $row = mysqli_fetch_array($hasil);
  $namapos = $row['namasubpos'];
}

?>

<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h5>
  DAFTAR SUB POS <?= $kdindukpos . ' ' . $namapos ?>
</h5>
<a href="tambahsubpos2.php?pos=<?= base64_encode($kdindukpos) ?>">(+) Tambah subpos</a><br />
<?php
/*
  $ses_userid=$_SESSION['p_userid'];
  $sqlsub="select subpos2 from `users-akses-pos` where subpos1='$subpos' and iduser='$ses_userid'";
  $rssub=db_select($sqlsub);
  //echo "asda".$rssub[0]['subpos2']."asd".$sqlsub;
  if ($rssub[0]['subpos2']=='' or $_SESSION['p_akses']=='0')
  {
  $sql="select kdsubpos, namasubpos
        from posanggota2
        where kdindukpos='$subpos'
        ";
  }
  else
  {
	  $sql="select kdsubpos, namasubpos
        from posanggota2
        where kdindukpos='$subpos' and kdsubpos in (select subpos2 from `users-akses-pos` where subpos1='$subpos' and iduser='$ses_userid')
        ";
  }
  $rs=db_select($sql);
*/
$sql = "select kdindukpos,kdsubpos, namasubpos
        from posinduk3
        where kdindukpos='$kdindukpos'
        ";

$no = 0;
$hasil = mysqli_query($mysqli, $sql) or die('Unable to execute query. ' . mysqli_error($mysqli));

$rs = mysqli_num_rows($hasil);
if (($rs) == 0) {  // Sebelumnya ini => (count($rs) == 0)
  echo "Data tidak ditemukan.<br>";
} else {
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
        <td>' . $no . '</td>
        <td align="center">' . $row['kdsubpos'] . '</td>   
        <td>' . $row['namasubpos'] . '</td> 
        <td>
        <a href="showdetailpos3.php?pos=' . base64_encode($row['kdsubpos']) . '">lihat sub</a>&nbsp;&nbsp;&nbsp;          
        <a href="editsubpos2.php?pos=' . base64_encode($row['kdsubpos']) . '">edit</a>&nbsp;&nbsp;&nbsp;
        <a href="?pos=' . base64_encode(($row['kdindukpos'])) . '&del=' . base64_encode($row['kdsubpos']) . '">hapus</a>      
        </td>                  
      </tr>';
  }
}

?>
</table>

<a href="showdetailpos1.php?pos=<?= base64_encode(substr($kdindukpos, 0, 2)); ?>">Kembali ke daftar pos</a>