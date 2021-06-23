<?php
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('index.php', '_parent')</script>";
		exit;
	}
	
	if($_SESSION['roleid']<=3) {
		require_once "config/control.inc.php";
		$link = mysql_connect($srv, $usr, $pwd);
		
		if (!$link) {
			die('Could not connect: ' . mysql_error());
		}
		mysql_select_db($db);
	
		$sql = "SELECT COUNT(*) jumlah FROM notadinas WHERE " . ($_SESSION["roleid"]==1? "coalesce(progress,0) = 0": "nip = '$_SESSION[nip]' AND coalesce(progress,0) = 2");
		//echo "$sql<br>";
		$result = mysql_query($sql) or die (mysql_error());
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$nd = $row["jumlah"];
		}
		mysql_free_result($result);
		
		$sql = "SELECT COUNT(*) jumlah FROM kontrak k INNER JOIN skkiterbit i ON k.nomorskkoi = i.nomorskki WHERE SIGNED IS NULL";
		//echo "$sql<br>";
		$result = mysql_query($sql) or die (mysql_error());
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$kk = $row["jumlah"];
		}
		mysql_free_result($result);
		mysql_close($link);	
		
		echo "Halo $_SESSION[nama],<br><br>";
		echo "Terdapat : <br>";
		echo "- $nd Nota Dinas Baru<br>";
		echo "- $kk Kontrak baru / kontrak yang belum SIGNED<br>";
	}
?>