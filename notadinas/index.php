<?php error_reporting (E_ALL ^ E_NOTICE); ?>
<?php

	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}

    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$adm=$_SESSION['adm'];
	
    if(isset($_GET['del']))
    {
      $notadinas=base64_decode($_GET['del']);
      $sql="delete 
            from notadinas
            where nomornota='$notadinas'
            ";
      $hasil=mysql_query($sql);
      
      $sql="delete 
            from notadinas_detail
            where nomornota='$notadinas'
            ";
      $hasil=mysql_query($sql);
      
	  $sql="delete 
            from skkoterbit
            where nomornota='$notadinas'
            ";
      $hasil=mysql_query($sql);
    }    

?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h2>Rekomendasi SKKO/I</h2>
<a href="tambahrekomendasiskk.php">(+) Tambah Rekomendasi SKKO/ I</a><br />
<table>
  <tr>
    <th>No</th>
    <th>No Nota</th>
    <th>Tanggal Nota Dinas</th>
    <th>User</th>
    <th>Perihal</th>
    <th>Jenis</th>
    <th>Nilai Usulan</th>
    <th>Pembuat SKKO</th>
    <th>Progress</th>
    <th>No SKKO/I</th>
    <th>Aksi</th>
  </tr>
<?php
  $sql="SELECT n.*, nama namadisposisi, kdunit unitdisposisi, info infonotadinas
	FROM notadinas n
	LEFT JOIN USER u ON n.nip = u.nip
	LEFT JOIN progress p ON n.progress = p.pid " .
	 ($adm>=2? "": " where n.nipuser='$nip'") .
	" order by tanggal DESC";
//	where coalesce(progress,0)<=1 " . ($nip=="admin"? "": " and n.nipuser='$nip'") .
	//echo $sql;

	$hasil=mysql_query($sql) or die (mysql_error());
	//echo mysql_num_rows($hasil);
	
	while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
    $no++;
    echo "
    <tr>
		<td>$no</td>
		<td>$row[nomornota]</td>
		<td>$row[tanggal]</td>
		<td>$row[nipuser]</td>
		<td>$row[perihal]</td>
		<td>$row[skkoi]</td>
		<td>".number_format($row["nilaiusulan"])."</td>
		<td>$row[namadisposisi]</td>
		<td>$row[infonotadinas]</td>
		<td>$row[noskkoi]</td>
		<td>" . (intval($row["progress"])>1? "":  
			"<a href='editrekomendasiskk.php?notadinas=".base64_encode($row["nomornota"])."'>Edit</a>
			&nbsp;&nbsp;&nbsp;<a href='?del=".base64_encode($row["nomornota"])."'>Hapus</a>") .
		"</td>
	</tr>";
	}  
?>
</table>
<a href="" onclick="parent.isi.print()">Cetak</a>   
