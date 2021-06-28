<?php
require_once '../config/koneksi.php'; #must be including for connection database
$posinduk2 = explode("-",$_GET['posinduk2']);
$kdpos2 = $posinduk2[0];
$namapos2 = $posinduk2[1];
$posinduk3 = mysqli_query("SELECT * FROM posinduk3 WHERE kdindukpos='$kdpos2' ORDER BY kdindukpos");
echo "<option value=''>-- Pilih Pos induk1 --</option>";
while($row=mysqli_fetch_array($posinduk3)){
            echo "<option value='".$row['kdsubpos']."'>".$row['kdsubpos']."-".$row['namasubpos']."</option>";
}

?>
?>
