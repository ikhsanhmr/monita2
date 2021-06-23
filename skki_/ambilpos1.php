<?php
require_once '../config/koneksi.php'; #must be including for connection database
$posinduk = explode("-",$_GET['posinduk']);
$kdpos = $posinduk[0];
$namapos = $posinduk[1];
$posinduk1 = mysql_query("SELECT * FROM posinduk2 WHERE kdindukpos='$kdpos' ORDER BY kdindukpos");
echo "<option value=''>-- Pilih Pos induk1 --</option>";
while($row=mysql_fetch_array($posinduk1)){
      echo "<option value='".$row['kdsubpos']."'>".$row['kdsubpos']."-".$row['namasubpos']."</option>";
}

?>

