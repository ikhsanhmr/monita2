<?php                 
	require_once "../config/koneksi.php";
	
	$sql="SELECT * FROM BIDANG";
	$hasil=mysql_query($sql);  
	echo "<select>";
	while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
		echo "<option value='".$row['id']."-".$row['namaunit']."'>".$row['namaunit']."</option>";
	}
	echo "</select>";
	mysql_free_result($hasil);
	mysql_close($kon);	
?>