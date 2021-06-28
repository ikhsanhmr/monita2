<?php                 
	require_once "../config/koneksi.php";
	
	$sql="SELECT * FROM BIDANG";
	$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));  
	echo "<select>";
	while ($row = mysqli_fetch_array($hasil)) {
		echo "<option value='".$row['id']."-".$row['namaunit']."'>".$row['namaunit']."</option>";
	}
	echo "</select>";
	mysqli_free_result($hasil);
	$mysqli->close();($kon);	
?>