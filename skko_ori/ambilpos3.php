<?php
require_once '../config/koneksi.php'; #must be including for connection database
$posinduk3 = explode("-",$_GET['posinduk3']);
$kdpos3 = $posinduk3[0];
$namapos3 = $posinduk3[1];
$posinduk4 = mysqli_query("SELECT * FROM posinduk4 WHERE kdindukpos='$kdpos3' ORDER BY kdindukpos");
echo "<option value=''>-- Pilih Pos induk3 --</option>";
while($row=mysqli_fetch_array($posinduk4)){
            echo "<option value='".$row['kdsubpos']."'>".$row['kdsubpos']."-".$row['namasubpos']."</option>";
}

?>
?>
